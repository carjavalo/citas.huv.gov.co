<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class respuestas extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'resalias',
        'resmsj'
    ];
}
