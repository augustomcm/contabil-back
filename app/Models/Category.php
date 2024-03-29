<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 'color', 'type'
    ];

    protected $casts = [
        'type' => EntryType::class
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
