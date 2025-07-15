<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Mi App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @livewireScripts
    @livewireStyles

</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
