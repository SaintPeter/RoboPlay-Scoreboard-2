@php
$section = 0;
@endphp
<h4>{{ $vid_score_type->first()->rubric->first()->competition->name }}</h4>
@foreach( $vid_score_type as $category)
    @if($category->rubric->count() > 0)
        <div class="rubric_section_header row">
            <div class="section_title">{{ $category->display_name }}</div>
            <div class="number_holder">
                @for($i=0;$i<5;$i++)
                    <div class="text-center">{{ $i }}</div>
                @endfor
            </div>
        </div>
        @foreach($category->rubric as $rubric_row)
            <div class="rubric_row row">
                <div ><strong>{{ $rubric_row->element_name }}</strong></div>
                <div >{{ $rubric_row->zero }}</div>
                <div >{{ $rubric_row->one }}</div>
                <div >{{ $rubric_row->two }}</div>
                <div >{{ $rubric_row->three }}</div>
                <div >{{ $rubric_row->four }}</div>
            </div>
        @endforeach
    @endif
@endforeach