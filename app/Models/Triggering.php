<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Triggering extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'triggerings';

    protected $fillable = [
        'description',
        'contact_list_id',
        'user_id',
        'evo_url',
        'evo_key',
        'evo_instance',
        'interval',
        'path',
        'status'
    ]; 

    public function getPathAttribute($value){
        return $value ? asset('storage/' . $value) : '';
    }

    public function list(){
        return $this->belongsTo(ContactList::class, 'contact_list_id');
    }

    public function messages(){
        return $this->hasMany(TriggeringMessage::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
