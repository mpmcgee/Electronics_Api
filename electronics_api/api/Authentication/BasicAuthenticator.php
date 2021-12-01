<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: BasicAuthenticator.php
 *Description:
 */

namespace Electronics\Authentication;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Electronics\Models\User;



class BasicAuthenticator {
    public function __invoke(Request $request, Response $response, $next)
    {
        // If the header named "Authorization" does not exist, display an error
        if (!$request->hasHeader('Authorization')) {
            $results = array('status' => 'Authorization header not available');
            return $response->withJson($results, 404, JSON_PRETTY_PRINT);
        }

        // Authorization header exists, retrieve the header and the header value
        $auth = $request->getHeader('Authorization');

        /* The value of the authorization header consists of Basic and the key separated
        * by a space. The key is a base64 encoded string: "username:password".
        */
        $apikey = substr($auth[0], strpos($auth[0], ' ') + 1); // the key is the second part of the string after a space

        // Get the username and password. The key needs to be decoded first.
        list($user, $password) = explode(':', base64_decode($apikey));

        // Authenticate the user
        if (!User::authenticateUser($user, $password)) {
            $results = array('status' => 'Authentication failed');
            return $response->withHeader('WWW-Authenticate', 'Basic realm="MyChatter API"')
                ->withJson($results, 401, JSON_PRETTY_PRINT);
        }
        // Proceed since the user has been authenticated.
        $response = $next($request, $response);
        return $response;
    }
}