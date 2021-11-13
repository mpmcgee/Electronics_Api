<?php
/**
 * Author: Matthew McGee
 * Date: 10/13/2021
 * File: bootstrap.php
 *Description:
 */

include __DIR__ .  '/config/credentials.php';
include __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

$capsule = new Capsule();
$capsule->addConnection([
    "driver" => "mysql",
    "host" => $db_host,
    "database" => $db_name,
    "username" => $db_user,
    "password" => $db_pass,
    "charset" => "utf8",
    "collation" => "utf8_general_ci",
    "prefix" => "" //this is optional.
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container = $app->getContainer();
$container['db'] = function($container)use($capsule){
    return $capsule;
};
