<?php

namespace Caffeinated\Modules\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
	 * The attributes that are fillable via mass assignment.
	 *
	 * @var array
	 */
    protected $fillable = ['name', 'slug', 'basename', 'manifest', 'enabled', 'order'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'manifest' => 'array',
    ];
}
