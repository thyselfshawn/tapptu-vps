<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization as needed
    }

    public function rules()
    {
        return [
            'token' => [
                'required',
                'string',
                'max:255',
                'unique:cards,token,' . $this->card->id, // Ensure this works for updates
            ],
            'name' => 'nullable|string|max:255',
            'status' => 'required|string|in:pending,active,inactive', // Validate status values
        ];
    }
}