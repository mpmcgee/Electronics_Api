<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: Logging.php
 *Description:
 */

namespace Electronics\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class Logging
{
    public function __invoke(Request $request, Response $response, $next)
    {
        error_log($request->getMethod() . " -- " . $request->getUri());
        $response = $next($request, $response);

        return $response;
    }
}
