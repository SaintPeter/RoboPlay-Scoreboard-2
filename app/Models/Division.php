<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Division
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $display_order
 * @property int $level
 * @property int $competition_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Challenge[] $challenges
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompYear[] $comp_year
 * @property-read \App\Models\Competition $competition
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereCompetitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Division query()
 * @property-read int|null $challenges_count
 * @property-read int|null $comp_year_count
 * @property-read int|null $teams_count
 */
class Division extends Model {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'display_order' => 'required',
		'competition_id' => 'required'
	);

	// Relationships
	public function competition()
	{
		return $this->belongsTo('App\Models\Competition')->orderBy('name');
	}

	public function challenges()
	{
		return $this->belongsToMany('App\Models\Challenge')->withPivot('display_order')->orderBy('display_order', 'asc');
	}

	public function teams()
	{
		return $this->hasMany('App\Models\Team');
	}

	public function comp_year() {
		return $this->morphToMany('App\Models\CompYear', 'yearable');
	}

	/**
	 * Return a list of id => long name keypairs
	 *
	 * @param null $withYear If true, includes year in
	 * @param null $year
	 * @return array
	 */
	public static function longname_array($withYear = false, $year = null)
	{
		$divlistQuery = Division::with('competition', 'comp_year');
		if($year) {
			$divlistQuery->where('comp_year.year', $year);
		}
		$divlist = $divlistQuery->get();
		$namelist[0] = "-- Select Division --";
		foreach($divlist as $div) {
			if($withYear) {
				// Skip elements without a comp year set
				if($div->comp_year->first()) {
					$namelist[$div->comp_year->first()->year][$div->competition->name][$div->id] = $div->competition->location . " - " . $div->name;
				}
			} else {
				$namelist[$div->competition->name][$div->id] = $div->competition->location . " - " . $div->name;
			}
		};
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

	public static function longname_array_counts()
	{
		$divlist = Division::with('competition', 'challenges')->get();
		$namelist[0] = "-- Select Division --";
		foreach($divlist as $div) {
			$namelist[$div->competition->name][$div->id] = $div->competition->location . " - " . $div->name . " ({$div->challenges->count()})";
		};
		return $namelist;
	}

	public function longname()
	{
		return $this->competition->name . ' - ' . $this->name;
	}
}
