<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeMessageRequest extends FormRequest
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
            'conversation_id' => ['required'],
            'sender_id' => ['required'],
            'message' => ['required']
        ];
    }
}
