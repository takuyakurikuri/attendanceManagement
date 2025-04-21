@extends('layouts.app')

@section('title')
    <title>ログイン</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-container text-center">
        <h2 class="mb-4 fw-bold">ログイン</h2>
        <form action="/login/store" method="post">
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
            <button type="submit" class="btn btn-dark rounded-0 w-100 py-2">ログインする</button>
        </form>
        <div class="mt-3">
            <a href="/register" class="btn btn-outline-primary rounded-0 w-100">会員登録はこちら</a>
        </div>
    </div>
</div>
@endsection