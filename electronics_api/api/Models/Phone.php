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
    public $timestamps = false;

    //Inverse of the one-to-many relationship
    public function user()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

//    public static function getPhones()
//    {
//        //all() method only retrieves the comments.
//        $phones = self::all();
//        return $phones;
//    }

    public static function getPhones($request)
    {

        //get the total number of Phones
        $count = self::count();

        //get query string variables from url
        $params = $request->getQueryParams();

        //Do limit and offset exist?
        $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; // items per page
        $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0; // offset of the first item

        //Get search terms
        $term = array_key_exists('q', $params) ? $params['q'] : null;

        if (!is_null($term)) {
            $phones = self::searchPhones($term);
            return $phones;
        } else {
        //Pagination
        $links = self::getLinks($request, $limit, $offset);

        // Sorting.
        $sort_key_array = self::getSortKeys($request);


        $query = self::all();
        $query = $query->slice($offset)->take($limit);  // limit the rows

        // sort the output by one or more columns
        foreach ($sort_key_array as $column => $direction) {
            $query->sortBy($column, $direction);
        }


            $phones = $query;

            //construct data for the response
            $results = [
                'totalCount' => $count,
                'limit' => $limit,
                'offset' => $offset,
                'links' => $links,
                'sort' => $sort_key_array,
                'data' => $phones
            ];
            return $results;
        }
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

    public static function searchPhones($terms)
    {
        if (is_numeric($terms)) {
            $query = self::where('phone_id', "like", "%$terms%")
                     ->orWhere('provider_id', "like", "%$terms%");
        } else {
            $query = self::where('name', 'like', "%$terms%")
                ->orWhere('brand', 'like', "%$terms%");
        }
        $results = $query->get();
        return $results;
    }
}