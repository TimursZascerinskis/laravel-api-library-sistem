<?php

namespace App\Models;

use Database\Factories\BorrowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    /** @use HasFactory<BorrowFactory> */
    use HasFactory;

    protected $fillable = ['gramata_id', 'lasitajs_id', 'aiznemsanas_datums', 'atdosanas_datums'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'gramata_id');
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class, 'lasitajs_id');
    }
}
