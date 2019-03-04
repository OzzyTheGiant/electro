<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model {
	protected $table = "Bills";
	protected $primaryKey = "ID";
	/** allow or disable timestamps for when record was created */
	public $timestamps = false;
	/** allow specified fields to be mass assigned, instead of filled individually, in controller */
	protected $fillable = ['PaymentAmount', 'PaymentDate', 'User'];
}