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
    public function user()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public static function getPhones()
    {
        //all() method only retrieves the comments.
        $phones = self::all();
        return $phones;
    }

    public static function getPhoneById($id)
    {
        $phone = self::findOrFail($id);
        return $phone;
    }

    //create a phone
    public static function createPhones($request)
    {
        $params = $request->getParsedBody();

        //Create a new phones object
        $phone = new Phone();

        foreach ($params as $field => $value) {
            $phone->$field = $value;
        }

        $phone->save();
        return $phone;
    }

    //delete a phone
    public static function deletePhones($request)
    {
        $id = $request->getAttribute('id');
        $message = self::findOrFail($id);
        return($message->delete());
    }

    //update a phones
    public static function updatePhone($request)
    {
        $params = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $phone = self::findOrFail($id);

        foreach ($params as $field => $value) {
            $phone->$field = $value;
        }
        $phone->save();
        return $phone;
    }
}