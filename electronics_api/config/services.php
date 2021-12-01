<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: services.php
 *Description:
 */

// Alias to the controllers
use Electronics\Controllers\UserController as UserController;
use Electronics\Controllers\PhoneController as PhoneController;
use Electronics\Controllers\TVController as TVController;
use Electronics\Controllers\ProviderController as ProviderController;

/*
 * The following is the controller and middleware factory. It
 * registers controllers and middleware with the DI container so
 * they can be accessed in other classes. Injecting instances into
 * the DI container so you don't need to pass the entire container or app,
 * rather only the services needed.
 * https://akrabat.com/accessing-services-in-slim-3/#comment-35429
 */
// Register controllers with the DIC. $c is the container itself.
$container['UserController'] = function ($c) {
    return new UserController();
};

$container['PhoneController'] = function ($c) {
    return new PhoneController();
};

$container['TVController'] = function ($c) {
    return new TVController();
};

$container['ProviderController'] = function ($c) {
    return new ProviderController();
};
