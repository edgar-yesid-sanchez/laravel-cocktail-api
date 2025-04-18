<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cocktail extends Model
{
	//protected $fillable = ['name', 'category', 'image'];

	public function user()
	{
			return $this->belongsTo(User::class);
	}

	protected $fillable = [
    'user_id',
    'api_id',
    'name',
    'category',
    'image',
	];
}
