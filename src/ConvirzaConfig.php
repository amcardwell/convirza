<?php

namespace Skidaatl\Convirza;

use Illuminate\Database\Eloquent\Model;

class ConvirzaConfig extends Model
{
	protected $table = 'convirza_config';

	protected $guarded = [];

	public $timestamps = false;

	public $incrementing = false;
}
