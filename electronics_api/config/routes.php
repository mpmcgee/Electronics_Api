<?php
/**
 * Author: Matthew McGee
 * Date: 10/13/2021
 * File: index.php
 *Description:
 */

use Electronics\Middleware\Logging as ElectronicsLogging;
use Electronics\Authentication\MyAuthenticator;
use Electronics\Authentication\BasicAuthenticator;
use Electronics\Authentication\BearerAuthenticator;
use Electronics\Models\User;
use Electronics\Models\TV;
use Electronics\Models\Provider;
use Electronics\models\Phone;
use Electronics\Authentication\JWTAuthenticator;


$app->get('/', function ($request, $response, $args) {
    return $response->write('Welcome to Electronics API!');
});

$app->get('/hello/{name}', function ($request, $response, $args) {
    return $response->write("Hello " . $args['name']);
});


// User routes
$app->group('/users', function () {
    $this->get('', 'UserController:index');
    $this->get('/{id}', 'UserController:view');
    $this->get('/{id}/messages', 'UserController:viewMessages');
    $this->get('/{id}/comments', 'UserController:viewComments');

    $this->post('', 'UserController:create');
    $this->put('/{id}', 'UserController:update');
    $this->patch('/{id}', 'UserController:update');
    $this->delete('/{id}', 'UserController:delete');

    $this->post('/authBearer', 'UserController:authBearer');
    $this->post('/authJWT', 'UserController:authJWT');
});
//})->add(new JWTAuthenticator());
// Route groups
$app->group('', function () {
    $this->group('/phones', function () {
        // The Message group

        $this->get('', 'PhoneController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'PhoneController:view');
        $this->get('/{id}/provider', 'PhoneController:viewProviders');
        $this->get('/{id}/user', 'PhoneController:viewUser');

        $this->post('', 'PhoneController:create');
        $this->put('/{id}', 'PhoneController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'PhoneController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'PhoneController:delete');
    });

    // The Provider group
    $this->group('/providers', function () {
        $this->get('', 'ProviderController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'ProviderController:view');
        $this->get('/{id}/user', 'ProviderController:viewUser');

        $this->post('', 'ProviderController:create');
        $this->put('/{id}', 'ProviderController:update');
        $this->patch('/{id}', 'ProviderController:update');
        $this->delete('/{id}', 'ProviderController:delete');
    });

    $this->group('/tvs', function () {
        // The Message group

        $this->get('', 'TVController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'TVController:view');
        $this->get('/{id}/provider', 'TVController:viewProviders');
        $this->get('/{id}/user', 'TVController:viewUser');

        $this->post('', 'TVController:create');
        $this->put('/{id}', 'TVController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
        $this->patch('/{id}', 'TVController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
        $this->delete('/{id}', 'TVController:delete');
    });


//})->add(new MyAuthenticator());
//})->add(new BasicAuthenticator());
//})->add(new BearerAuthenticator());
//})->add(new JWTAuthenticator());
});



//$app->add(new BasicAuthenticator());
//$app->add(new MyAuthenticator());
$app->add(new ElectronicsLogging());
$app->run();