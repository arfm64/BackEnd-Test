<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCompanyRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:32',
            'email' => 'required|email|min:8|unique:companies',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
            'address' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The company name field is mandatory.',
            'name.string' => 'The company name must be a string.',
            'name.max' => 'The company name must be at most 32 characters.',
            'email.required' => 'The company email field is mandatory.',
            'email.email' => 'Please provide a valid email address',
            'email.min' => 'The company email must be at most 8 characters.',
            'phone.required' => 'The company phone field is mandatory.',
            'phone.string' => 'The company phone must be a string.',
            'phone.max' => 'The company phone must be at most 15 characters.',
            'password.required' => 'The password field is mandatory.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 6 characters.',
            'address.required' => 'The company address field is mandatory.',
            'address.string' => 'The company address must be a string.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}