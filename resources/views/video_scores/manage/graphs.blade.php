@extends('layouts.scaffold')

@section('main')
    @include('partials.year_select')
    @include('partials.scorenav', [ 'nav' => 'graphs', 'year' => $year])

    @if($year)
        <img src="{{ route('graph_video_performace', [ 'year' => $year ]) }}" width="550" height="350">
        <img src="{{ route('graph_judge_performace', [ 'year' => $year ]) }}" width="550" height="350">
    @else
        <h2 class="text-center">You must select a year to display graphs</h2>
    @endif

@endsection