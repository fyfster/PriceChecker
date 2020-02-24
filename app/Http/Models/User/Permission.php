<?php

namespace App\Models\User;
use App\Models\BaseModel;
use App\Models\User;
use App\Models\User\Exception\Permission as PermissionException;
use Log;

class Permission extends BaseModel
{
    CONST PERMISSION_EMPLOYEE_ID = 2;
    
    private $id;
    private $name;

    /**
     * Permission constructor.
     * @param int $id value of id field
     *
     * @throws PermissionException
     */
    public function __construct($id = null)
    {
        parent::__construct();
        if (!empty($id)) {
            $this->loadBy("id", $id);
        }
    }

    /*
     * Getters
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /*
     * Setters
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Loads a permission model from db with the given field and value
     *
     * @param string $field - field to search by
     * @param string $value - value the column should have to identify permission
     * @throws PermissionException
     */
    private function LoadBy($field, $value)
    {
        $result = $this->db
            ->table(self::PERMISSIONS_TABLE)
            ->where($field, $value)
            ->first();

        if (!empty($result) && !empty($result->id)) {
            $this->id = $result->id;
            $this->name = $result->name;
        } else {
            throw new PermissionException("Could not load permission by field " . $field . " with value " . $value);
        }
    }

    /**
     * Loads a permission model from db with the given name
     *
     * @param string $id - value for field id
     * @throws PermissionException
     */
    public function loadById($id)
    {
        $this->loadBy("id", $id);
    }

    /**
     * Loads a permission model from db with the given id
     *
     * @param string $name - value for field name
     * @throws PermissionException
     */
    public function loadByName($name)
    {
        $this->loadBy("name", $name);
    }

    /**
     * Inserts values into the user_permissions table
     * @param array $insertInfo array with insert information
     *
     * @return bool
     */
    public function insertUserPermissions($insertInfo)
    {
        try {
            $this->db->table(self::USER_PERMISSIONS_TABLE)
                ->insert($insertInfo);
        } catch (\Exception $e) {
            Log::error('Error inserting user_permissions. Info - ' . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }

        return true;
    }

    /**
     * Delete ol user permissions
     * @param int $userId
     *
     * @return bool
     */
    public function deleteUserPermissions($userId)
    {
        try {
            $this->db->table(self::USER_PERMISSIONS_TABLE)
                ->where('user_id', $userId)
                ->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting user_permissions. Info - ' . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }

        return true;
    }
}
