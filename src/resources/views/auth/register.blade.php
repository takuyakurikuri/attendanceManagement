@extends('layouts.app')

@section('title')
    <title>登録</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('header-on')

@endsection

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="register-container text-center">
        <h2 class="mb-4 fw-bold">会員登録</h2>
        <form action="/register/store" method="post">
            @csrf
            <div class="mb-3 text-start">
                <label for="name" class="form-label fw-bold">ユーザー名</label>
                <input type="text" name="name" id="name" class="form-control" value="{{old('name')}}">
            </div>
            @error('name')
                {{$message}}
            @enderror
            <div class="mb-3 text-start">
                <label for="email" class="form-labe fw-bold">メールアドレス</label>
                <input type="text" name="email" id="email" class="form-control" value="{{old('email')}}">
            </div>
            @error('email')
                {{$message}}
            @enderror
            <div class="mb-3 text-start">
                <label for="password" class="form-label fw-bold">パスワード</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="mb-3 text-start">
                <label for="password_confirmation" class="form-label fw-bold">確認用パスワード</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            <input type="hidden" name="address_id" value="1">
            @error('password')
                {{$message}}
            @enderror
            <button type="submit" class="btn btn-dark rounded-0 w-100 py-2">登録する</button>
        </form>
        <div class="mt-3">
            <a href="/login" class="btn btn-outline-primary rounded-0 w-100">ログインはこちら</a>
        </div>
    </div>
</div>
@endsection