<?php
/**
 * Author: Matthew McGee
 * Date: 10/13/2021
 * File: Phone.php
 *Description:
 */

namespace Electronics\Models;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
}