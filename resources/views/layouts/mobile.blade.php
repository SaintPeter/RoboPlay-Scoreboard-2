<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>RoboPlay Scoreboard @yield('title')</title>

		{{ HTML::style('//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css') }}
		{{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css') }}
		{{ HTML::style('css/jquery.mobile-1.4.1.css') }}
		{{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') }}
        {{ HTML::script('js/jquery.mobile-1.4.1.js') }}
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
			    div[data-role=page] {
			        max-width: 600px;
			        left:0;
			        right:0;
			        margin-left:auto;
			        margin-right:auto;
			        border: 1px solid #ccc !important;
			        -moz-box-shadow:    2px 2px 12px 2px rgba(100,100,100,0.3);
			        -webkit-box-shadow: 2px 2px 12px 2px rgba(100,100,100,0.3);
			        box-shadow:         2px 2px 12px 2px rgba(100,100,100,0.3);
			    }
			}
			@yield('style')
		</style>
		<link rel="icon" type="image/ico" href="http://cstem.ucdavis.edu/scoreboard/favicon.ico"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    @yield('script')
	</head>

	<body>
		<div data-role="page" data-url="/score/">

			<div data-role="header">
				<h1>@yield('header')</h1>
				<a href="/" class="ui-btn-left" data-icon="home" data-ajax="false" data-iconpos="notext" data-direction="reverse">Home</a>
				@yield('navbar')
			</div><!-- /header -->

			<div data-role="content" >
				<!-- menu position helper -->
				<div id="menu-anchor"></div>
					@if (Session::has('message'))
						<div class="flash alert">
							<p>{{ Session::get('message') }}</p>
						</div>
					@endif

					@yield('main')
			</div><!-- /content -->

			<div data-role="footer">
				<h4>@yield('footer')
    				<div class="text-center">
    				    <span style="font-size: 10px; ">This page took {{ round((microtime(true) - LARAVEL_START),5) }} seconds to render</span>
    				</div>
				</h4>

			</div><!-- /footer -->
		</div><!-- /page -->
	</body>
</html>