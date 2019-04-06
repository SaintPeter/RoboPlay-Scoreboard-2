<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Frm_fields
 *
 * @property int $id
 * @property string|null $field_key
 * @property string|null $name
 * @property string|null $description
 * @property string|null $type
 * @property string|null $default_value
 * @property string|null $options
 * @property int|null $field_order
 * @property int|null $required
 * @property string|null $field_options
 * @property int|null $form_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereFieldOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereFieldOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields whereType($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_fields query()
 */
class Frm_fields extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'frm_fields';
	protected $primaryKey = 'id';
	protected $guarded = array('id');
	public $timestamps = false;

	public static $rules = array();

    // Attributes
    public function getFieldOptionsAttribute() {
        return unserialize($this->attributes['field_options']);
    }

}
