<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'exists:users,email',
            ],
            'password' => ['required', 'min:8'],
        ];
    }

    public function messages(){
        return [
            'email.required' =>'メールアドレスを入力して下さい',
            'email.exists' => 'ログイン情報が登録されていません',
            'password.required' =>'パスワードを入力して下さい',
            'password.min8' =>'パスワードは8桁以上で入力してください'
        ];
    }
}