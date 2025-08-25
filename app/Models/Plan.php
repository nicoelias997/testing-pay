<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class Plan extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'slug',
        'price',
        'duration_in_days',
        'created_at',
        'updated_at'
    ];

    public function subscription(){
        return $this->belongsTo(Subscription::class);
    }
}
