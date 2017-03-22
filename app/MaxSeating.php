<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laraveldaily\Quickadmin\Observers\UserActionsObserver;


use Illuminate\Database\Eloquent\SoftDeletes;

class MaxSeating extends Model {

    use SoftDeletes;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    protected $table    = 'maxseating';
    
    protected $fillable = ['seating'];
    

    public static function boot()
    {
        parent::boot();

        MaxSeating::observe(new UserActionsObserver);
    }
    
    
    
    
}