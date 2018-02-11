<li>
		<p>{!! $score_element->display_text !!}</p>
		<select id="sel_{{{ $score_element->id }}}" base="{{{ $score_element->base_value }}}" multi="{{{ $score_element->multiplier }}}" name="scores[{{{ $score_element->id }}}][value]" data-role="flipswitch" >
	        <option value="1">Yes</option>
	        <option value="0">No</option>
    	</select>
</li>