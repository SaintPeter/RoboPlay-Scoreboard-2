@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/lytebox.css') }}
	{{ HTML::script('js/lytebox.js') }}
@endsection

@section('main')

@include('partials.showvideo', [ 'video' => $video, 'show_division' => true ])

<div class="row">
@include('partials.filelist', [ 'video' => $video, 'show_type' => true, 'allow_edit' => false ])
</div>
{{ link_to(URL::previous(), 'Return', [ 'class' => 'btn btn-primary btn-margin']) }}

@endsection