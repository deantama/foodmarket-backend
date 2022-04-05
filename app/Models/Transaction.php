<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

    
    protected $fillable = [
        'user_id','food_id','quantity','total','status','payment_url'
    ];

    public function food()
    {
        retun $this ->hasOne(Food::class,'id','food_id');
    }

    public function user()
    {
        retun $this ->hasOne(User::class,'id','user_id');
    }

    
    public function getCreatedAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }

    public function getUpdatedAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }


}
