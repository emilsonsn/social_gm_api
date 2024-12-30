<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactList extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'contact_lists';

    protected $fillable = [
        'description',
        'user_id'
    ];

    public function contacts(){
        return $this->hasMany(Contact::class, 'contact_list_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
