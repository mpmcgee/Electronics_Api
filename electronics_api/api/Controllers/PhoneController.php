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
        $results = Phone::getPhones();
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    //get single phone by id
    public function view(Request $request, Response $response, array $args){
        $id = $args['phone_id'];
        $results = Phone::getPhoneById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

}