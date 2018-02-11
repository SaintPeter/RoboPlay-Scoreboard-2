@extends('layouts.scaffold')

@section('main')
	@include('partials.showvideo', [ 'video' => $video, 'show_division' => true ])

	@include('partials.filelist', [ 'video' => $video, 'show_type' => false, 'allow_edit' => false ])

{{ link_to(URL::previous(), 'Return', [ 'class' => 'btn btn-primary btn-margin']) }}
@endsection