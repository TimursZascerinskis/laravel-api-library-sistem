<?php

namespace App\Models;

use Database\Factories\ReaderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reader extends Model
{
    /** @use HasFactory<ReaderFactory> */
    use HasFactory;

    protected $fillable = ['vards', 'e_pasts'];
}
