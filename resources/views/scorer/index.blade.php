<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    {{ HTML::style('css/jquery.mobile-1.4.1.css') }}
<!--
    {{ HTML::style('//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css') }}
    {{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') }}
    {{ HTML::script('js/jquery.mobile-1.4.1.js') }}
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    -->

    <script type="application/javascript">
        var compData = {!! $competition_list !!};
    </script>

    <style>
        table form { margin-bottom: 0; }
        form ul { margin-left: 0; list-style: none; }
        .error { color: red; font-style: italic; }
        body { padding-top: 20px; }
        .breadcrumbs {
            list-style: none;
            overflow: hidden;
        }
        .breadcrumbs li {
            float: left;
        }
        .over {
            text-decoration: overline;
        }
        @media all and (min-width: 600px) {
            .ui-page {
                max-width: 600px;
                left:0;
                right:0;
                margin-left:auto;
                margin-right:auto;
                border: 1px solid #ccc !important;
                -moz-box-shadow:    2px 2px 12px 2px rgba(100,100,100,0.3);
                -webkit-box-shadow: 2px 2px 12px 2px rgba(100,100,100,0.3);
                box-shadow:         2px 2px 12px 2px rgba(100,100,100,0.3);
                display: block;
            }
        }

        .listview-spacer {
            margin-top: 1em !important;
        }

    </style>

</head>
<body>
    <div id="scorer"></div>

    <script src="{{asset('js/app.js')}}" ></script>
</body>
</html>