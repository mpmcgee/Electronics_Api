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

// Route groups
$app->group('', function () {
    $this->group('/phones', function () {
        // The Message group

        $this->get('', 'PhoneController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'PhoneController:view');
        $this->get('/{id}/provider', 'PhoneController:viewProviders');
        $this->get('/{id}/user', 'PhoneController:viewUser');

//        $this->post('', 'MessageController:create');
//        $this->put('/{id}', 'MessageController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
//        $this->patch('/{id}', 'MessageController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
//        $this->delete('/{id}', 'MessageController:delete');
    });

    // The Provider group
    $this->group('/providers', function () {
        $this->get('', 'ProviderController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'ProviderController:view');
        $this->get('/{id}/user', 'ProviderController:viewUser');

        //TODO: Post needs CUD
//        $this->post('', 'CommentController:create');
//        $this->put('/{id}', 'CommentController:update');
//        $this->patch('/{id}', 'CommentController:update');
//        $this->delete('/{id}', 'CommentController:delete');
    });

    $this->group('/tvs', function () {
        // The Message group

        $this->get('', 'TVController:index'); // "Class" is registered in DIC
        $this->get('/{id}', 'TVController:view');
        $this->get('/{id}/provider', 'TVController:viewProviders');
        $this->get('/{id}/user', 'TVController:viewUser');

//        $this->post('', 'MessageController:create');
//        $this->put('/{id}', 'MessageController:update');//Postman PUT Boyd with x-www-form-urlencoded to send new information.
//        $this->patch('/{id}', 'MessageController:update');//Postman PATCH Boyd with x-www-form-urlencoded to send new information.
//        $this->delete('/{id}', 'MessageController:delete');
    });


//})->add(new MyAuthenticator());
//})->add(new BasicAuthenticator());
//})->add(new BearerAuthenticator());
})->add(new JWTAuthenticator());


//$app->add(new MyAuthenticator());
//$app->add(new BasicAuthenticator());
//$app->add(new BearerAuthenticator());

//})->add(new MyAuthenticator());
//})->add(new BasicAuthenticator());
//})->add(new BearerAuthenticator());
//})->add(new JWTAuthenticator()); // this was used.
//})->add(new OAuth2Authenticator());  // Needs to test it in a browser, but not in Postman

$app->add(new BasicAuthenticator());
//$app->add(new MyAuthenticator());
$app->add(new ElectronicsLogging());
$app->run();