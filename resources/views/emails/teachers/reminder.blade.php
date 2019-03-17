@component('mail::message')
# RoboPlay Reminder

You have issues with your RoboPlay Video or Team Submissions.  You have until {{ $comp_year->reminder_end->format('M jS') }}
to resolve these issues.

@if(count($general))
## General Issues
@foreach($general as $issue)
* {{ $issue }}
@endforeach
@endif

@if(count($teams))
## Team Issues
@foreach($teams as $name => $issue)
* {{ $name }}
    * {{ $issue }}
@endforeach
@endif

@if(count($teams))
## Video Issues
@foreach($videos as $name => $issue_list)
* {{ $name }}
@foreach($issue_list as $issue)
    * {{ $issue }}
@endforeach
@endforeach
@endif

@component('mail::button', ['url' => route('teacher.index')])
    Manage Teams and Videos
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
