<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardVenue;
use App\Models\Venue;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\VenuesDataTable;
use Illuminate\Support\Facades\Storage;
use App\Enums\VenueStatusEnum;
use App\Enums\CardStatusEnum;
use Illuminate\Validation\Rule;

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
    public function store(Request $request)
    {
        if(auth()->user()->role == 'admin'){
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'name' => 'nullable|string|max:255',
                'logo' => 'nullable|string',
                'voucher' => 'nullable',
                'googlereviewstart' => 'nullable',
                'googleplaceid' => 'nullable',
                'notification' => 'boolean',
                'status' => ['nullable', Rule::in(CardStatusEnum::values())],
            ]);
            if (!empty($request->logo)) {
                // Store the avatar and get the relative path
                $validatedData['logo'] = $this->storeBase64($request->logo);
            } 
            Venue::create($validatedData);
        }else{
            $validatedData = $request->validate([
                'card' => 'nullable|exists:cards,uuid',
                'name' => 'required|string|max:255',
                'logo' => 'nullable|string',
                'voucher' => 'nullable',
                'googlereviewstart' => 'nullable',
                'googleplaceid' => 'nullable',
                'notification' => 'boolean',
            ]);
            if (!empty($request->logo)) {
                // Store the avatar and get the relative path
                $validatedData['logo'] = $this->storeBase64($request->logo);
                $venue->update(['logo' => $validatedData['logo']]);
            }
            $venue = Venue::create([
                'user_id' => auth()->user()->id,
                'name' => $validatedData['name'],
                'voucher' => $validatedData['voucher'],
                'googleplaceid' => $validatedData['googleplaceid'],
                'googlereviewstart' => $validatedData['googlereviewstart'],
            ]);
            if($venue && !empty($validatedData['card'])){
                $card = Card::where('uuid', $validatedData['card'])->first();
                if($card){
                    CardVenue::create(['card_id' => $card->id, 'venue_id' => $venue->id]);
                    $card->update(['status' => CardStatusEnum::attached]);
                    $this->activate_trial($validatedData, $venue);
                }else{
                    return redirect()->back()->with('error', 'Invalid Card token');
                }
            }
        }
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

        if(auth()->user()->role == 'admin'){
            $users = User::where('id','!=',1)->get();
            return view('venues.edit', compact('venue','users'));
        } else if(auth()->user()->id == $venue->user_id){
            return view('venues.edit', compact('venue'));
        }
        abort(404,'You are not allowed!');
    }

    // Update the specified resource in storage.
    public function update(Request $request, Venue $venue)
    {
        if(auth()->user()->role == 'admin'){
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'name' => 'nullable|string|max:255',
                'voucher' => 'nullable',
                'googlereviewstart' => 'nullable',
                'googleplaceid' => 'nullable',
                'notification' => 'boolean',
                'status' => ['nullable', Rule::in(CardStatusEnum::values())],
                
            ]);
            // check for logo
            if (!empty($request->logo)) {
                // If the user has an old avatar, delete it
                if ($venue->logo && $venue->logo != 'logo.png') {
                    Storage::disk($this->discPath)->delete($this->imagePath . $venue->logo);
                }
                $validatedData['logo'] = $this->storeBase64($request->logo);
            }
            $venue->update($validatedData);
            $this->activate_trial($validatedData, $venue);
            return redirect()->route('venues.index')->with('success', 'Venue updated successfully.');
        }else if(auth()->user()->id == $venue->user_id){
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'voucher' => 'nullable',
                'googlereviewstart' => 'nullable',
                'googleplaceid' => 'nullable',
                'notification' => 'boolean',
            ]);

            // check for logo
            if (!empty($request->logo)) {
                // If the user has an old avatar, delete it
                if ($venue->logo && $venue->logo != 'logo.png') {
                    Storage::disk($this->discPath)->delete($this->imagePath . $venue->logo);
                }
                $validatedData['logo'] = $this->storeBase64($request->logo);
            }
            $venue->update($validatedData);
            // is all items are set make rfid card online
            $this->activate_trial($validatedData, $venue);
            return redirect()->route('venues.index')->with('success', 'Venue updated successfully.');
        }
        abort(403, 'Unauthorized acess!');
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
        if($card && $card->status == CardStatusEnum::pending){
            $venue = Venue::findOrFail($id);
            if($venue){
                CardVenue::create(['card_id' => $card->id, 'venue_id' => $venue->id]);
                $card->update(['status' => CardStatusEnum::attached]);
                // Clear specific session data
                session()->forget('stup-card');
                return redirect()->route('venues.show', $venue->id)->with('success', 'Card attached!');
            }
        }
        return redirect()->back()->with('error', 'Card not found!');
    }

    public function create_with_card()
    {
        dd(session('setup-card'));
    }
    private function activate_trial($validatedData, $venue)
    {
        $allItemsSet = array_filter($validatedData, function ($value) {
            return !is_null($value) && (is_string($value) ? trim($value) !== '' : true);
        });

        $allItemsSet = count($allItemsSet) === count($validatedData);

        if ($allItemsSet && $venue->status === VenueStatusEnum::pending) {
            $venue->update(['status' => VenueStatusEnum::trial]);
            return $allItemsSet;
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
        list(, $imageBase64)      = explode(',', $imageBase64);
        $imageBase64 = base64_decode($imageBase64);
        $imageName = time() . '.png';
        
        // Store the image in the storage (e.g., private disk)
        Storage::disk($this->discPath)->put($this->imagePath . $imageName, $imageBase64);

        return $imageName;
    }
}