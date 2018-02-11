<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Frm_items
 *
 * @property int $id
 * @property string|null $item_key
 * @property string|null $name
 * @property string|null $description
 * @property string|null $ip
 * @property int|null $form_id
 * @property int|null $post_id
 * @property int|null $user_id
 * @property int|null $parent_item_id
 * @property int|null $is_draft
 * @property int|null $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Frm_fields[] $fields
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Frm_item_metas[] $values
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereIsDraft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereItemKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereParentItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Frm_items whereUserId($value)
 * @mixin \Eloquent
 */
class Frm_items extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'frm_items';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];
	public $timestamps = false;

    // This is a list of unique invoices

    public function getDescriptionAttribute() {
        return unserialize($this->attributes['description']);
    }

    // It has a bunch of fields associated with it
	public function fields() {
		return $this->hasMany('App\Models\Frm_fields', 'form_id', 'form_id');
	}

    // It has a bunch of item_metas
	public function values() {
	    return $this->hasMany('App\Models\Frm_item_metas', 'item_id', 'id');
	}
}