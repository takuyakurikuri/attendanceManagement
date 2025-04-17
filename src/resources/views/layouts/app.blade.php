<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    @yield('title')
    @yield('css')
</head>
<body>
    <header class="bg-dark text-white py-2">
    <div class="container">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center my-2 gap-3">
            
            <a href="/">
                <div class="d-flex align-items-center">
                    <img class="logo" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
                </div>
            </a>
        
            @if (!Request::is(['register', 'login']))
                {{-- <form class="d-flex w-50 w-md-25 flex-grow-1" action="/search" method="get">
                    <input class="form-control mx-2" name="keyword" type="text" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                    <input type="hidden" name="tab" value="{{ request('tab', 'mylist') }}">
                </form> --}}

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    @if(Auth::check())
                        <form action="/logout" method="post">
                            @csrf
                            <button class="btn btn-secondary btn-fixed">ログアウト</button>
                        </form>
                    @else
                        <a class="btn btn-secondary btn-fixed" href="/login">ログイン</a>
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