<script>
    $(document).ready(function () {
      $('.validate_video').click(function (e) {
        e.preventDefault();
        var video_id = $(this).data('id');
        $('#validation_results_' + video_id).remove();
        $(this).removeClass('validate_pulse');
        $('#spinner_' + video_id).show();
        $.get('/validate_video/' + video_id, function (data) {
          $('#video_row_' + video_id).after('<tr id="validation_results_' + video_id + '"><td colspan="8">' + data + "</td></tr>");
          $('#spinner_' + video_id).hide();
        });
      });
    });
</script>