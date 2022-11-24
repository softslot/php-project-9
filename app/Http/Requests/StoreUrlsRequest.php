<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url.name' => 'required|max:255|url',
        ];
    }

    public function messages(): array
    {
        return [
            'url.name.*' => 'Некорректный URL',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors();

        return response()
            ->view('index', compact('errors'))
            ->setStatusCode(422);
    }
}
