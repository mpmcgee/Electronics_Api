<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: ProviderController.php
 *Description:
 */

namespace Electronics\Controllers;
use Electronics\Models\Provider;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electronics\Models\Phone;

class ProviderController{
    //list all providers in database
    public function index(Request $request, Response $response, array $args){
        $results = Provider::getProviders();
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    //get single phone by id
    public function view(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = Provider::getProviderById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }



// Creating a provider
    public function create(Request $request, Response $response, array $args)
    {
        // Inserting a new provider
        $provider = Provider::createProvider($request);
        if ($provider->id) {
            $results = [
                'status' => 'Provider created',
                'provider_uri' => '/providers/' . $provider->id,
                'data' => $provider
            ];
            $code = 201;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Updating a provider
    public function update(Request $request, Response $response, array $args)
    {
        // Inserting a new provider
        $provider = Provider::updateProvider($request);
        if ($provider->provider_id) {
            $results = [
                'status' => 'Provider updated',
                'provider_uri' => '/providers/' . $provider->id,
                'data' => $provider
            ];
            $code = 200;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Deleting a provider
    public function delete(Request $request, Response $response, array $args)
    {
        $id = $request->getAttribute('id');
        Provider::deleteProvider($request);
        if (Provider::find($id)->exists) {
            return $response->withStatus(500);

        } else {
            $results = [
                'status' => "Provider '/providers/$id' has been deleted."
            ];
            return $response->withJson($results, 200, JSON_PRETTY_PRINT);
        }
    }
}