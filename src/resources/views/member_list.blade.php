@extends('layouts.app')

@section('title')
    <title>従業員の勤怠状況</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/member_list.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container mt-5">
    <div class="mb-4">
        <h4 class="fw-bold border-start border-4 ps-3">{{$date->format('Y年m月d日')}}の勤怠</h4>
    </div>

    @php
        $prevDay = $date->copy()->subDay()->format('Y-m-d');
        $nextDay = $date->copy()->addDay()->format('Y-m-d');
    @endphp


    <div class="d-flex justify-content-between align-items-center mb-4 rounded-3 bg-white py-2">
        <a class="btn me-3" href="{{route('member.list',['day' => $prevDay])}}">
            <i class="bi bi-arrow-left"></i> 前日
        </a>
        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>{{$date->format('Y/m/d')}}</h5>
        <a class="btn ms-3" href="{{route('member.list',['day' => $nextDay])}}">
            翌日 <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive rounded-3">
        <table class="table text-center align-middle">
            <thead class="text-secondary">
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        @php
                            $attendance = $user->attendances->first(); // その日の勤怠が1件ある前提
                        @endphp

                        @if ($attendance)
                            <td>{{ $attendance->clock_in->format('H:i') }}</td>
                            <td>{{ $attendance->clock_out->format('H:i') }}</td>
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
                        @else
                            <td colspan="5">出勤記録なし</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection