<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentPlatform extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'image',
        'subscriptions_enabled'
    ];

    public static function resolveService($id) {
        $payment_platform = self::where('id', $id)->firstOrFail();
        $name = strtolower(str_replace(' ', '_', $payment_platform->name));
        
        $service = config("services.$name.class"); 
        if($service) {
            return resolve($service);
        } 
        throw new NotFoundHttpException("The selected payment platform is not in the configuration");
    }
}
