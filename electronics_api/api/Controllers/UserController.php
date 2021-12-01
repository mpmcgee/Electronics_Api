<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: UserController.php
 *Description:
 */

namespace Electronics\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electronics\Models\User;
use Electronics\Validations\Validator;
use Electronics\Models\Token;

class UserController
{
    //list all users in the database
    public function index(Request $request, Response $response, array $args)
    {
        $results = User::getUsers();
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    //get a user information by id
    public function view(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        $results = User::getUserById($id);
        $code = array_key_exists('status', $results) ? 500 : 200;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

//    //get all messages posted by a user
//    public function viewMessages(Request $request, Response $response, array $args)
//    {
//        $id = $args['id'];
//        $results = User::getMessagesByUser($id);
//        $code = array_key_exists('status', $results) ? 500 : 200;
//        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
//    }
//
//    //get all comments posted by a user
//    public function viewComments(Request $request, Response $response, array $args)
//    {
//        $id = $args['id'];
//        $results = User::getCommentByUser($id);
//        $code = array_key_exists('status', $results) ? 500 : 200;
//        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
//    }

    // Create a user when the user signs up an account
    public function create(Request $request, Response $response, array $args)
    {
        // Validate the request
        $validation = Validator::validateUser($request);

        // If validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return $response->withJson($results, 500, JSON_PRETTY_PRINT);
        }

        // Validation has passed; Proceed to create the user
        $user = User::createUser($request);
        $results = [
            'status' => 'user created',
            'data' => $user
        ];
        $code = array_key_exists('status', $results) ? 201 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Update a user
    public function update(Request $request, Response $response, array $args)
    {
        // Validate the request
        $validation = Validator::validateUser($request);

        // If validation failed
        if (!$validation) {
            $results = [
                'status' => "Validation failed",
                'errors' => Validator::getErrors()
            ];
            return $response->withJson($results, 500, JSON_PRETTY_PRINT);
        }

        // Validation has passed; Proceed to update the user
        $user = User::updateUser($request);
        $results = [
            'status' => 'user updated',
            'data' => $user
        ];
        $code = array_key_exists('status', $results) ? 200 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Delete a user
    public function delete(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        User::deleteUser($id);
        $results = [
            'status' => 'User deleted',
        ];
        $code = array_key_exists('status', $results) ? 200 : 500;
        return $response->withJson($results, $code, JSON_PRETTY_PRINT);
    }

    // Validate a user with username and password. It returns a Bearer token on success
    public function authBearer(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $username = $params['username'];
        $password = $params['password'];

        $user = User::authenticateUser($username, $password);
        if ($user) {
            $status_code = 200;
            $token = Token::generateBearer($user->id);
            $results = [
                'status' => 'login successful',
                'token' => $token
            ];
        } else {
            $status_code = 401;
            $results = [
                'status' => 'login failed'
            ];
        }
        return $response->withJson($results, $status_code, JSON_PRETTY_PRINT);
    }

    // Validate a user with username and password. It returns a JWT token on success.
    public function authJWT(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $username = $params['username'];
        $password = $params['password'];

        $user = User::authenticateUser($username, $password);

        if ($user) {
            $status_code = 200;
            $jwt = User::generateJWT($user->id);
            $results = [
                'status' => 'login successful',
                'jwt' => $jwt,
                'name' => $user->username
            ];
        } else {
            $status_code = 401;
            $results = [
                'status' => 'login failed',
            ];
        }

        //return $results;
        return $response->withJson($results, $status_code, JSON_PRETTY_PRINT);
    }
}