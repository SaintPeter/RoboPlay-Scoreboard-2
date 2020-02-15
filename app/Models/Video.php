<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Video
 *
 * @property int $id
 * @property string $name
 * @property string $yt_code
 * @property int $has_custom
 * @property int $vid_division_id
 * @property int $school_id
 * @property int $teacher_id
 * @property int $has_code
 * @property int $has_story
 * @property int $has_choreo
 * @property int $has_task
 * @property int $has_vid
 * @property int $year
 * @property int $flag
 * @property int $audit
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VideoAward[] $awards
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_comment[] $comments
 * @property-read \App\Models\Vid_division $division
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files[] $files
 * @property-read mixed $filelist
 * @property-read \App\Models\School $school
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_scores[] $scores
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read \App\Models\Vid_division $vid_division
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereAudit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHasChoreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHasCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHasCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHasStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHasTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereHasVid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereVidDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereYtCode($value)
 * @mixin \Eloquent
 * @property int $status
 * @property int $review_status
 * @property int|null $reviewer_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_review_problems[] $problems
 * @property-read \App\Models\User|null $reviewer
 * @property-read \App\Models\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereReviewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video whereStatus($value)
 */
class Video extends Model {
	protected $guarded = [ 'id', 'flag' ];
	protected $fillable = [
		'name', 'yt_code', 'has_custom', 'vid_division_id',
		'school_id', 'teacher_id', 'has_code', 'has_story',
		'has_choreo', 'has_task', 'has_vid', 'year', 'flag',
		'audit', 'status', 'notes', 'created_at', 'updated_at'
	];

	public static $rules = array(
		'name' => 'required',
		'yt_code' => ['required','yt_valid', 'yt_embeddable', 'yt_public', 'yt_length:60,300'],
		'school_id' => 'required',
		'vid_division_id' => 'required|not_in:0'
	);

	protected $attributes = array(
  		'has_custom' => false,
  		'has_vid' => false,
  		'has_code' => false
	);

	public static function boot()
    {
        parent::boot();

        static::deleting(function($video)
        {
        	foreach($video->files as $file) {
        		$file->delete();
        	}

           if(is_dir(public_path() . '/uploads/video_' . $video->id)) {
            	rmdir(public_path() . '/uploads/video_' . $video->id);
            }
        });
    }

	public function setYtCodeAttribute($code)
	{
		if(preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $code, $matches)) {
			if(isset($matches[2]) && $matches[2] != ''){
				$this->attributes['yt_code'] = $matches[2];
				return;
			} else {
				$this->attributes['yt_code'] = '';
			}
		}
		$this->attributes['yt_code'] = $code;
	}

	public function url($timestamp = -1) {
		$url = "https://youtu.be/" . $this->yt_code;
		if($timestamp > -1) {
			$url .= "?t=${timestamp}s";
		}
		return $url;
	}

	// Relationships
	public function awards() {
	    return $this->belongsToMany('App\Models\VideoAward');
	}

	public function vid_division()
	{
		return $this->belongsTo('App\Models\Vid_division');
	}

	public function division()
	{
		return $this->belongsTo('App\Models\Vid_division', 'vid_division_id');
	}

	public function school()
	{
		return $this->belongsTo('App\Models\School');
	}

	public function files()
	{
		return $this->hasMany('App\Models\Files')->orderBy('filename');
	}

	public function scores()
	{
		return $this->hasMany('App\Models\Video_scores');
	}

	public function students() {
		return $this->morphToMany('App\Models\Student', 'studentable');
	}

	public function comments() {
		return $this->hasMany('App\Models\Video_comment');
	}

	public function teacher() {
		return $this->belongsTo('App\Models\User', 'teacher_id');
	}

	public function problems() {
		return $this->hasMany(Video_review_problems::class);
	}

	public function reviewer() {
		return $this->belongsTo(User::class,'reviewer_id');
	}

	// Methods
	public function student_count()
	{
		return $this->students()->count();
	}

	public function student_list()
	{
		$student_list = [];

		if(count($this->students) > 0) {
			foreach($this->students as $student) {
				$student_list[] = $student->fullName();
			}
		} else {
			$student_list = [ 'Warning: No Students' ];
		}

		return $student_list;
	}

	public function getYearAttribute() {
		return $this->created_at->year;
	}

	public function general_scores_count()
	{
		$count = $this->scores->reduce(function($acc, $score) {
			if($score->score_group == 1) {
				$acc++;
			}
		    return $acc;
		}, 0);
		if($this->year > 2017) {
			$count /= 4;
		} else {
			$count /= 3;
		}
		return $count;
	}

	public function part_scores_count()
	{
		if($this->has_custom) {
			$count = 0;
			$this->scores->map(function($score) use (&$count) {
				if($score->score_group == 2) {
					$count++;
				}
			});
			return $count;
		}
		return '-';
	}

	public function compute_scores_count()
	{
		if($this->has_code) {
			$count = 0;
			$this->scores->map(function($score) use (&$count) {
				if($score->score_group == 3) {
					$count++;
				}
			});
			return $count;
		}
		return '-';
	}

	public function all_scores_count()
	{
	    $count = $this->scores->reduce(function($acc, $score) {
			if($score->score_group == 1) {
				$acc['general']++;
			}
			if($score->score_group == 2) {
				$acc['custom']++;
			}
			if($score->score_group == 3) {
				$acc['compute']++;
			}
		    return $acc;
		}, ['general' => 0, 'compute' => 0, 'custom' => 0]);

		$count['general'] /= 3;
		return $count;
	}

    // Produce a list of files sorted into categories
	public function getFilelistAttribute()
	{
	    $output = [];

	    if(count($this->files)) {
	        foreach($this->files as $file) {
	            $output[$file->filetype->name][] = $file;
	        }
	        uksort($output, function($a, $b) { return strcasecmp($a, $b); });
	        foreach($output as $cat => $file) {
	            uasort($output[$cat], function($a, $b) { return strnatcasecmp($a->filename, $b->filename); } );
	        }
	    }
	    return $output;
	}

}
