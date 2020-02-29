<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vid_division
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $display_order
 * @property int $competition_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompYear[] $comp_year
 * @property-read \App\Models\Vid_competition $competition
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_scores[] $scores
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereCompetitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_division query()
 * @property-read int|null $comp_year_count
 * @property-read int|null $scores_count
 * @property-read int|null $videos_count
 */
class Vid_division extends Model {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'display_order' => 'required',
		'competition_id' => 'required'
	);

	// Relationships
	public function competition() {
		return $this->belongsTo('App\Models\Vid_competition');
	}

	public function scores()
	{
		return $this->hasMany('App\Models\Video_scores');
	}

	public function videos()
	{
		return $this->hasMany('App\Models\Video');
	}

	public function comp_year() {
		return $this->morphToMany('App\Models\CompYear', 'yearable');
	}

	/**
	 * Return a list of id => long name keypairs
	 *
	 * @return array
	 */
	public static function longname_array($withYear = false, $year = null)
	{
		$divlistQuery = Vid_division::with('competition', 'comp_year');
		if($year) {
			$divlistQuery->where('comp_year.year', $year);
		}
		$divlist = $divlistQuery->get();
		$namelist[0] = "-- Select Video Division --";
		foreach($divlist as $div) {
			if ($withYear) {
				// Skip elements without a comp year set
				if ($div->comp_year->first()) {
					$namelist[$div->comp_year->first()->year][$div->competition->name][$div->id] = $div->name;
				}
			} else {
				$namelist[$div->competition->name][$div->id] = $div->name;
			}
		}
		uksort($namelist, function($a, $b) {
			if($a == 0) {
				return -1;
			}
			if($b == 0) {
				return 1;
			}
			return $b <=> $a;
		});
		return $namelist;
	}

	public function longname()
	{
		return $this->competition->name . ' - ' . $this->name;
	}
}
