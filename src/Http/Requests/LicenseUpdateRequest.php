<?php

namespace LaravelReady\LicenseServer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LicenseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'domain' => 'nullable|string|max:255',
            'user_id' => 'nullable|numeric|exists:users,id',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'expiration_days' => 'nullable|numeric',
            'is_trial' => 'nullable|boolean',
            'is_lifetime' => 'nullable|boolean',
        ];
    }
}
