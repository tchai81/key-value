<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyValue extends Model
{
    use HasFactory;

    public function key() {
        return $this->belongsTo('App\Models\Key');
    }
}
