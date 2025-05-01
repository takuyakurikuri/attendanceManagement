@extends('layouts.app')

@section('title')
    <title>勤怠画面</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/member_list.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container mt-5">
    <div class="mb-4">
        <h4 class="fw-bold border-start border-4 ps-3">2025年4月26日の勤怠</h4>
    </div>

    {{-- @php
        $prevMonth = $date->copy()->subMonth()->format('Y-m');
        $nextMonth = $date->copy()->addMonth()->format('Y-m');
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4 rounded-3 bg-white py-2">
        <a class="btn me-3" href="{{route('attendance.list',['month' => $prevMonth])}}">
            <i class="bi bi-arrow-left"></i> 前月
        </a>
        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>{{ $date->format('Y/m') }}</h5>
        <a class="btn ms-3" href="{{route('attendance.list',['month' => $nextMonth])}}">
            翌月 <i class="bi bi-arrow-right"></i>
        </a>
    </div> --}}

    <div class="d-flex justify-content-between align-items-center mb-4 rounded-3 bg-white py-2">
        <a class="btn me-3" href="">
            <i class="bi bi-arrow-left"></i> 前日
        </a>
        <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>2025/04/26</h5>
        <a class="btn ms-3" href="">
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
            </tbody>
        </table>
    </div>
</div>
@endsection