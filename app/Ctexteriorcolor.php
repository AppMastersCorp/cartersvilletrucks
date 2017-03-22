<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class Ctexteriorcolor extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'ctexteriorcolor';
    
    protected $fillable = ['exterior_color'];
    

    public static function boot()
    {
        parent::boot();

        Ctexteriorcolor::observe(new UserActionsObserver);
    }
    
    
    
    
}