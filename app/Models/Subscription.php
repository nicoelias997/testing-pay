<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Plan;
use App\Models\User;

class Subscription extends Model
{
          /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'plan_id',
        'active_until',
        'created_at',
        'updated_at'
    ];


    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
