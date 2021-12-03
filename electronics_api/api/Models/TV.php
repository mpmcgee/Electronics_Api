<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: TV.php
 *Description:
 */

namespace Electronics\Models;
use \Illuminate\Database\Eloquent\Model;

class TV extends Model {
    // The table associated with this model
    protected $table = 'tvs';
    protected $primaryKey = 'tv_id';

    //Inverse of the one-to-many relationship
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getTVs(){
        //all() method only retrieves the comments.
        $tvs = self::all();
        return $tvs;
    }

    public static function getTVById($tv_id){
        $tv = self::findOrFail($tv_id);
        return $tv;
    }


    // Create a new tv listing
    public static function createTV($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        // Create a new TV instance
        $tv = new \Electronics\Models\TV();

        // Set the tv attributes
        foreach ($params as $field => $value) {

            // Need to hash password
            if ($field == 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }

            $tv->$field = $value;
        }

        // Insert the tv into the database
        $tv->save();
        return $tv;
    }

    // Update a tv listing
    public static function updateTV($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        //Retrieve the tv's id from url and then the tv from the database
        $tv_id = $request->getAttribute('tv_id');
        $tv = self::findOrFail($tv_id);

        // Update attributes of the professor
        $tv->provider_id = $params['provider_id'];
        $tv->name = $params['name'];
        $tv->brand = $params['brand'];
        $tv->price = $params['price'];

        // Update the database
        $tv->save();
        return $tv;
    }


    // Delete a tv listing
    public static function deleteTV($tv_id)
    {
        $tv = self::findOrFail($tv_id);
        return ($tv->delete());
    }
}