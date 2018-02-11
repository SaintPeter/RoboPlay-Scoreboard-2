@extends('layouts.scaffold')

@section('main')
<H2>{!! isset($error_title) ? $error_title : "An Error has Occured" !!}</H2>
<p>{!! $message !!}</p>
@endsection