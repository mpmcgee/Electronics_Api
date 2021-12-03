<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: config.php
 *Description:
 */

return [
    /*
     * This option controls whether to display error details or not.
     * It should be set to true in the development environment.
     */
    'displayErrorDetails' => true,

    'addContentLengthHeader' => false,

    /*
     * This array contains database connection settings.
     */
    'db' => [
    'driver' => "mysql",
    'host' => 'localhost',
    'port' => 3307,
    'database' => 'electronics_api',
    'username' => 'phpuser',
    'password' => 'phpuser',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '' //this is optional
    ]
];
