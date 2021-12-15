<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: Provider.php
 *Description:
 */

namespace Electronics\Models;
use \Illuminate\Database\Eloquent\Model;

class Provider extends Model {
    // The table associated with this model
    protected $table = 'providers';
    protected $primaryKey = 'provider_id';
    public $timestamps = false;


    public static function getProviders()
    {
        //all() method only retrieves the providers.
        $providers = self::all();
        return $providers;
    }

    public static function getProviderById($provider_id)
    {
        $provider = self::findOrFail($provider_id);
        return $provider;
    }


//creating a provider
    public static function createProvider($request)
    {
        $params = $request->getParsedBody();

        //Creating a new provider object
        $provider = new Provider();

        foreach ($params as $field => $value) {
            $provider->$field = $value;
        }

        $provider->save();
        return $provider;
    }

//updating a provider
    public static function updateProvider($request)
    {
        $params = $request->getParsedBody();
        $id = $request->getAttribute('id');
        $provider = self::findOrFail($id);

        foreach ($params as $field => $value) {
            $provider->$field = $value;
        }
        $provider->save();
        return $provider;
    }


//deleting a provider
    public static function deleteProvider($request)
    {
        $provider_id = $request->getAttribute('id');
        $provider = self::findOrFail($provider_id);
        return ($provider->delete());
    }
}