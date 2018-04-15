@php
    $section = 0;
@endphp
<h4>{{ $vid_score_type->first()->rubric->first()->competition->name }}</h4>
{!! Form::open([ 'route' => [ 'rubric.save', $competition_id ]]) !!}
@foreach( $vid_score_type as $category)
    <div class="rubric_category" data-category-id="{{ $category->id }}">
        <div class="rubric_section_header row">
            <div class="section_title">{{ $category->display_name }}</div>
            <div class="number_holder">
                @for($i=0;$i<5;$i++)
                    <div class="text-center">{{ $i }}</div>
                @endfor
            </div>
        </div>
        @forelse($category->rubric as $rubric_row)
            <div class="rubric_edit_row row">
                {{ Form::hidden("rubric[{$rubric_row->id}][vid_score_type_id]",$rubric_row->vid_score_type_id, [ 'class' => 'type_id' ]) }}
                {{ Form::hidden("rubric[{$rubric_row->id}][vid_competition_id]",$rubric_row->vid_competition_id, [ 'class' => 'competition_id' ]) }}
                {{ Form::hidden("rubric[{$rubric_row->id}][order]",$rubric_row->order, [ 'class' => 'order' ]) }}
                {{ Form::hidden("rubric[{$rubric_row->id}][new]",0, [ 'class' => 'new' ]) }}
                {{ Form::hidden("rubric[{$rubric_row->id}][delta]",0, [ 'class' => 'delta' ]) }}
                {{ Form::hidden("rubric[{$rubric_row->id}][delete]",0, [ 'class' => 'delete' ]) }}
                <div class="rubric_movement_controls">
                    @if(!$hasScores)
                    <button class="btn btn-primary btn-sm rubric_button" data-direction="up">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                    <button class="btn btn-primary btn-sm rubric_button" data-direction="down">
                        <i class="fa fa-chevron-down"></i>
                    </button>
                    @endif
                </div>
                <div class="rubric_element_and_delete">
                    <input class="form-control" type="text" name="rubric[{{ $rubric_row->id }}][element_name]" value="{{ $rubric_row->element_name }}" />
                    @if(!$hasScores)
                        <button class="btn btn-danger btn-sm rubric_delete" title="Flag for Deletion">
                            <i class="fa fa-times"></i>
                            Delete
                        </button>
                    @endif
                </div>

                <textarea rows="5" class="form-control" name="rubric[{{ $rubric_row->id }}][zero]">{{ $rubric_row->zero }}</textarea>
                <textarea rows="5" class="form-control" name="rubric[{{ $rubric_row->id }}][one]">{{ $rubric_row->one }}</textarea>
                <textarea rows="5" class="form-control" name="rubric[{{ $rubric_row->id }}][two]">{{ $rubric_row->two }}</textarea>
                <textarea rows="5" class="form-control" name="rubric[{{ $rubric_row->id }}][three]">{{ $rubric_row->three }}</textarea>
                <textarea rows="5" class="form-control" name="rubric[{{ $rubric_row->id }}][four]">{{ $rubric_row->four }}</textarea>
            </div>
        @empty
            <div class="rubric_edit_row row">
                <h4 class="text-center">
                    No Elements
                </h4>
            </div>
        @endforelse
        @if(count($category->rubric) < 11 && !$hasScores)
            <div class="rubric_category_controls row">
                <button class="btn btn-success btn-sm rubric_add_row" data-cat-id="{{$category->id}}" data-comp-id="{{ $competition_id }}">
                    <i class="fa fa-plus"></i> Add Element
                </button>
                {!! Form::submit('Save', [ 'class' => 'btn btn-primary btn-sm']) !!}
            </div>
        @endif
    </div>
@endforeach
<div class="form-group col-2-md">
    {!! Form::submit('Save', [ 'class' => 'btn btn-primary btn-sm']) !!}
</div>
{!! Form::close() !!}