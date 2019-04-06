@component('mail::message')
## Video Reported

**Video Name:** {{ $video->name }}<br>
**Review:** {{ link_to_route('video_scores.manage.reported','Review Site', [ 'year' => $video->year ]) }}<br>

**Comment**<br>
{{ $comment }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent

