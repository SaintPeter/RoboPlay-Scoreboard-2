<li>
		<p>{!! $score_element->display_text !!}</p>
		<select
            id="sel_{{{ $score_element->id }}}"
            base="{{{ $score_element->base_value }}}"
            multi="{{{ $score_element->multiplier }}}"
            name="scores[{{{ $score_element->id }}}][value]"
            map="{{ ($score_element->score_map_raw) ?: '' }}"
            data-role="flipswitch" >
	        <option value="0">No</option>
	        <option value="1">Yes</option>
    	</select>
</li>