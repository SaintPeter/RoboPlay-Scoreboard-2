<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Review</title>
    <link media="all" type="text/css" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    {{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css') }}

    <script type="application/javascript">
        var urlYear = {!! $year !!};
        var yearList = {!! $yearList !!};
    </script>

    <style>

    </style>

</head>
<body>
    <div class="container">
        <h1>Invoice Review</h1>
        @if(!isset($skip_breadcrumbs))
            @php
                if(Breadcrumbs::exists()) {
                    echo Breadcrumbs::render();
                } else {
                    echo '<div class="error">Breadcrumbs - Missing Route: ' . Route::current()->getName() . "</div>";
                }
            @endphp
        @endif
        <div id="invoicer"></div>
    </div>

    <div class="text-center">
        <span style="font-size: 10px; ">This page took {{ round((microtime(true) - LARAVEL_START),5) }} seconds to render</span>
    </div>

    <script defer src="{{asset('js/invoicer.js')}}" ></script>
</body>
</html>