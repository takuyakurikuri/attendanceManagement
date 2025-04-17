@extends('layouts.app')

@section('title')
    <title>勤怠画面</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div>
    <h2><span id="current-time"></span></h2>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateClock, 1000); // 毎秒更新
    updateClock(); // 初回実行
</script>

@endsection