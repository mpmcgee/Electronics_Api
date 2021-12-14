<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: TVController.php
 *Description:
 */

namespace Electronics\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electronics\Models\TV;

class TVController {

    //list all TVs in database
    public function index(Request $request, Response $response, array $args){
        $results = TV::getTVs($request);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    //get single tv by id
    public function view(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = TV::getTvById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Create a tv when the user signs up an account
    public function create(Request $request, Response $response, array $args)
    {

        $tv = TV::createTV($request);
        $results = [
            'status' => 'tv created',
            'data' => $tv
        ];
        $code = array_key_exists('status', $results) ? 201 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Update a tv
    public function update(Request $request, Response $response, array $args)
    {
        // Inserting a new provider
        $tv = TV::updateTV($request);
        if ($tv->tv_id) {
            $results = [
                'status' => 'TV updated',
                'provider_uri' => '/tvs/' . $tv->id,
                'data' => $tv
            ];
            $code = 200;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Delete a tv
    public function delete(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        TV::deleteTV($id);
        $results = [
            'status' => 'TV deleted',
        ];
        $code = array_key_exists('status', $results) ? 200 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }
}