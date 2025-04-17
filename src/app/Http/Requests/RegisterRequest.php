<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => ['required', 'min:8','confirmed'],
        ];
    }

    public function messages(){
        return [
            'name.required' =>'お名前を入力して下さい',
            'email.required' =>'メールアドレスを入力して下さい',
            'email.unique' =>'このメールアドレスは既に使われています',
            'password.required' =>'パスワードを入力して下さい',
            'password.min' =>'パスワードを8桁以上で入力して下さい',
            'password.confirmed' => 'パスワードと一致しません'
        ];
    }
}