<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    @yield('title')
    @yield('css')
</head>
<body>
    <header class="bg-dark text-white py-2">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/" class="d-flex align-items-center text-white text-decoration-none">
                    <img class="logo me-2" src="{{ asset('images/logo.svg') }}" alt="COACHTECH" style="height: 40px;">
                </a>

                @if (!Request::is(['register', 'login','admin/login']))
                    <div class="d-flex align-items-center gap-3">
                        @if(Auth::guard('admin')->check())
                            <a class="text-white text-decoration-none fw-bold" href="/admin/attendance/list">勤怠一覧</a>
                            <a class="text-white text-decoration-none fw-bold" href="/admin/staff/list">スタッフ一覧</a>
                            <a class="text-white text-decoration-none fw-bold" href="/stamp_correction_request/list">申請一覧</a>
                            <form action="/admin/logout" method="post" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-link text-white text-decoration-none fw-bold p-0">ログアウト</button>
                            </form>
                        @else
                            <a class="text-white text-decoration-none fw-bold" href="/attendance">勤怠</a>
                            <a class="text-white text-decoration-none fw-bold" href="/attendance/list">勤怠一覧</a>
                            <a class="text-white text-decoration-none fw-bold" href="/stamp_correction_request/list">申請</a>
                            <form action="/logout" method="post" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-link text-white text-decoration-none fw-bold p-0">ログアウト</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>