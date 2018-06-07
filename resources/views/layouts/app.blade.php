<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pasbeli - @yield('title')</title>

        <link rel="stylesheet" type="text/css" href="/css/spectre.min.css">
        <style>
        .content .container {
            padding-bottom: 1.5rem;
        }

        .content .panel .tile {
            margin: .75rem 0;
        }
        </style>
        @section('assets')
        @show
    </head>
    <body>
        <div class="content">
            @yield('content')
        </div>

        @section('endjs')
        @show
    </body>
</html>