<h4>Video Reported</h4>
<strong>Video Name: </strong>{{ $video->name }}<br>
<strong>Review:</strong>{{ link_to_route('video_scores.manage.reported','Review Site', [ 'year' => $year ]) }}
<strong>Comment</strong><br>
{{ $comment }}
