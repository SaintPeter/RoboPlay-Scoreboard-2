<li class="ui-field-contain ui-li-static ui-body-inherit ui-first-child" data-type="vertical">
	<p>{!! $score_element->display_text !!}</p>
	<input 
        id="sel_{{{ $score_element->id }}}"
        base="{{{ $score_element->base_value }}}"
        multi="1"
        step="{{{ $score_element->multiplier }}}"
        class="ui-clear-both"
        name="scores[{{{ $score_element->id }}}][value]"
        min="{{{ $score_element->min_entry }}}"
        max="{{{ $score_element->max_entry }}}"
        value="{{{ $score_element->min_entry }}}"
        map="{{ ($score_element->score_map_raw) ?: '' }}"
        type="range">
</li>