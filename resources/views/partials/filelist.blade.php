<div class="row">
    @if($video->filelist)
    <div class="col-sm-12 col-md-6">
        <h4>Files ({{ count($video->files) }})</h4>
        <div class="panel panel-default ">
            @foreach($video->filelist as $cat => $files)
                <?php $catname = preg_replace('/\W/','',strtolower($cat)) ?>
                <div class="panel-heading" role="button" data-toggle="collapse" data-target="#{{ $catname }}" aria-expanded="true">
                    {{ $cat }} ({{ count($files) }})
                </div>
                <div class="panel-collapse in" id="{{ $catname }}" aria-expanded="true">
            		<ul class="list-group">
            		@foreach($files as $file)
            		    <li class="list-group-item">
            		        &nbsp;&nbsp;
            				@if($file->filetype->viewer == 'lytebox')
        						<a href="{{ $file->url() }}" class="lytebox" data-title="{{ $file->filename }}" data-lyte-options="group:group1" target="_blank" >
        							<i class="fa {{ $file->filetype->icon}}"></i>
        							<span id="filename_{{ $file->id }}">{{ $file->filename }}</span>
        						</a>
            				@else
        						<a href="{{ url($file->path()) }}" target="_blank">
        							<i class="fa {{ $file->filetype->icon}}"></i>
        							{{ $file->filename }}
        						</a>
            				@endif
            				<span class="pull-right">
                				@if($file->filetype->viewer == 'lytebox' or $file->filetype->viewer == 'none')
             					    <a href="{{ url($file->path()) }}" target="_blank">
             					        <span class="glyphicon glyphicon-download" title="Download File"></span>
             					    </a>
             					    &nbsp;
                 				@endif
                				@if($allow_edit)
            				        <a class="rename_button"
            				           data-ext="{{ $file->filetype->ext }}"
            				           data-filename="{{ $file->just_filename() }}"
            				           data-id="{{ $file->id }}"
            				           data-target="{{ route('uploader.rename_file', [ 'video_id' => $video->id, 'file_id' => $file->id ]) }}"
            				           href="#">
            					        <span class="glyphicon glyphicon-edit" title="Rename File"></span>
            					    </a>
            					    &nbsp;
            					    <a href="{{ route('uploader.delete_file', [ 'video_id' => $video->id, 'file_id' => $file->id ]) }}" class="delete_file">
            					        <span class="glyphicon glyphicon-remove" style="color: red;" title="Delete File"></span>
            					    </a>
                				@endif
                			</span>
            			</li>
            		@endforeach
            		</ul>
            	</div>
            @endforeach
        </div>
    </div>

@if($allow_edit)
    {{ HTML::style('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.0.3/jquery-confirm.min.css') }}
    {{ HTML::script('//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.0.3/jquery-confirm.min.js') }}
@endif
<div id="active_dialog" style="display:none" title="Rename File">
    <form class="form-inline" id="rename_file">
            <div class="text-danger error_message"></div>
        <div class="form-group">

            <label>Filename</label>
                <input name="filename" class="filename form-control" type="text" value="">
                &nbsp;.<span class="ext"></span>
        </div>

    </form>
</div>
<script>
    // Make a copy of the dialog
    //$( "#dialog" ).clone().attr('id', 'active_dialog').dialog({ autoOpen: false });

    $(".delete_file").confirm({
        title: "Delete File?",
        content: "This will premanently delete this file.",
        buttons: {
            delete: function() {
                location.href = this.$target.attr('href');
            },
            cancel: function() {

            }
        }
    });

    // Globals to store dialog data
    var rename_url;
    var ext;
    var filename;
    var file_id;

    $(".rename_button").on('click', function(e) {
        e.preventDefault();
        rename_url = $(this).data('target');
        ext = $(this).data('ext');
        filename = $(this).data('filename');
        file_id = $(this).data('id');
        $("#active_dialog").dialog('open');
    });

    $("#active_dialog").dialog({
            autoOpen: false,
            resizable: false,
            width: 500,
    		open: function() {
    		    $('#active_dialog .filename').val(filename);
    		    $('#active_dialog .ext').html(ext);
    		},
    		buttons: {
    			"Rename": function() {
    				var data = $( "#active_dialog #rename_file" ).serialize();
    				$.post(rename_url, data, function(returnData) {
    					if(returnData.success) {
    					    $("filename_" + file_id).html(returnData.filename);
    					    $("#active_dialog").dialog('close');
    					} else {
    					    $('#active_dialog .error_message').html(returnData.msg);
    					}
    				});
    			},
    			"Cancel": function() {
    				$("#active_dialog").dialog('close');
    			}
    		},
    		close: function() {
    		    //$( "#dialog" ).clone().attr('id', 'active_dialog').dialog({ autoOpen: false });
    		}

        });
</script>
@else
    <div class="col-sm-12 col-md-6">
        <h4>No Files</h4>
    </div>

@endif
</div>