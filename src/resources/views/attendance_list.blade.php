@extends('layouts.app')

@section('title')
    <title>勤怠画面</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container mt-4">
    <h2>勤怠一覧</h2>
    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
        <h2 class="btn">前月</h2>
        <h2 class="m-0">{{$today->format('Y/m')}}</h2>
        <h2 class="btn">翌月</>
    </div>

    <table class="table text-center">
        <thead class="">
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            <tr>
                <td>{{ $attendance->clock_in->format('m/d/') }}({{$week[$attendance->clock_in->format('w')]}})</td>
                <td>{{ $attendance->clock_in->format('H:i') }}</td>
                <td>{{ optional($attendance->clock_out)->format('H:i') ?? '-' }}</td>
                <td>
                    @php
                        $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                            return $break->break_end && $break->break_start
                                ? $break->break_start->diffInMinutes($break->break_end)
                                : 0;
                        });

                        $breakHours = floor($totalBreakMinutes / 60);
                        $breakMinutes = str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT);
                    @endphp
                    {{ $breakHours }}:{{ $breakMinutes }}
                </td>

                <td>
                    @php
                        $workDuration = 0;
                        if ($attendance->clock_in && $attendance->clock_out) {
                            $workDuration = $attendance->clock_in->diffInMinutes($attendance->clock_out) - $totalBreakMinutes;
                            $workDuration = max(0, $workDuration);
                        }

                        $workHours = floor($workDuration / 60);
                        $workMinutes = str_pad($workDuration % 60, 2, '0', STR_PAD_LEFT);
                    @endphp
                    {{ $workHours }}:{{ $workMinutes }}
                </td>
                <td><a class="detail" href="#">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection