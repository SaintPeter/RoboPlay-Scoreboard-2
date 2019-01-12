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

    </script>

    <style>

    </style>

</head>
<body>
    <div id="invoicer"></div>

    <script defer src="{{asset('js/invoicer.js')}}" ></script>
</body>
</html>