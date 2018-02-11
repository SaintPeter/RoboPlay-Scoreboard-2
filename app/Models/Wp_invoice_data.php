<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Wp_invoice_data
 *
 * @property int $row_id
 * @property int $invoice_no
 * @property string $field_name
 * @property string $field_value
 * @property float $field_cost
 * @property-read \App\Models\Wp_invoice_table $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_data whereFieldCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_data whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_data whereFieldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_data whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice_data whereRowId($value)
 * @mixin \Eloquent
 */
class Wp_invoice_data extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'invoice_data';
	protected $primaryKey = 'row_id';
	protected $guarded = ['row_id'];
	public $timestamps = false;

	public function invoice() {
			return $this->belongsTo('App\Models\Wp_invoice_table', 'invoice_no', 'invoice_no');
	}
}