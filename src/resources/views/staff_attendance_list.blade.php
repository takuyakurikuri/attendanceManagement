@extends('layouts.app')

@section('title')
    <title>従業員別の勤怠実績</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff_attendance_list.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container mt-5">
    <div class="mb-4">
        <h4 class="fw-bold border-start border-4 ps-3">{{$user->name}}さんの勤怠</h4>
    </div>

    @php
        $prevMonth = $date->copy()->subMonth()->format('Y-m');
        $nextMonth = $date->copy()->addMonth()->format('Y-m');
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4 rounded-3 bg-white py-2">
        <a class="btn me-3" href="{{route('admin.attendance.list',['user_id'=>$user->id,'month' => $prevMonth])}}">
            <i class="bi bi-arrow-left"></i> 前月
        </a>
        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>{{ $date->format('Y/m') }}</h5>
        <a class="btn ms-3" href="{{route('admin.attendance.list',['user_id'=>$user->id,'month' => $nextMonth])}}">
            翌月 <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive rounded-3">
        <table class="table text-center align-middle">
            <thead class="text-secondary">
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
                    <td>{{ $attendance->clock_in->format('m/d') }}({{ $week[$attendance->clock_in->format('w')] }})</td>
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
                    <td><a href="/attendance/{{$attendance->id}}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <form action="/admin/attendance/staff/csv" method="post">
        @csrf
        <input type="hidden" name="user_id" value="{{$user->id}}">
        <input type="hidden" name="date" value="{{$date->format('Y-m')}}">
        <div class="btn-container">
            <button class="btn btn-dark" type="submit">CSV出力</button>
        </div>
    </form>
</div>
@endsection