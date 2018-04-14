@php
$section = 0;
@endphp

@foreach($rubric as $rubric_row)
    @if($rubric_row->vid_score_type->id != $section)
        <div class="rubric_section_header row">
            <div class="section_title">{{ $rubric_row->vid_score_type->display_name }}</div>
            <div class="number_holder">
            @for($i=0;$i<5;$i++)
                <div class="text-center">{{ $i }}</div>
            @endfor
            </div>
        </div>

        @php
            $section = $rubric_row->vid_score_type->id
        @endphp
    @endif
    <div class="rubric_row row">
        <div ><strong>{{ $rubric_row->element_name }}</strong></div>
        <div >{{ $rubric_row->zero }}</div>
        <div >{{ $rubric_row->one }}</div>
        <div >{{ $rubric_row->two }}</div>
        <div >{{ $rubric_row->three }}</div>
        <div >{{ $rubric_row->four }}</div>
    </div>
@endforeach