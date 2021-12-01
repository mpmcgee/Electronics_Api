<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: Phone.php
 *Description:
 */

namespace Electronics\Models;
use \Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    // The table associated with this model
    protected $table = 'phones';
    protected $primaryKey = 'phone_id';

    //Inverse of the one-to-many relationship
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getPhones(){
        //all() methdod only retrieves the comments.
        $phones = self::all();
        return $phones;
    }

    public static function getPhoneById($id){
        $phone = self::findOrFail($id);
        return $phone;
    }


    }