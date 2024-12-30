<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'contacts';

    protected $fillable = [
        'name',
        'phone',
        'is_whatsapp',
        'contact_list_id'
    ];  

    public function list(){
        return $this->belongsTo(ContactList::class);
    }
}
