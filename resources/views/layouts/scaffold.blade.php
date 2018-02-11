<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>@if(isset($title)){{ $title . ' | ' }}RoboPlay Scoreboard @endif</title>
		<link rel="icon" type="image/ico" href="http://cstem.ucdavis.edu/scoreboard/favicon.ico"/>
		{{ HTML::style('//netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css') }}
		{{ HTML::style('//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css') }}
		{{-- HTML::style('/css/custom-theme/jquery-ui-1.12.0.custom.css') --}}
		{{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css') }}
		{{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js') }}
        {{ HTML::script('//code.jquery.com/ui/1.12.1/jquery-ui.min.js') }}
        {{ HTML::script('//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js') }}

        @yield('head')

		<style>
			table form { margin-bottom: 0; }
			form>ul { margin-left: 0; list-style: none; }
			.error { color: red; font-style: italic; }
			label { display: block; }
			body { padding-top: 20px; padding-bottom: 50px;}
			.btn-margin {
				margin: 2px;
			}
			::-webkit-scrollbar {
			    -webkit-appearance: none;
			    width: 7px;
			}
			::-webkit-scrollbar-thumb {
			    border-radius: 4px;
			    background-color: rgba(0,0,0,.5);
			    box-shadow: 0 0 1px rgba(255,255,255,.5);
			}
			.over {
			 	text-decoration: overline;
			}
			.table-nonfluid {
               width: auto !important;
            }
			@yield('style')
		</style>

	    @yield('script')
	</head>

	<body>

@php
    $container = isset($fluid) ? 'container-fluid' : 'container';
@endphp
		<div class="{{ $container }}">
			@yield('before_header')

			@if(isset($title) and !isset($skip_title))
				<h1>{{ $title }}</h1>
			@endif
			@if(!isset($skip_breadcrumbs))
                @php
                    if(Breadcrumbs::exists()) {
                        echo Breadcrumbs::render();
                    } else {
                        echo '<div class="error">Breadcrumbs - Missing Route: ' . Route::current()->getName() . "</div>";
                    }
                @endphp
				{{-- Breadcrumbs::render() --}}
			@endif

@section('message')
			@if (Session::has('message'))
				<div class="alert alert-info alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {!!  Session::get('message') !!}
                </div>
			@endif
			@if (Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {!!  Session::get('error') !!}
                </div>
			@endif
@show

			@yield('main')
		</div>
		<div class="text-center">
		    <span style="font-size: 10px; ">This page took {{ round((microtime(true) - LARAVEL_START),5) }} seconds to render</span>
		</div>
	</body>

</html>