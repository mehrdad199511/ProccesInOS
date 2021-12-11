<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateFileRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title' => 'required|min:2|max:20|regex:/^[0-9a-zA-Z]+$/',
            'extension' => 'required|string|in:txt,pdf,html,css,php,py,yml,yaml|regex:/^[a-zA-Z]+$/'
        ];
    }


    /**
     * return a custom message for rules
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.regex' => 'The file title format not valid. The title can only be number or string',
            'extension.regex' => 'The file extension format not valid.',
            'extension.in' => 'The extension format not valid. The extension can only be txt,pdf,html,css,php,py,yml,yaml'
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
