<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageSendingLog extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    public $table = 'message_sending_logs';

    public $fillable = [
        'schedule_id',
        'description',
        'instanceName',
        'datetime',
        'group_id',
    ];

    public function schedule(){
        return $this->belongsTo(Scheduling::class);
    }
}