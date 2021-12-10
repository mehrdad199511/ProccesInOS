<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:55',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:5',
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error[0];
        }

        throw new HttpResponseException(response()->json([
            'message' => $messages
        ], 422, [], JSON_UNESCAPED_UNICODE)
        );
    }
}
