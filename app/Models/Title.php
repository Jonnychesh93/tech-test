<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model {
    protected $fillable = [
        'display_title',
        'csv_title',
    ];

}
