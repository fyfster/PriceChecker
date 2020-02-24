<?php
namespace App\Models;


use Illuminate\Support\Collection;

class BaseModel
{
    CONST PERMISSIONS_TABLE = "permissions";
    CONST USER_PERMISSIONS_TABLE = "user_permissions";
    CONST USERS_TABLE = 'users';

    /**
     * @var \Illuminate\Database\Connection
     */
    protected $db;

    public function __construct(){
        $this->db = app()->make('db');
    }

    /**
     * Converts the result of a query (array with objects) into an array with arrays
     *
     * @param Collection $result result of a query
     * @param bool $isOneDimensional result given is one dimensional
     * @return array
     */
    protected function returnArrayFromColumns($result, $isOneDimensional = false)
    {
        if (empty($result)) {
            return array();
        }

        return json_decode(json_encode($result), true);
    }
}
