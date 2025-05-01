@extends('layouts.app')

@section('title')
    <title>管理者ログイン</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-container text-center">
        <h2 class="mb-4 fw-bold">管理者ログイン</h2>
        <form action="/admin/login/store" method="post">
            @csrf
            <div class="mb-3 text-start">
                <label for="email" class="form-label fw-bold">メールアドレス</label>
                <input type="text" name="email" id="email" class="form-control" value="{{old('email')}}">
            </div>
            @error('email')
                {{$message}}
            @enderror
            <div class="mb-3 text-start">
                <label for="password" class="form-label fw-bold">パスワード</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            @error('password')
                {{$message}}
            @enderror
            <button type="submit" class="btn btn-dark rounded-0 w-100 py-2">管理者ログインする</button>
        </form>
    </div>
</div>
@endsection