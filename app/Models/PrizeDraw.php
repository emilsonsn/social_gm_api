<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrizeDraw extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'prize_draws';

    protected $fillable = [
        'instance_id',
        'groups',
        'groups_name',
        'prize_name',
    ];

    public function drawns(){
        return $this->hasMany(PrizeDrawDrawn::class);
    }

    public function instance(){
        return $this->belongsTo(Instance::class, 'external_id', 'instance_id');
    }

}
