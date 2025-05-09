@extends('layouts.app')

@section('title')
    <title>申請内容の確認</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_detail.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h4 class="fw-bold border-start border-4 ps-3">勤怠詳細</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4 pb-0">

            <form action="/attendance/modify" id="attendance-modify" method="post">
                @csrf
                {{-- <input type="hidden" name="attendance_id" value="{{$attendance->id}}"> --}}

                <div class="row form-section">
                    <label class="col-sm-2 col-form-label fw-bold">名前</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext fw-bold">{{$attendanceCorrection->user->name}}</p>
                    </div>
                </div>

                <div class="row form-section">
                    <label class="col-sm-2 col-form-label fw-bold">日付</label>
                    <div class="col-sm-5">
                        <p class="form-control-plaintext fw-bold">{{$attendanceCorrection->clock_in->format('Y')}}年</p>
                    </div>
                    <div class="col-sm-5">
                        <p class="form-control-plaintext fw-bold">{{$attendanceCorrection->clock_in->format('n月j日')}}</p>
                    </div>
                </div>

                <div class="row form-section">
                    <label class="col-sm-2 col-form-label fw-bold">出勤・退勤</label>
                    <div class="col-sm-4">
                        <p class="form-control fw-bold attendanceCorrection">{{$attendanceCorrection->clock_in->format('H:i')}}</p>
                    </div>
                    <div class="col-sm-1 text-center">〜</div>
                    <div class="col-sm-4">
                        <p class="form-control fw-bold attendanceCorrection">{{$attendanceCorrection->clock_out->format('H:i')}}</p>
                    </div>
                </div>
                @foreach ($attendanceCorrection->breakCorrections as $index => $breakTime)
                    <div class="row form-section">
                        <label class="col-sm-2 col-form-label fw-bold">休憩</label>
                        <div class="col-sm-4">
                            <p class="form-control fw-bold attendanceCorrection">{{$breakTime->break_start->format('H:i')}}</p>
                        </div>
                        <div class="col-sm-1 text-center">〜</div>
                        <div class="col-sm-4">
                            <p class="form-control fw-bold attendanceCorrection">{{$breakTime->break_end->format('H:i')}}</p>
                        </div>
                        <input type="hidden" name="breakTime_id[]" value="{{$breakTime->id}}">
                    </div>
                @endforeach

                <div class="row form-section align-items-start mb-0">
                    <label class="col-sm-2 col-form-label fw-bold">備考</label>
                    <div class="col-sm-9">
                        <p class="form-control fw-bold attendanceCorrection">{{$attendanceCorrection->reason}}</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="text-danger fw-bold text-end mt-4">
        <button>承認</button>
    </div>
</div>
@endsection