<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    <link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script>
        $(document).on("mobileinit", function() {
            $.mobile.ajaxEnabled = false;
            $.mobile.autoInitializePage = false;
            //$.mobile.linkBindingEnabled = false;
        });
    </script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    {{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css') }}

    <script type="application/javascript">
        var compData = {!! $competition_list !!};
        var judgeId = '{!! $judgeId !!}';
        var judgeName = '{!! $judgeName !!}';
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
        .ui-mobile .ui-page-active {
            overflow-x: visible !important;
        }

        .bigtext {
            font-size: 100px;
        }
        .center {
            text-align: center;
        }
        #abortPopup-popup, #submitPopup-popup, #randomPopup-popup, #randomListPopup-popup {
            width: 90%;
        }
        .score_display {
            top: 25%;
            width: 2em;
        }

        .favoriteButton {
            position: absolute;
            right: 50px;
            top: 25%;
            border-radius: 50%;
            background-color: darkgrey;
            border: 2px solid lightgrey;
            padding: 0;
            width: 1.75em;
            height: 1.75em;
        }

        .favoriteStar-active {
            color: yellow;
            -webkit-text-stroke: 1px black;
        }

        .favoriteStar-inactive {
            color: black;
            -webkit-text-stroke: unset;
        }


    </style>

</head>
<body>
    <div id="scorer"></div>

    <script defer src="{{asset('js/app.js')}}" ></script>
</body>
</html>