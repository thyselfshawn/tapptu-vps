<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\VenueStatusEnum;
use Illuminate\Validation\Rule;

class VenueCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow both admin and non-admin users
    }

    public function rules()
    {
        $isAdmin = auth()->user()->role == 'admin';

        $rules = [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'voucher' => 'nullable',
            'googlereviewstart' => 'nullable',
            'googleplaceid' => 'nullable',
            'notification' => 'boolean',
            'user_id' => 'required|exists:users,id',
        ];

        if ($isAdmin) {
            $rules['status'] = ['nullable', Rule::in(VenueStatusEnum::values())];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $isAdmin = auth()->user()->role == 'admin';

        if (!$isAdmin || !$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->user()->id,
                'status' => 'pending',
            ]);
        }
    }
}
