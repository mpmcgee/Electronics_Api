<?php
/**
 * Author: Matthew McGee
 * Date: 12/1/2021
 * File: User.php
 *Description:
 */

namespace Electronics\Models;

use \Illuminate\Database\Eloquent\Model;
use Firebase\JWT\JWT;

class User extends Model {
    const JWT_KEY = 'my signature';
    const JWT_EXPIRE = 600;
    // The table associated with this model
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    //map the one-to-many relationship
    public function phones()
    {
        return $this->hasMany(Phone::class, 'user_id');
    }

    //map the one-to-many relationship
    public function TVs()
    {
        return $this->hasMany(TV::class, 'user_id');
    }

    //get all users
    public static function getUsers()
    {
        //all() methdod only retrieves the comments.
        $users = self::all();
        return $users;
    }

    //get a user by id
    public static function getUserById($id)
    {
        $user = self::findOrFail($id);
        return $user;
    }

    //get all messages post by a user
    public static function getMessagesByUser($id)
    {
        $messages = self::findOrFail($id)->messages;
        return $messages;
    }

    //get all comments posted by a user
    public static function getCommentByUser($id)
    {
        $comments = self::findOrFail($id)->comments;
        return $comments;
    }

    // Create a new user
    public static function createUser($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        // Create a new User instance
        $user = new \Electronics\Models\User();

        // Set the user's attributes
        foreach ($params as $field => $value) {

            // Need to hash password
            if ($field == 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }

            $user->$field = $value;
        }

        // Insert the user into the database
        $user->save();
        return $user;
    }

    // Update a user
    public static function updateUser($request)
    {
        // Retrieve parameters from request body
        $params = $request->getParsedBody();

        //Retrieve the user's id from url and then the user from the database
        $id = $request->getAttribute('user_id');
        $user = self::findOrFail($id);

        // Update attributes of the professor
        $user->email = $params['email'];
        $user->username = $params['username'];
        $user->password = password_hash($params['password'], PASSWORD_DEFAULT);

        // Update the professor
        $user->save();
        return $user;
    }

    // Delete a user
    public static function deleteUser($id)
    {
        $user = self::findOrFail($id);
        return ($user->delete());
    }

    /************************************ JWT Authentication ***************************************/

    /*
     * Generate a JWT token.
     * The signature secret rule: the secret must be at least 12 characters in length;
     * contain numbers; upper and lowercase letters; and one of the following special characters *&!@%^#$.
     * For more details, please visit https://github.com/RobDWaller/ReallySimpleJWT
     */
    public static function generateJWT($id)
    {
        // Data for payload
        $user = $user = self::findOrFail($id);
        if (!$user) {
            return false;
        }

        $key = self::JWT_KEY;
        $expiration = time() + self::JWT_EXPIRE;
        $issuer = 'myelectronics-api.com';

        $token = [
            'iss' => $issuer,
            'exp' => $expiration,
            'isa' => time(),
            'data' => [
                'uid' => $id,
                'name' => $user->username,
                'email' => $user->email,
            ]
        ];
        // Generate and return a token
        return JWT::encode(
            $token,   // data to be encoded in the JWT
            $key,    // the signing key
            'HS256'   // algorithm used to sign the token; defaults to HS256
        );
        // return Token::create($userId, $secret, $expiration, $issuer);
    }

    // Verify a token
    public static function validateJWT($token)
    {
        $decoded = JWT::decode($token, self::JWT_KEY, array('HS256'));
        // print_r($decoded); exit;
        return $decoded;

    }

    /************************************ User Authentication ***************************************/

    // Authenticate a user by username and password. Return the user.
    public static function authenticateUser($username, $password)
    {
        $user = self::where('username', $username)->first();
        if (!$user) {
            return false;
        }
        return password_verify($password, $user->password) ? $user : false;
    }

}