<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class WebUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'email' => 'required|max:200|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|min:6|max:191',
            'dob' => 'required|date_format:Y-m-d',
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'state_id' => 'required|exists:states,id,deleted_at,NULL',
            'city_id' => 'required|exists:cities,id,deleted_at,NULL',
            'gender' => 'required|in:F,M',
            'status' => 'required|in:Y,N',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Add custom validation messages here
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
