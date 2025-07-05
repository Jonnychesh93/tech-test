<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileUploadRequest extends FormRequest {
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'file' => ['required', 'file', 'mimes:csv', 'max:2048'], // I added 2MB max size for now, Will depend on Server Setup.
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded file must be a valid file.',
            'file.mimes' => 'The file must be a CSV file.',
            'file.max' => 'The file size must not exceed 2MB.', // Again can be increased in the future.
        ];
    }
}
