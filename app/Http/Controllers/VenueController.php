<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardVenue;
use App\Models\Venue;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\DataTables\VenuesDataTable;
use Illuminate\Support\Facades\Storage;
use App\Enums\VenueStatusEnum;
use App\Enums\CardStatusEnum;
use App\Http\Requests\VenueCreateRequest;
use App\Http\Requests\VenueUpdateRequest;
use Carbon\Carbon;

class VenueController extends Controller
{
    // image directory in private storage
    protected $imagePath = 'images/';
    protected $discPath = 'public';

    public function __construct()
    {
        // Only admins can access
        $this->middleware('admin')->only(['destroy']);
    }
    // Display a listing of the resource.
    public function index(VenuesDataTable $dataTable)
    {
        return $dataTable->render('venues.index');
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('venues.create', [
            'users' => auth()->user()->role == 'admin' ? User::all() : '',
        ]);
    }

    // Store a newly created resource in storage.
    public function store(VenueCreateRequest $request)
    {
        $validatedData = $request->validated();

        if (!empty($request->logo)) {
            $validatedData['logo'] = $this->storeBase64($request->logo);
        }

        $venue = Venue::create($validatedData);
        $venue = Venue::findOrFail($venue->id);
        $this->attachCard($venue);
        $this->activate_trial($venue);
        return redirect()->route('venues.index')->with('success', 'Venue created successfully.');
    }

    // Display the specified resource.
    public function show(Venue $venue)
    {
        $packages = Package::where('status', true)->get();
        return view('venues.show', compact('venue', 'packages'));
    }

    // Show the form for editing the specified resource.
    public function edit(Venue $venue)
    {

        if (auth()->user()->role == 'admin') {
            $users = User::where('id', '!=', 1)->get();
            return view('venues.edit', compact('venue', 'users'));
        } else if (auth()->user()->id == $venue->user_id) {
            return view('venues.edit', compact('venue'));
        }
        abort(404, 'You are not allowed!');
    }

    // Update the specified resource in storage.
    public function update(VenueUpdateRequest $request, Venue $venue)
    {
        $validatedData = $request->validated();
        if (!empty($request->logo)) {
            $validatedData['logo'] = $this->updateLogo($venue, $request->logo);
        }

        $venue->update(array_filter($validatedData, function ($value) {
            return $value !== null;
        }));

        $this->activate_trial($venue);
        return redirect()->route('venues.index')->with('success', 'Venue updated successfully.');
    }

    private function updateLogo(Venue $venue, $newLogo)
    {
        if ($venue->logo && $venue->logo != 'logo.png') {
            Storage::disk($this->discPath)->delete($this->imagePath . $venue->logo);
        }
        return $this->storeBase64($newLogo);
    }

    // Remove the specified resource from storage.
    public function destroy(Venue $venue)
    {
        if ($venue->logo && $venue->logo != 'logo.png') {
            Storage::disk($this->discPath)->delete($this->imagePath . $venue->logo);
        }
        $venue->delete();
        return redirect()->route('venues.index')->with('success', 'Venue deleted successfully.');
    }

    public function attach_card($id, $card)
    {
        $card = Card::where('uuid', $card)->first();
        if ($card && $card->status == CardStatusEnum::pending) {
            $venue = Venue::findOrFail($id);
            if ($venue) {
                CardVenue::create(['card_id' => $card->id, 'venue_id' => $venue->id]);
                $card->update(['status' => CardStatusEnum::attached->value]);
                // Clear specific session data
                session()->forget('setup-card');
                return redirect()->route('venues.show', $venue->slug)->with('success', 'Card attached!');
            }
        }
        return redirect()->back()->with('error', 'Card not found!');
    }

    private function activate_trial($venue)
    {
        $requiredFields = ['name', 'slug', 'logo', 'voucher', 'googleplaceid'];

        $allFieldsSet = collect($requiredFields)->every(function ($field) use ($venue) {
            $value = $venue->$field;
            return !is_null($value) && (is_string($value) ? trim($value) !== '' : true);
        });
        if ($allFieldsSet && $venue->status === VenueStatusEnum::pending->value) {
            $venue->update(['status' => VenueStatusEnum::trial->value]);
            if ($venue->currentSubscription()) {
                $venue->currentSubscription()->update([
                    'end_at' => Carbon::now()->addMonth(),
                    'status' => 1
                ]);
            } else {
                $package = Package::where('name', operator: 'premium')->where('type', 'month')->first();
                Subscription::create([
                    'venue_id' => $venue->id,
                    'package_id' => $package->id,
                    'status' => true
                ]);
            }
            return true;
        }
        return false;
    }
    public function image($filename)
    {
        $path = $this->imagePath . $filename;

        // Check if the file exists
        if (!Storage::disk($this->discPath)->exists($path)) {
            abort(404);
        }

        // Get the file contents
        $file = Storage::disk($this->discPath)->get($path);
        $mimeType = Storage::disk($this->discPath)->mimeType($path);

        // Return the file as a response
        return response($file, 200)
            ->header('Content-Type', $mimeType);
    }

    private function storeBase64($imageBase64)
    {
        list($type, $imageBase64) = explode(';', $imageBase64);
        list(, $imageBase64) = explode(',', $imageBase64);
        $imageBase64 = base64_decode($imageBase64);
        $imageName = time() . '.png';

        // Store the image in the storage (e.g., private disk)
        Storage::disk($this->discPath)->put($this->imagePath . $imageName, $imageBase64);

        return $imageName;
    }

    private function attachCard($venue)
    {
        if (!empty(session('setup-card'))) {
            $card = Card::where('uuid', session('setup-card'))->first();
            if ($card) {
                CardVenue::create(['card_id' => $card->id, 'venue_id' => $venue->id]);
                $card->update(['status' => CardStatusEnum::attached->value]);
                return true;
            }
        }
        return false;
    }
}

