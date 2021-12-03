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
        $results = TV::getTVs();
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    //get single tv by id
    public function view(Request $request, Response $response, array $args){
        $id = $args['tv_id'];
        $results = TV::getTvById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Create a tv when the user signs up an account
    public function create(Request $request, Response $response, array $args)
    {
        // Validate the request
        $validation = Validator::validateTV($request);

        // If validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return $response->withJson($results, 500, JSON_PRETTY_PRINT);
        }

        // Validation has passed; Proceed to create the tv
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
        // Validate the request
        $validation = Validator::validateTV($request);

        // If validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return $response->withJson($results, 500, JSON_PRETTY_PRINT);
        }

        // Validation has passed; Proceed to update the tv
        $tv = TV::updateTV($request);
        $results = [
            'status' => 'tv updated',
            'data' => $tv
        ];
        $code = array_key_exists('status', $results) ? 200 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Delete a tv
    public function delete(Request $request, Response $response, array $args)
    {
        $tv_id = $args['tv_id'];
        TV::deleteTV($tv_id);
        $results = [
            'status' => 'TV deleted',
        ];
        $code = array_key_exists('status', $results) ? 200 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }
}