<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Wp_invoice_table
 *
 * @property int $invoice_no
 * @property int|null $old_invoice_no
 * @property int $invoice_type_id
 * @property int $user_id
 * @property float $total
 * @property int $po_rcvd
 * @property int $invoice_sent
 * @property int $paid
 * @property int $cancel
 * @property int $books_sent
 * @property int $books_received
 * @property int $mod_needed
 * @property string $notes
 * @property string $invoice_creation_date
 * @property string|null $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wp_invoice_data[] $invoice_data
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Wp_user $wp_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereBooksReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereBooksSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereInvoiceCreationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereInvoiceSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereInvoiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereModNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereOldInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table wherePoRcvd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_table query()
 */
class Wp_invoice_table extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'invoice_table';
	protected $primaryKey = 'invoice_no';
	protected $guarded = array('invoice_no');
	public $timestamps = false;

	public $data = [];

	public function invoice_data() {
		return $this->hasMany('App\Models\Wp_invoice_data', 'invoice_no', 'invoice_no');
	}

	public function wp_user() {
		return $this->belongsTo('App\Models\Wp_user', 'user_id', 'ID');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id' );
	}

	public function videos() {
		return $this->hasMany('App\Models\Video', 'teacher_id', 'user_id');
	}

	public function teams() {
		return $this->hasMany('App\Models\Team', 'teacher_id', 'user_id');
	}

	public function getData($key, $default = '') {
		if(empty($this->data)) {
			$this->data = $this->invoice_data->pluck('field_value', 'field_name')->all();
		}
		if(array_key_exists($key, $this->data)) {
			return $this->data[$key];
		} else {
			return $default;
		}
	}



}