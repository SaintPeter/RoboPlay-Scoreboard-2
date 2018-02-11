<style>
.tag_container {
	border-radius: 5px;
	display: inline-block;
	border: 1px solid black;
	vertical-align: middle;
	margin: 5px;

}
.tag_icon {
	float:left;
	border-top-left-radius: 5px;
	border-bottom-left-radius: 5px;
	color: white;
	width: 30px;
	height: 30px;
	padding: 0px;
	display: block;
	text-align: center;
	border-bottom: 1px solid black;
	border-top: 1px solid black;
	border-left: 1px solid black;
}
.tag_icon > span {
	min-height: 30px;
	line-height: 25px;
}
.tag_text {
	background-color: white;
	border-top-right-radius: 5px;
	border-bottom-right-radius: 5px;
	color: black;
	float: left;
	padding: 4px 4px 4px 8px;
	height:30px;
	border-bottom: 1px solid black;
	border-top: 1px solid black;
	border-right: 1px solid black;
}
</style>
@if($video->has_story)
	<div class="tag_container btn-primary">
		<div class="tag_icon "><span class="glyphicon glyphicon-book"></span></div>
		<div class="tag_text">Storyline</div>
	</div>
@endif

@if($video->has_choreo)
	<div class="tag_container btn-danger">
			<div class="tag_icon"><span class="glyphicon glyphicon-music"></span></div>
			<div class="tag_text">Choreography</div>
	</div>
@endif

@if($video->has_task)
	<div class="tag_container btn-warning">
			<div class="tag_icon"><span class="glyphicon glyphicon-cutlery"></span></div>
			<div class="tag_text">Interesting Task</div>
	</div>
@endif

@if($video->has_code)
	<div class="tag_container btn-success">
			<div class="tag_icon"><span class="glyphicon glyphicon-flash"></span></div>
			<div class="tag_text">Computational Thinking</div>
	</div>
@endif

@if($video->has_custom)
	<div class="tag_container btn-info">
			<div class="tag_icon"><span class="glyphicon glyphicon-wrench"></span></div>
			<div class="tag_text">Custom Part</div>
	</div>
@endif