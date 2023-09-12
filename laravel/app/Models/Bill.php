<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model {
	protected $table = "bills";
	protected $primaryKey = "id";
	/** allow or disable timestamps for when record was created */
	public $timestamps = false;
	/** allow specified fields to be mass assigned, instead of filled individually, in controller */
	protected $fillable = ['user_id', 'payment_amount', 'payment_date'];
}
