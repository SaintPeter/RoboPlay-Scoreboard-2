<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CompYear
 *
 * @property int $id
 * @property int $year
 * @property int $invoice_type
 * @property int $invoice_type_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompYear whereInvoiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompYear whereInvoiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompYear whereYear($value)
 * @mixin \Eloquent
 */
class CompYear extends \Eloquent {
	// Add your validation rules here
	public static $rules = [
		'year' => 'required|numeric|digits:4',
		'reminder_start' => 'required|date',
		'reminder_end' => 'required|date',
		'edit_end' => 'required|date'
	];

	protected $fillable = ['year', 'invoice_type', 'invoice_type_id', 'reminder_start', 'reminder_end', 'edit_end'];
	protected $dates = ['reminder_start', 'reminder_end', 'edit_end'];

	// Get the year requested or the most recent
	public static function yearOrMostRecent($year) {
	    if($year > 0 AND CompYear::where('year', $year)->count() > 0) {
	        return $year;
	    } else {
	        return CompYear::orderBy('year', 'desc')->first()->year;
	    }
	}

	// Get the CompYear or most recent
	public static function CompYearOrMostRecent($year) {
		if($year > 0 AND CompYear::where('year', $year)->count() > 0) {
			return CompYear::where('year', $year)->first();
		} else {
			return CompYear::orderBy('year', 'desc')->first();
		}
	}

	// Return the most recent instance of CompYear
	public static function mostRecent() {
		return CompYear::orderBy('year', 'desc')->first();
	}

	public static function current() {
		return CompYear::orderBy('year', 'desc')->first();
	}

	// Relationships
	public function competitions() {
		return $this->morphedByMany(\App\Models\Competition::class, 'yearable');
	}

	public function divisions() {
		return $this->morphedByMany(\App\Models\Division::class, 'yearable');
	}

	public function vid_competitions() {
		return $this->morphedByMany(\App\Models\Vid_competition::class, 'yearable');
	}

	public function vid_divisions() {
		return $this->morphedByMany(\App\Models\Vid_division::class, 'yearable');
	}
}