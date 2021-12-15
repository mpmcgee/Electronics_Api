<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: PhoneController.php
 *Description:
 */

namespace Electronics\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electronics\Models\Phone;


class PhoneController {

    //list all phones in database
    public function index(Request $request, Response $response, array $args){
        $results = Phone::getPhones($request);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    //get single phone by id
    public function view(Request $request, Response $response, array $args){
        $id = $args['id'];
        $results = Phone::getPhoneById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }
// Create a message
    public function create(Request $request, Response $response, array $args)
    {
        // Insert a new phone
        $phone = Phone::createPhones($request);
        if ($phone->phone_id) {
            $results = [
                'status' => 'Phone created',
                'phone_uri' => '/phones/' . $phone->phone_id,
                'data' => $phone
            ];
            $code = 201;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Update a phone
    public function update(Request $request, Response $response, array $args)
    {
        // Insert a new phone
        $phone = Phone::updatePhone($request);
        if ($phone->phone_id) {
            $results = [
                'status' => 'Phone updated',
                'phone_uri' => '/phones/' . $phone->id,
                'data' => $phone
            ];
            $code = 200;
        } else {
            $code = 500;
        }

        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Delete a phone
    public function delete(Request $request, Response $response, array $args)
    {
        $id = $request->getAttribute('id');
        Phone::deletePhones($request);
        if (Phone::find($id)->exists) {
            return $response->withStatus(500);

        } else {
            $results = [
                'status' => "Phone '/phones/$id' has been deleted."
            ];
            return $response->withJson($results, 200, JSON_PRETTY_PRINT);
        }
    }
}