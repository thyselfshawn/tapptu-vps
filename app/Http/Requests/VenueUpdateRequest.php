<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\CardStatusEnum;
use Illuminate\Validation\Rule;

class VenueUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We'll handle specific authorization in the rules
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isAdmin = auth()->user()->role == 'admin';

        $rules = [
            'name' => 'required|string|max:255',
            'voucher' => 'nullable',
            'googlereviewstart' => 'nullable',
            'googleplaceid' => 'nullable',
            'notification' => 'boolean',
            'logo' => 'nullable|string', // Assuming logo is handled as base64 string
        ];

        if ($isAdmin) {
            $rules = array_merge($rules, [
                'user_id' => 'required|exists:users,id',
                'status' => ['nullable', Rule::in(CardStatusEnum::values())],
            ]);
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $isAdmin = auth()->user()->role == 'admin';

        if (!$isAdmin) {
            $this->merge([
                'user_id' => auth()->user()->id,
            ]);
        }
    }
}
