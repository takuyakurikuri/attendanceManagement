<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionRequest extends FormRequest
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
        $rules =  [
            // 'reason' =>[
            //     'required',
            // ],
            'clock_in' =>[
                'required',
            ],
            'clock_out' =>[
                'required',
                'after:clock_in',
            ],
            'break_start' =>[
                'required',
                'array'
            ],
            'break_start.*' =>[
                'required',
                'date_format:H:i'
            ],
            'break_end' =>[
                'required',
                'array'
            ],
            'break_end.*' =>[
                'required',
                'date_format:H:i'
            ],
        ];
        
        if(!Auth::guard('admin')->check()){
            $rules['reason'] = ['required'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $breakStarts = $this->input('break_start', []);
            $breakEnds = $this->input('break_end', []);

            foreach ($breakStarts as $index => $start) {
                $end = $breakEnds[$index] ?? null;

                if (!$start || !$end) {
                    continue;
                }

                if ($start >= $end) {
                    $validator->errors()->add("break_end.$index", '出勤時間もしくは退勤時間が不適切な値です');
                }

                $clockIn = $this->input('clock_in');
                $clockOut = $this->input('clock_out');

                if ($start < $clockIn) {
                    $validator->errors()->add("break_start.$index", '出勤時間もしくは退勤時間が不適切な値です');
                }

                if ($end > $clockOut) {
                    $validator->errors()->add("break_end.$index", '出勤時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }

    public function messages(){
        return [
            'reason.required' =>'備考欄を入力して下さい',
            'clock_in.required' => '出勤時間を入力して下さい',
            'clock_out.required' => '退勤時間を入力して下さい',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start.required' => '休憩開始時間を入力して下さい',
            'break_end.required' => '休憩終了時間を入力して下さい',
        ];
    }
}