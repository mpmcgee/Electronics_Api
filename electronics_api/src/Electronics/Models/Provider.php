<?php
/**
 * Author: Matthew McGee
 * Date: 10/13/2021
 * File: Phone.php
 *Description:
 */

namespace Electronics\Models;

class Provider extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'provider';
    protected $primaryKey = 'provider_id';
    public $timestamps = false;

    public static function getSortKeys($request) {
        $sort_key_array = array();

        // Get querystring variables from url
        $params = $request->getQueryParams();

        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']); // remove white spaces, [, and ]
            $sort_keys = explode(',', $sort); //get all the key:direction pairs
            foreach ($sort_keys as $sort_key) {
                $direction = 'asc';
                $column = $sort_key;
                if (strpos($sort_key, ':')) {
                    list($column, $direction) = explode(':', $sort_key);
                }
                $sort_key_array[$column] = $direction;
            }
        }
        return $sort_key_array;
    }

    public static function getLinks($request, $limit, $offset)
    {
        $count = self::count();
        // Get request uri and parts
        $uri = $request->getUri();
        $base_url = $uri->getBaseUrl();
        $path = $uri->getPath();

        // Construct links for pagination
        $links = array();
        $links[] = ['rel' => 'self', 'href' => $base_url . "/$path" . "?
            limit=$limit&offset=$offset"];
        $links[] = ['rel' => 'first', 'href' => $base_url . "/$path" . "?
            limit=$limit&offset=0"];
        if ($offset - $limit >= 0) {
            $links[] = ['rel' => 'prev', 'href' => $base_url . "/$path" . "?
            limit=$limit&offset=" . ($offset - $limit)];
        }
        if ($offset + $limit < $count) {
            $links[] = ['rel' => 'next', 'href' => $base_url . "/$path" . "?
            limit=$limit&offset=" . ($offset + $limit)];
        }
        $links[] = ['rel' => 'last', 'href' => $base_url . "/$path" . "?
            limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];
        return $links;
    }

    public static function searchProviders($terms)
    {
        if (is_numeric($terms)) {
            $query = self::where('provider_id', "like", "%$terms%");
        } else {
            $query = self::where('street', 'like', "%$terms%")
                ->orWhere('name', 'like', "%$terms%")
                ->orWhere('city', 'like', "%$terms%")
                ->orWhere('state', 'like', "%$terms%")
                ->orWhere('phone_number', 'like', "%$terms%");
        }
        $results = $query->get();
        return $results;
    }

}