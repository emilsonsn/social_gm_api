<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriggeringMessage extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'triggering_messages';

    protected $fillable = [
        'message',
        'triggering_id',
    ]; 

    public function triggering(){
        return $this->belongsTo(Triggering::class);
    }}
