<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RandomList
 *
 * @property int $id
 * @property string $name
 * @property string $format
 * @property string $popup_format
 * @property string $d1_format
 * @property string $d2_format
 * @property string $d3_format
 * @property string $d4_format
 * @property string $d5_format
 * @property int $display_order
 * @property int $challenge_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RandomListElement[] $elements
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereD1Format($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereD2Format($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereD3Format($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereD4Format($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereD5Format($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList wherePopupFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RandomList extends \Eloquent {
	protected $fillable = [
	    'name', 'format', 'popup_format',
	    'd1_format', 'd2_format', 'd3_format', 'd4_format', 'd5_format',
	    'display_order',
	    'challenge_id'];

    public static $rules = [
		'name' => 'required',
		'format' => 'required',
	    'popup_format' => 'required',
		'display_order' => 'numeric'
	];

	public static $chosen = null;

	// We will ALWAYS load elements
	protected $with = [ 'elements' ];

	public function get_elements() {
	    $element_list = $this->elements;

        if(count($element_list)) {
            $output = [];

    	    foreach($element_list as $element) {
    	        $output[] = join(';', [ $element->d1, $element->d2, $element->d3, $element->d4, $element->d5 ]);
    	    }

    	    return join("\n", $output);
    	} else {
    	    return '';
    	}
	}

	public function get_formatted() {
	    $element_list = $this->elements;

        if(isset($this->chosen)) {
            $choose = $this->chosen;
        } else {
            $this->chosen = $choose = rand(0, count($element_list) - 1);
        }

        $formatted = $this->format_elements($element_list[$choose]);

	    return str_ireplace(array_keys($formatted), $formatted, $this->format);
	}

	public function get_formatted_popup() {
	    $element_list = $this->elements;

        if(isset($this->chosen)) {
            $choose = $this->chosen;
        } else {
            $this->chosen = $choose = rand(0, count($element_list) - 1);
        }

        $formatted = $this->format_elements($element_list[$choose]);

	    return str_ireplace(array_keys($formatted), $formatted, $this->popup_format);

	}

    public function format_elements($elements) {
        $names = [ 'd1', 'd2', 'd3', 'd4', 'd5' ];
        $formats = [ 'd1_format', 'd2_format', 'd3_format', 'd4_format', 'd5_format' ];
        $output = [];

        for($i = 0; $i < 5; $i++) {
            if($this->{$formats[$i]}) {
                $output['{' . $names[$i] . '}'] = sprintf($this->{$formats[$i]}, $elements->{$names[$i]});
            } else {
                break;
            }
        }
        return $output;
    }


	// Relationships
	public function elements()
	{
	    return $this->hasMany('App\Models\RandomListElement');
	}

}