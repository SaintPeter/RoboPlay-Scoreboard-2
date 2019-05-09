<html>
    <head>
        <title>{{ $file->name }}</title>
        <link href="{{ asset('madeleine/css/Madeleine.css') }}" rel="stylesheet">
        <script src="{{ asset('madeleine/lib/stats.js') }}"></script>
        <script src="{{ asset('madeleine/lib/detector.js') }}"></script>
        <script src="{{ asset('madeleine/lib/three.min.js') }}"></script>
        <script src="{{ asset('madeleine/Madeleine.js') }}"></script>
    </head>
    <style>
        .madeleine {
            width: 600px;
            height: 600px;
            margin: auto;
        }
    </style>
    <body>
    <div id="target" class="madeleine"></div>

    <script>
      window.onload = function(){
        var madeleine = new Madeleine({
          target: 'target', // target div id
          data: '{{ $file->download_url }}', // data path
          path: '{{ asset('madeleine/') }}', // path to source directory from current html file
          viewer: {
            width: 600,
            height: 600
          }
        });
      };
    </script>
    </body>
</html>
