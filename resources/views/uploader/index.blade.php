@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/SimpleAjaxUploader.min.js') }}
@endsection

@section('script')
<script>
	$(function() {
		var uploader = new ss.SimpleUpload({
			button: 'uploadButton',
			url: '{!! route('uploader.handler', [ $video_id ]) !!}',
			progressUrl: '{!! route('uploader.progress') !!}',
			responseType: 'json',
			name: 'uploadfile',
			multiple: true,
			maxUploads: 3,
			queue: true,
			allowedExtensions: {!! $ext_list !!},
			hoverClass: 'ui-state-hover',
			focusClass: 'ui-state-focus',
			disabledClass: 'ui-state-disabled',
			onExtError: function(filename, ext) {
				var output = document.getElementById('output');
				var message = document.createElement('div');
				message.className = 'alert alert-danger';
				message.innerHTML = '<strong>Error:</strong> File "' + filename + '" does not match one of the valid formats.';
				output.appendChild(message);
				return true;
			},
			onSizeError: function(filename, fileSize) {
				var output = document.getElementById('output');
				var message = document.createElement('div');
				message.className = 'alert alert-danger';
				message.innerHTML = '<strong>Error:</strong> File "' + filename + '" file size exceeds limit of 100 MegaBytes.';
				output.appendChild(message);
				return true;
			},
			onSubmit: function(filename, extension) {
				// Create the elements of our progress bar
				var progress = document.createElement('div'),
					bar = document.createElement('div'),
					fileSize = document.createElement('div'),
					perc = document.createElement('div'),
					wrapper = document.createElement('div'),
					progressBox = document.getElementById('progressBox');

				// Assign each element its corresponding class
				progress.className = 'progress';
				bar.className = 'progress-bar';
				perc.className = 'perc';
				fileSize.className = 'size';
				wrapper.className = 'wrapper';

				// Assemble the progress bar and add it to the page
				bar.appendChild(perc);
				progress.appendChild(bar);
				wrapper.innerHTML = '<div class="name">'+filename+'</div>';
				wrapper.appendChild(fileSize);
				wrapper.appendChild(progress);

				progressBox.appendChild(wrapper);

				// Assign roles to the elements of the progress bar
				this.setProgressBar(bar);
				this.setPctBox(perc);
				this.setFileSizeBox(fileSize);
				this.setProgressContainer(wrapper);
			},
			onError: function(filename, errorType, status, statusText, response, uploadBtn) {
				var output = document.getElementById('output');
				var message = document.createElement('div');
				message.className = 'alert alert-danger';
				message.innerHTML = 'Upload failed: ' + statusText;
				output.appendChild(message);
				return true;
			},
			onComplete: function(filename, response) {
				if (!response || response.success != 0) {
					var output = document.getElementById('output');
					var message = document.createElement('div');
					message.className = 'alert alert-danger';
					message.innerHTML = 'Upload of file "' + filename + '" failed ' + response.success + ': ' + response.msg;
					output.appendChild(message);
					return false;
				}
				else {
					var output = document.getElementById('output');
					var message = document.createElement('div');
					message.className = 'alert alert-success alert-dismissable';
					message.innerHTML = 'Thank you for uploading "' + response.file + '"';
					output.appendChild(message);
					return true;
				}
			}
		});
	});
</script>
@endsection

@section('style')
<style>
.filetypes {
	margin: 10px 15px;
}
</style>
@endsection

@section('main')
<div class="col-md-8">
	<div class="clearfix">
		<h4>Known File Types</h4>
		@foreach($filetypes as $type => $ext_list)
			<div class="pull-left filetypes">
				<strong>{{ $type }}</strong>
				<p style="margin-left: 10px;">{{ join(', ', $ext_list) }}</p>
			</div>
		@endforeach
	</div>
	<h4>File Size Limits</h4>
	<p style="margin-left: 10px;">Files may be up to 100MB. <br />Video Files must be at least 10MB.</p>
	<br />
	<div id="uploads">
		<div id="noupload">
			<input id="uploadButton" class="btn btn-primary btn-large" type="button" value="Choose File"></input>
		</div>
		<div id="progressBox"></div>
		<div id="output"></div>
	</div>
	<div style="display: block; position: absolute; overflow: hidden; margin: 0px; padding: 0px; opacity: 0; direction: ltr; z-index: 2147483583; left: 413px; top: 192px; width: 100px; height: 34px; visibility: hidden;">
		<input style="position: absolute; right: 0px; margin: 0px; padding: 0px; font-size: 480px; font-family: sans-serif; cursor: pointer;" accept="image/*" multiple="" name="imgfile" type="file">
	</div>
	<br />
	<br />
	<a class="btn btn-info" href="{{ URL::previous() }}">Return to Video</a>
</div>
@endsection
