<?php
/**
 * Author: Matthew McGee
 * Date: 10/13/2021
 * File: Phone.php
 *Description:
 */

namespace Electronics\Models;

class Phone extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'phone';
    protected $primaryKey = 'phone_id';
    public $timestamps = false;
}