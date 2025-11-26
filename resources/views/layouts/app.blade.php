<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="{{ asset('images/bfar.png') }}" type="image/x-icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <title>@yield('title', 'pms')</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body>
    <div class="w-full min-h-screen bg-red-100 flex relative">
        @if ($errors->any())
            <div
                class="absolute top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 max-w-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session()->has('message'))
            <div
                class="absolute top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50 max-w-sm">
                {{ session('message') }}
            </div>
        @endif
        <div class="w-[224px] h-screen sticky top-0 bg-white">
            @include('partials.sidebar')
        </div>
        <div class="flex-1 min-h-screen bg-gray-200 flex flex-col">
            @include('partials.navbar')
            @yield('content')
        </div>
    </div>

    @once
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                toastr.options = {
                    "progressBar": true,
                    "positionClass": "toast-bottom-right"
                };
                window.addEventListener('success', event => {
                    toastr.success(event.detail.message);
                });
                window.addEventListener('warning', event => {
                    toastr.warning(event.detail.message);
                });
                window.addEventListener('error', event => {
                    toastr.error(event.detail.message);
                });
            });
        </script>
    @endonce
    @livewireScripts
    @stack('scripts')
</body>
</html>
