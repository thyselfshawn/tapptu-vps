<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as needed for authorization
    }

    public function rules()
    {
        $user= $this->route('user'); // Get the user ID from the route
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'nullable|string|max:50',
            'avatar' => 'nullable|string',
        ];
    }
}
