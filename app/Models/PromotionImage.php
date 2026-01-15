<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'image_path',
        'display_order',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}


