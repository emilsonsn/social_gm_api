<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scheduling extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'schedulings';

    public $fillable = [
        'description',
        'midia',
        'mention',
        'instance_id',
        'group_id',
        'link_id',
        'group_name',
        'text',
        'video_path',
        'image_path',
        'audio_path',
        'datetime',
        'status',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function link(){
        return $this->belongsTo(Link::class);
    }

    public function instance(){
        return $this->belongsTo(Instance::class);
    }

    public function getVideoPathAttribute($value){
        return $value ? asset('storage/' . $value) : null;
    }
    
    public function getAudioPathAttribute($value){
        return $value ? asset('storage/' . $value) : null;
    }

    public function getImagePathAttribute($value){
        return $value ? asset('storage/' . $value) : null;
    }

    public function messageSendingLog(){
        return $this->hasMany(MessageSendingLog::class, 'schedule_id');
    }
}
