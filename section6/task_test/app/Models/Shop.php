<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public function area()
    {
        return $this->belongsTo("App\Models\Area");
    }

    public function route()
    {
        return $this->belongsToMany("App\Models\Route");
    }
}
