@extends('layouts.scaffold')

@section('style')
<style>
    .clickable {
        cursor: pointer;
    }
</style>
@endsection

@section('main')
    <div class="panel panel-default">
        <div class="panel-heading clickable" data-toggle="collapse" data-target="#award_definitions">
            <div class="panel-title">
                Judge Awards
            </div>
        </div>
        <div id="award_definitions" class="panel-collapse collapse in">
            <div class="panel-body">
                <strong>Perseverance Award</strong>
                <p>This award goes to the team that improvises and overcomes a difficult situation while still maintaining a
                    high level of performance.</p>
                <strong>Spirit Award</strong>
                <p>This award celebrates a team that displays extraordinary enthusiasm and spirit</p>
                <strong>Teamwork Award</strong>
                <p>This award recognizes a team that fluidly works together with strong communication, tasks delegation, and
                    excellent time management.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <table class="table table-condensed table-bordered">
            <tbody>
            @foreach($divs as $div_name => $div)
                <tr>
                    <td colspan="3" class="bg-primary">
                        <h4 ><strong>{{ $div_name }}</strong></h4>
                    </td>
                </tr>
                <tr>
                    <th>Award</th>
                    <th>Team</th>
                    <th>School</th>
                </tr>
                @foreach($div as $award => $team)
                    <tr>
                        <td>{{ $award }}</td>
                        <td>{{ $team->name }}</td>
                        <td>{{ $team->school->name }}</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
@endsection