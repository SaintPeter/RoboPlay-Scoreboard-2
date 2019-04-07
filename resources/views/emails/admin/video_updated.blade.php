@component('mail::message')
## Disqualified Video Updated

Previously Disqualified Video '{{ link_to("/video_review/{$video->year}/{$video->id}", $video->name) }}' Has Been Updated

The video has been moved back to the reviewed Queue.

Resolve the outstanding issues or move it to disqualified.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
