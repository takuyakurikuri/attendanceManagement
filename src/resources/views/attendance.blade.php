@extends('layouts.app')

@section('title')
    <title>勤怠画面</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        @if ($isClockOut)
            <div class="status-label">退勤済</div>
        @elseif ($isWorkingToday)
            <div class="status-label">出勤中</div>
        @elseif ($isBreaking)
            <div class="status-label">休憩中</div>
        @else
            <div class="status-label">勤務外</div>
        @endif
        <h2 id="current-date">＊＊＊＊年＊月＊日(＊)</h2>
        <div class="current-time" id="current-time">＊＊:＊＊:＊＊</div>

        @if ($isClockOut)
            <h2 class="thanks">お疲れ様でした。</h2>
        @elseif ($isWorkingToday)
            <div class="d-flex justify-content-center">
                @if ($isBreaking)
                    <form action="/attendance/breakEnd" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="attendance_id" value={{$attendance->id}}>
                        <button type="submit" class="break-button">休憩戻</button>
                    </form>
                @else
                    <form class="me-5" action="/attendance/clockOut" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="attendance_id" value={{$attendance->id}}>
                        <button type="submit" class="attendance-button">退勤</button>
                    </form>
                    <form action="/attendance/breakStart" method="post">
                        @csrf
                        <input type="hidden" name="attendance_id" value={{$attendance->id}}>
                        <button type="submit" class="break-button">休憩入</button>
                    </form>
                @endif
            </div>
        @else
            <form action="/attendance/clockIn" method="post">
                @csrf
                <button type="submit" class="attendance-button">出勤</button>
            </form>
        @endif
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;

        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        const day = now.getDate();
        const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        const weekday = weekdays[now.getDay()];
        document.getElementById('current-date').textContent = `${year}年${month}月${day}日（${weekday}）`;
    }

    setInterval(updateClock, 1000); // 毎秒更新
    updateClock(); // 初回実行
</script>

@endsection