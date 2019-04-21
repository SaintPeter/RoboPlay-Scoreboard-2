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
    @foreach($nom_list as $cat => $award)
        <div class="list-group-item heading">
            <h4 class="list-group-item-heading">{{ $cat }}</h4>
        </div>
        @foreach($award['teams'] as $team)
            <div class="list-group-item{{ $team->has_award($award['award_id']) ? ' list-group-item-success' : '' }}">
                <h5 class="list-group-item-heading">
                    @if($team->has_award($award['award_id']))
                        <img src="{{ asset('images/star.png') }}" alt="Has Award Star" />
                    @endif
                    {{ $team->name }}
                    @if($team->has_award($award['award_id']))
                        <a class="btn btn-danger btn-xs pull-right"
                           href="{{ route('awards.revoke', [$div_id, $team->id, $award['award_id']]) }}">Remove Award</a>
                    @else
                        @if(!$award['awarded'])
                            <a class="btn btn-success btn-xs pull-right"
                               href="{{ route('awards.grant', [$div_id, $team->id, $award['award_id']]) }}">Grant Award</a>
                        @endif
                    @endif
                </h5>
                <div class="list-group-item-text">{{ $team->school->name }}</div>
            </div>
        @endforeach
    @endforeach
</div>

@endsection