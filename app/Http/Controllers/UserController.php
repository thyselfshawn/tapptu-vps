<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DataTables\UsersDataTable;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // image directory in private storage
    protected $imagePath = 'images/';
    protected $discPath = 'private';

    public function __construct()
    {
        // Only admins can access
        $this->middleware('admin')->only(['create', 'store','delete', 'index']);
        
        // Admins and the venue themselves can access
        $this->middleware('venue')->only(['show', 'edit', 'update']);
    }
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }


    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);

        if (!empty($request->avatar)) {
            // Store the avatar and get the relative path
            $validatedData['avatar'] = $this->storeBase64($request->avatar);
        }

        User::create($validatedData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        if (!empty($request->avatar)) {
            // If the user has an old avatar, delete it
            if ($user->avatar && $user->avatar != 'avatar.png') {
                Storage::disk($this->discPath)->delete($this->imagePath . $user->avatar);
            }
            $validatedData['avatar'] = $this->storeBase64($request->avatar);
        }
        
        $user->update($validatedData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->avatar && $user->avatar != 'avatar.png') {
            Storage::disk($this->discPath)->delete($this->imagePath . $user->avatar);
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
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
