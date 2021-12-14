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

//    public static function getTVs(){
//        //all() method only retrieves the comments.
//        $tvs = self::all();
//        return $tvs;
//    }

    public static function getTVs($request)
    {

        //get the total number of TVs
        $count = self::count();

        //get query string variables from url
        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; // items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //Get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;


            //Pagination
            $links = self::getLinks($request, $limit, $offset);

            // Sorting.
            $sort_key_array = self::getSortKeys($request);


            $query = self::all();
            $query = $query->slice($offset)->take($limit);  // limit the rows

            // sort the output by one or more columns
            foreach ($sort_key_array as $column => $direction) {
                $query->sortBy($column, $direction);


            $tvs = $query;

            //construct data for the response
            $results = [
                'totalCount' => $count,
                'limit' => $limit,
                'offset' => $offset,
                'links' => $links,
                'sort' => $sort_key_array,
                'data' => $tvs
            ];
            return $results;
        }
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
        $tv = new TV();

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
        $id = $request->getAttribute('id');
        $tv = self::findOrFail($id);

        foreach ($params as $field => $value) {
            $tv->$field = $value;
        }
        $tv->save();
        return $tv;
    }


    // Delete a tv listing
    public static function deleteTV($tv_id)
    {
        $tv = self::findOrFail($tv_id);
        return ($tv->delete());
    }

    public static function searchTVs($terms)
    {
        if (is_numeric($terms)) {
            $query = self::where('tv_id', "like", "%$terms%")
                    ->orWhere('provider_id', "like", "%$terms%");
        } else {
            $query = self::where('name', 'like', "%$terms%")
                ->orWhere('brand', 'like', "%$terms%");
        }
        $results = $query->get();
        return $results;
    }

    // This function returns an array of links for pagination. The array includes links for the current, first, next, and last pages.
    public static function getLinks($request, $limit, $offset)
    {
        $count = self::count();

        // Get request uri and parts
        $uri = $request->getUri();
        $base_url = $uri->getBaseUrl();
        $path = $uri->getPath();

        // Construct links for pagination
        $links = array();
        $links[] = ['rel' => 'self', 'href' => $base_url . $path . "?limit=$limit&offset=$offset"];
        $links[] = ['rel' => 'first', 'href' => $base_url . $path . "?limit=$limit&offset=0"];
        if ($offset - $limit >= 0) {
            $links[] = ['rel' => 'prev', 'href' => $base_url . $path . "?limit=$limit&offset=" . ($offset - $limit)];
        }
        if ($offset + $limit < $count) {
            $links[] = ['rel' => 'next', 'href' => $base_url . $path . "?limit=$limit&offset=" . ($offset + $limit)];
        }
        $links[] = ['rel' => 'last', 'href' => $base_url . $path . "?limit=$limit&offset=" . $limit * (ceil($count / $limit) - 1)];

        return $links;
    }

    /*
     * Sort keys are optionally enclosed in [ ], separated with commas;
     * Sort directions can be optionally appended to each sort key, separated by :.
     * Sort directions can be 'asc' or 'desc' and defaults to 'asc'.
     * Examples: sort=[number:asc,title:desc], sort=[number, title:desc]
     * This function retrieves sorting keys from uri and returns an array.
    */
    public static function getSortKeys($request)
    {
        $sort_key_array = array();

        // Get querystring variables from url
        $params = $request->getQueryParams();

        if (array_key_exists('sort', $params)) {
            $sort = preg_replace('/^\[|\]$|\s+/', '', $params['sort']);  // remove white spaces, [, and ]
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
}