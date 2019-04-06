@component('mail::message')
# Video Disqualified

Video '{{ link_to("/video_review/{$video->year}/{$video->id}", $video->name) }}' has been disqualified.

### Disqualification Reasons
@foreach($video->problems as $problem)
* {{ $problem->detail->reason  }}
@if($problem->timestamp > -1)
    (<a href="{{ $video->url($problem->timestamp) }}&autoplay=0" target="_blank">{{ $problem->formatted_timestamp }}</a>)
@endif
@if($problem->comment)
    * {{ $problem->comment }}
@endif
@if($problem->reviewer)
    * Reviewer: {{ $problem->reviewer->name }}
@endif
@endforeach

@if($resolvable)
All issues are resolvable.
@else
One or more issues are unresolveable.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
