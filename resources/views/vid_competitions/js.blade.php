<script>
  $(document).ready(function() {
    $( ".date" ).datepicker({ dateFormat: "yy-mm-dd" });

    var user_list = [];
    $("#filter").on('input', function(e) {
      var filter = e.target.value;

      if(filter.length >= 3 && user_list.length === 0) {
        $("#spinner").show();
        $.get("/vid_competitions/user_list/" + filter, function(data) {
            user_list = data;
            render_user_list(filter);
            $("#spinner").hide();
        });
      } else {
        if(filter.length < 3) {
          user_list = [];
        }
        render_user_list(filter);
      }
    });

    function render_user_list(filter) {
      var re = new RegExp(filter, "i");
      var show_user_list = user_list.filter(function(item) {
        return re.test(item.name) || re.test(item.email);
      });

      var target = $("#user_list");
      if(show_user_list.length > 0) {
        target.empty()
          .append(function() {
            return show_user_list.map(function(user) {
              return '<option value="' + user.id + '">' + user.name + " (" + user.email + ")</option>";
            }).join('');
          });
      } else {
        target.empty().append('<option value="">-- None --</option>');
      }
    }

  });
</script>