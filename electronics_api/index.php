<?php
/**
 * Author: Matthew McGee
 * Date: 10/13/2021
 * File: index.php
 *Description:
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electronics\Models\User;
use Electronics\Models\TV;
use Electronics\Models\Provider;
use Electronics\models\Phone;

//$app = new \Slim\App();

$app->get('/', function($request, $response, $args){
    return $response->write("Hello, this is Electronics API.");
});

$app->get('/hello/{name}', function ($request, $response, $args){
    return $response->write("Hello " . $args['name']);
});

$app->get('/tvs', function ($request, $response, $args) {
    //Get the total number of tvs
    $count = TV::count();
    //Get querystring variable from url
    $params = $request->getQueryParams();
    //Do limit and offset exist?
    $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10; //
    //items per page
    $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0;
    // offset of the first item
    //Pagination
    $links = TV::getLinks($request, $limit, $offset);
    // Sorting.
    $sort_key_array = TV::getSortKeys($request);

    $query = TV::skip($offset)->take($limit);  // limit the rows
    // sort the output by one or more columns
    foreach ($sort_key_array as $column => $direction) {
        $query->orderBy($column, $direction);
    }

    $tvs = $query->get();

    $payload = [];

    foreach ($tvs as $_tv) {
        $payload[$_tv->tv_id] = [
            'provider_id' => $_tv->provider_id,
            'name' => $_tv->name,
            'brand' => $_tv->brand,
            'price' => $_tv->price
        ];
    }

    $payload_final = [
        'totalCount' => $count,
        'limit' => $limit,
        'offset' => $offset,
        'links' => $links,
        'sort' => $sort_key_array,
        'data' => $payload
    ];
    return $response->withStatus(200)->withJson($payload_final);
});

$app->get('/providers/{provider_id}/tvs', function (Request $request, Response $response, array $args){
    $tv = TV::all();
    $provider_id = $args['provider_id'];

    $payload = [];

    foreach ($tv as $_tv) {
        if ($_tv->provider_id == $provider_id) {
            $payload[$_tv->tv_id] = [
                'provider_id' => $_tv->provider_id,
                'name' => $_tv->tv_id,
                'brand' => $_tv->brand,
                'price' => $_tv->price
            ];
        }
    }
    return $response->withStatus(200)->withJson($payload);

});

//create a new tv listing
$app->post('/tvs', function ($request, $response, $args) {
    $tv = new TV();
    $_tv = $request->getParsedBodyParam('tv', '');
    $_tv_id = $request->getParsedBodyParam('tv_id');
    $_provider_id = $request->getParsedBodyParam('provider_id');
    $_name = $request->getParsedBodyParam('name');
    $_brand = $request->getParsedBodyParam('brand');
    $_price = $request->getParsedBodyParam('price');
    $tv->tv_id = $_tv_id;
    $tv->provider_id = $_provider_id;
    $tv->name = $_name;
    $tv->brand = $_brand;
    $tv->price = $_price;

    $tv->save();
    if ($tv->tv_id) {
        $payload = ['tv_id' => $tv->tv_id,
            'tv_uri' => '/tv/' . $tv->tv_id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});

//update a tv listing
$app->patch('/tvs/{tv_id}', function ($request, $response, $args) {
    $id = $args['tv_id'];
    $tv = TV::findOrFail($id);
    $params = $request->getParsedBody();
    foreach ($params as $field => $value) {
        $tv->$field = $value;
    }
    $tv->save();
    if ($tv->tv_id) {
        $payload = ['tv_id' => $tv->tv_id,
            'provider_id' => $tv->provider_id,
            'name' => $tv->name,
            'brand' => $tv->brand,
            'price' => $tv->price,
            'tv_uri' => '/tv/' . $tv->tv_id
        ];
        return $response->withStatus(200)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }
});


//delete a tv listing
$app->delete('/tvs/{tv_id}', function ($request, $response, $args) {
    $tv_id = $args['tv_id'];
    $tv = TV::find($tv_id);
    $tv->delete();
    if ($tv->exists) {
        return $response->withStatus(500);
    } else {
        return $response->withStatus(204)->getBody()->write("TV '/tvs/$tv_id' has been deleted.");
    }
});

//get a single tv listing
$app->get('/tvs/{tv_id}', function ($request, $response, $args){
    $tv_id = $args['tv_id'];
    $tv = new TV();
    $_tv = $tv->find($tv_id);

    $payload[$_tv ->tv_id] = [
        'provider_id' => $_tv->provider_id,
        'name' => $_tv->name,
        'brand' => $_tv->brand,
        'price' => $_tv->price];

    return $response->withStatus(200)->withJson($payload);

});



$app->get('/users', function(Request $request, Response $response, array $args){

   $users = User::all();

   $payload = [];

   foreach ($users as $_usr){
       $payload[$_usr->user_id] = ['role' => $_usr->role,
           'last_name' => $_usr->last_name,
           'first_name' => $_usr->first_name,
           'password' => $_usr->password,
           'email' => $_usr->email,
           'phone_number' => $_usr->phone_number,
       ];
   }
   return $response->withStatus(200)->withJson($payload);
});

$app->get('/users/{user_id}', function (Request $request, Response $response, array $args){
    $id = $args['user_id'];
    $user = new User();
    $_usr = $user->find($id);

    $payload[$_usr->user_id] = ['role' => $_usr->role,
        'last_name' => $_usr->last_name,
        'first_name' => $_usr->first_name,
        'password' => $_usr->password,
        'email' => $_usr->email,
        'phone_number' => $_usr->phone_number,
    ];

    return $response->withStatus(200)->withJson($payload);
});

$app->post('/users', function ($request, $response, $args) {

    $user = new User();

    $_role = $request->getParsedBodyParam('role', '');
    $_last_name = $request->getParsedBodyParam('last_name', '');
    $_first_name = $request->getParsedBodyParam('first_name', '');
    $_password = $request->getParsedBodyParam('password', '');
    $_email = $request->getParsedBodyParam('email', '');
    $_phone_number = $request->getParsedBodyParam('phone_number', '');

    $user->role = $_role;
    $user->last_Name = $_last_name;
    $user->first_name = $_first_name;
    $user->password = $_password;
    $user->email = $_email;
    $user->phone_number = $_phone_number;
    $user->save();

    if ($user->user_id) {

        $payload = ['user_id' => $user->user_id,
            'user_uri' => '/user/' . $user->user_id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }

});

$app->patch('/users/{user_id}', function ($request, $response, $args) {
    $id = $args['user_id'];
    $user = User::findOrFail($id);

    $params = $request->getParsedBody();

    foreach ($params as $field => $value) {
        $user->$field = $value;
    }
    $user->save();

    if ($user->user_id) {
        $payload = ['user_id' => $user->user_id,
            'role' => $user->role,
            'last_name' => $user->last_name,
            'first_name' => $user->first_name,
            'password' => $user->password,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'user_uri' => '/users/' . $user->user_id
        ];
        return $response->withStatus(200)->withJson($payload);
    } else {
        return $response->withStatus(500);
    }

});

$app->delete('/users/{user_id}', function ($request, $response, $args) {
   $id = $args['user_id'];
   $user = User::find($id);
   $user->delete();

   if ($user->exists) {
       return $response->withStatus(500);
   } else {
       return $response->withStatus(204)->getBody()->write("User '/users/$id' has been deleted");
   }
});

$app->get('/providers/{provider_id}', function (Request $request, Response $response, array $args){
    $id = $args['provider_id'];
    $provider = new Provider();
    $_pvd = $provider->find($id);

    $payload[$_pvd->provider_id] = ['name' => $_pvd->name,
        'street' => $_pvd->street,
        'city' => $_pvd->city,
        'state' => $_pvd->state,
        'phone_number' => $_pvd->phone_number,
    ];

    return $response->withStatus(200)->withJson($payload);
});

$app->get('/providers', function ($request, $response, $args) {
    $count = Provider::count();
    $params = $request->getQueryParams();
    $limit = array_key_exists('limit', $params) ? (int)$params['limit'] : 10;
    //items per page
 $offset = array_key_exists('offset', $params) ? (int)$params['offset'] : 0;
 $term = array_key_exists('q', $params) ? $params['q'] : null;
if (!is_null($term)) {
    $providers = Provider::searchProviders($term);
    $payload_final = [];
    foreach ($providers as $_pvd) {
        $payload_final[$_pvd->provider_id] = [
            'name' => $_pvd->name,
            'street' => $_pvd->street,
            'city' => $_pvd->city,
            'state' => $_pvd->state,
            'phone_number' => $_pvd->phone_number
        ];
    }
} else {
    $links = Provider::getLinks($request, $limit, $offset);
    $sort_key_array = Provider::getSortKeys($request);
//    $query = Provider::with('comments');
    $query = Provider::skip($offset)->take($limit); // limit the rows
    foreach ($sort_key_array as $column => $direction) {
        $query->orderBy($column, $direction);
    }
    $providers = $query->get();
    $payload = [];
    foreach ($providers as $_pvd) {
        $payload[$_pvd->provider_id] = [
            'name' => $_pvd->name,
            'street' => $_pvd->street,
            'city' => $_pvd->city,
            'state' => $_pvd->state,
            'phone_number' => $_pvd->phone_number,
        ];
    }
    $payload_final = [
        'totalCount' => $count,
        'limit' => $limit,
        'offset' => $offset,
        'links' => $links,
        'sort' => $sort_key_array,
        'data' => $payload
    ];
}
   return $response->withStatus(200)->withJson($payload_final);
});


$app->get('/phones', function(Request $request, Response $response, array $args){
    $phone = Phone::all();

    $payload = [];

    foreach ($phone as $_phn){
        $payload[$_phn->phone_id] = ['provider_id' => $_phn->provider_id,
            'name' => $_phn->name,
            'brand' => $_phn->brand,
            'price' => $_phn->price,
        ];
    }
    return $response->withStatus(200)->withJson($payload);
});

$app->get('/phones/{phone_id}', function (Request $request, Response $response, array $args){
    $id = $args['phone_id'];
    $phone = new Phone();
    $_phn = $phone->find($id);


        $payload[$_phn->phone_id] = ['provider_id' => $_phn->provider_id,
            'name' => $_phn->name,
            'brand' => $_phn->brand,
            'price' => $_phn->price,
        ];

    return $response->withStatus(200)->withJson($payload);
});


$app->run();