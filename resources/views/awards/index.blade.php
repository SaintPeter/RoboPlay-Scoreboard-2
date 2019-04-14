@extends('layouts.scaffold')

@section('style')
<style>
    .heading {
        background-color: lightgrey;
    }
</style>
@endsection

@section('main')
<div class="col-sm-4 list-group">
    @foreach($nom_list as $cat => $teams)
        <div class="list-group-item heading">
            <h4 class="list-group-item-heading">{{ $cat }}</h4>
        </div>
        @foreach($teams as $team)
            <div class="list-group-item">
                <h5 class="list-group-item-heading">{{ $team->name }}</h5>
                <div class="list-group-item-text">{{ $team->school->name }}</div>
            </div>
        @endforeach
    @endforeach
</div>

@endsection