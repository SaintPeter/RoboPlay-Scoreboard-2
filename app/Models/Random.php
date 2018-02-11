<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Random
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $format
 * @property int $min1
 * @property int $max1
 * @property int $min2
 * @property int $max2
 * @property int $may_not_match
 * @property int $display_order
 * @property int $challenge_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereMax1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereMax2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereMayNotMatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereMin1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereMin2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Random whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Random extends \Eloquent {
	protected $guarded = [ 'id' ];

	public static $rules = [
		'name' => 'required',
		'type' => 'required',
		'format' => 'required',
		'min1' => 'required|numeric',
		'max1' => 'required|numeric',
		'min2' => 'numeric',
		'max2' => 'numeric',
		'display_order' => 'numeric'
	];

	// For filling in the types dropdown
	public static $types = [
		'single' => 'Single',
		'dual' => 'Dual Numbers' ];

	// Store Random Numbers for this page display
	private static $rand1 = null;
	private static $rand2 = null;

	// Create the Random Number output
	public function formatted() {
		// If the numbers have not been created, create them
		if($this->rand1 == null) {
				$this->rand1 = mt_rand($this->min1, $this->max1);
				$this->rand2 = mt_rand($this->min2, $this->max2);

			if($this->may_not_match) {
				while($this->rand1 == $this->rand2) {
					$this->rand2 = mt_rand($this->min2, $this->max2);
				}
			}
		}
		switch ($this->type) {
			case 'single':
				return sprintf($this->format, $this->rand1);
				break;
			case 'dual':
				return sprintf($this->format, $this->rand1, $this->rand2);
				break;
		}

	}

}