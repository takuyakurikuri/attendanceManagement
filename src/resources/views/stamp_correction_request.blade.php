@extends('layouts.app')

@section('title')
    <title>勤怠画面</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction_request.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')


<div class="container py-5">
    <div class="mb-4">
        <h4 class="fw-bold border-start border-4 ps-3">申請一覧</h4>
    </div>
    
    <ul class="nav nav-tabs nav-underline mb-3">
        <li class="nav-item nav-link">
            <form action="/stamp_correction_request/list" method="get" class="d-inline">
                <input type="hidden" name="tab">
                <button type="submit" class=" border-0 bg-transparent fw-bold">承認待ち</button>
            </form>
        </li>
        <li class="nav-item nav-link">
            <form action="/stamp_correction_request/list" method="get" class="d-inline">
                <input type="hidden" name="tab" value="approved">
                <button type="submit" class=" border-0 bg-transparent fw-bold">承認済み</button>
            </form>
        </li>
    </ul>
    
    
    <div class="bg-white rounded shadow-sm p-3">
        <table class="table text-center align-middle mb-0">
            <thead class="text-secondary">
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceCorrections as $attendanceCorrection)
                    <tr>
                        <td>
                            @switch($attendanceCorrection->status)
                                @case(1)
                                    承認待ち
                                    @break
                                @case(2)
                                    承認済み
                                    @break
                                @default
                                    無効な申請です
                            @endswitch
                        </td>
                        <td>{{$attendanceCorrection->user->name}}</td>
                        <td>{{$attendanceCorrection->clock_in->format('Y/m/d')}}</td>
                        <td>{{$attendanceCorrection->reason}}</td>
                        <td>{{$attendanceCorrection->created_at->format('Y/m/d')}}</td>
                        <td>
                            @auth('admin')
                                <a href="/stamp_correction_request/approve/{{$attendanceCorrection->id}}" class="detail-link">詳細</a>
                            @else
                                <a href="/attendance/{{$attendanceCorrection->attendance->id}}" class="detail-link">詳細</a>
                            @endif
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection