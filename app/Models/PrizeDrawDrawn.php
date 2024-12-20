<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrizeDrawDrawn extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'prize_draw_drawns';

    protected $fillable = [
        'prize_draw_id',
        'name',
        'number',
    ];

    public function prizeDraw(){
        return $this->belongsTo(PrizeDraw::class);
    }
    
}
