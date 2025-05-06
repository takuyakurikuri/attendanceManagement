@extends('layouts.app')

@section('title')
    <title>スタッフ一覧</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff_list.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container mt-5">
    <div class="mb-4">
        <h4 class="fw-bold border-start border-4 ps-3">スタッフ一覧</h4>
    </div>
    <div class="table-responsive rounded-3">
        <table class="table text-center align-middle">
            <thead class="text-secondary">
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td><a class="detail-link" href="/admin/attendance/staff/{{$user->id}}">詳細</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection