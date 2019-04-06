@component('mail::message')
# Video Disqualified

We regret to inform you that your student video '{{ link_to_route('teacher.videos.show', $video->name, [ $video ]) }}' has been disqualified.

### Disqualification Reasons
@foreach($video->problems as $problem)
* {{ $problem->detail->reason  }}
@if($problem->timestamp > -1)
    (<a href="{{ $video->url($problem->timestamp) }}&autoplay=0" target="_blank">{{ $problem->formatted_timestamp }}</a>)
@endif
@if($problem->comment)
    * {{ $problem->comment }}
@endif
@endforeach

@if($resolvable)
## Resolvable Issues
All of the issues identified may be resolvable.  If your students can quickly fix the issues,
you may re-upload the video to YouTube and {{ link_to_route('teacher.videos.edit', "Edit", [ $video ], [ 'target' => "_blank"]) }}
your video entry to update the YouTube link.  Do not delete your video entry, as you will not be allowed to recreate it.
@else
## Unresolvable Issues
One or more of the above identified issues are not resovable.  You may not attempt to correct these issues, as it
would be unfair to other contestants.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
