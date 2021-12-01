<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: TV.php
 *Description:
 */

namespace Electronics\Models;
use \Illuminate\Database\Eloquent\Model;

class TV {
    // The table associated with this model
    protected $table = 'providers';
    protected $primaryKey = 'provider_id';

    //Inverse of the one-to-many relationship
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getProviders(){
        //all() methdod only retrieves the comments.
        $providers = self::all();
        return $providers;
    }

    public static function getProviderById($id){
        $provider = self::findOrFail($id);
        return $provider;
    }

}