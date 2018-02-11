<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Frm_item_metas
 *
 * @property int $id
 * @property string|null $meta_value
 * @property int $field_id
 * @property int $item_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_item_metas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_item_metas whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_item_metas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_item_metas whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_item_metas whereMetaValue($value)
 * @mixin \Eloquent
 */
class Frm_item_metas extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'frm_item_metas';
	protected $primaryKey = 'id';
	protected $guarded = array('id');
	public $timestamps = false;

	public $data = [];

}