@extends('layouts.app')

@section('title')
    <title>勤怠詳細</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
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
                    <input type="hidden" name="attendance_id" value="{{$attendance->id}}">

                    <div class="row form-section">
                        <label class="col-sm-2 col-form-label fw-bold">名前</label>
                        <div class="col-sm-10">
                            <p class="form-control-plaintext fw-bold">{{$user->name}}</p>
                        </div>
                    </div>

                    <div class="row form-section">
                        <label class="col-sm-2 col-form-label fw-bold">日付</label>
                        <div class="col-sm-5">
                            <p class="form-control-plaintext fw-bold">{{$attendance->clock_in->format('Y')}}年</p>
                        </div>
                        <div class="col-sm-5">
                            <p class="form-control-plaintext fw-bold">{{$attendance->clock_in->format('n月j日')}}</p>
                        </div>
                    </div>

                    <div class="row form-section">
                        <label class="col-sm-2 col-form-label fw-bold">出勤・退勤</label>
                        <div class="col-sm-4">
                            @if (optional($attendanceCorrection)->status == 1)
                                <p class="form-control fw-bold attendanceCorrection">{{$attendanceCorrection->clock_in->format('H:i')}}</p>
                            @else
                                <input type="time" name="clock_in" class="form-control fw-bold" value="{{$attendance->clock_in->format('H:i')}}">
                                @error('clock_in') <div class="text-danger">{{$message}}</div> @enderror
                            @endif
                        </div>
                        <div class="col-sm-1 text-center">〜</div>
                        <div class="col-sm-4">
                            @if (optional($attendanceCorrection)->status == 1)
                                <p class="form-control fw-bold attendanceCorrection">{{$attendanceCorrection->clock_out->format('H:i')}}</p>
                            @else
                                <input type="time" name="clock_out" class="form-control fw-bold" value="{{$attendance->clock_out->format('H:i')}}">
                                @error('clock_out') <div class="text-danger">{{$message}}</div> @enderror
                            @endif
                        </div>
                    </div>

                    @if (optional($attendanceCorrection)->status == 1)
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
                    @else
                        @foreach ($breakTimes as $index => $breakTime)
                            <div class="row form-section">
                                <label class="col-sm-2 col-form-label fw-bold">休憩</label>
                                <div class="col-sm-4">
                                    <input type="time" name="break_start[]" class="form-control fw-bold" value="{{$breakTime->break_start->format('H:i')}}">
                                    @error('break_start.' . $index)
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-1 text-center">〜</div>
                                <div class="col-sm-4">
                                    <input type="time" name="break_end[]" class="form-control fw-bold" value="{{$breakTime->break_end->format('H:i')}}">
                                    @error('break_end.' . $index)
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <input type="hidden" name="breakTime_id[]" value="{{$breakTime->id}}">
                            </div>
                        @endforeach
                    @endif

                    <div class="row form-section align-items-start mb-0">
                        <label class="col-sm-2 col-form-label fw-bold">備考</label>
                        <div class="col-sm-9">
                            @if (optional($attendanceCorrection)->status == 1)
                                <p class="form-control fw-bold attendanceCorrection">{{$attendanceCorrection->reason}}</p>
                            @else
                                <textarea name="reason" class="form-control fw-bold" placeholder="申請の理由を記載して下さい"></textarea>
                                @error('reason') <div class="text-danger">{{$message}}</div> @enderror
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (optional($attendanceCorrection)->status == 1)
            <div class="text-danger fw-bold text-end mt-4">
                ※承認待ちのため修正はできません
            </div>
        @else
            <div class="btn-container">
                <button form="attendance-modify" type="submit" class="btn btn-dark px-4">修正</button>
            </div>
        @endif
    </div>
@endsection