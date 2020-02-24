<?php

namespace App\Models;

use App\Http\Helpers\Services;
use App\Models\User\Exception\User as UserException;
use Log;

class User extends BaseModel
{
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $phone;
    protected $userName;
    protected $registeredAt;
    protected $salt;
    protected $uuid;
    protected $address;
    protected $cityId;

    CONST PASSWORD_LENGTH = 8;
    CONST UUID_LENGTH = 8;
    CONST USERNAME_LENGTH = 5;
    CONST TOKEN_LENGTH = 100;

    /**
     * User constructor.
     * @param int $id value of id field
     *
     * @throws UserException
     */
    public function __construct($id = null)
    {
        parent::__construct();
        if (!empty($id)) {
            $this->loadBy('id', $id);
        }
    }

    /*
     * Getters
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getResetToken()
    {
        return $this->resetToken;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCityId()
    {
        return $this->cityId;
    }


    /*
     * Setters
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function setSalt($salt)
    {
        $this->salt =  hash('sha256', $salt);
    }

    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }

    /**
     * Loads an user model from db with the given field and value
     *
     * @param string $field - field to search by
     * @param string $value - value the column should have to identify user
     * @throws UserException
     */
    protected function loadBy($field, $value)
    {
        $query = $this->db->table(self::USERS_TABLE);

        $result = $query->where($field, $value)->first();

        if (!empty($result) && !empty($result->id)) {
            $this->id = $result->id;
            $this->firstName = $result->first_name;
            $this->lastName = $result->last_name;
            $this->email = $result->email;
            $this->phone = $result->phone;
            $this->salt = $result->salt;
            $this->userName = $result->username;
            $this->registeredAt = $result->registered_at;
            $this->resetToken = $result->reset_token;
            $this->uuid = $result->uuid;
            $this->avatar = $result->avatar;
            $this->address = $result->address;
            $this->cityId = $result->city_id;
        } else {
            throw new UserException('Could not load user by field ' . $field . ' with value ' . $value);
        }
    }

    /**
     * Loads an user model from db with the given id
     *
     * @param string $id - value for field id
     * @throws UserException
     */
    public function loadById($id)
    {
        $this->loadBy('id', $id);
    }

    /**
     * Loads an user model from db with the given email
     *
     * @param string $email - value for field email
     * @throws UserException
     */
    public function loadByEmail($email)
    {
        $this->loadBy('email', $email);
    }


    /**
     * Loads an user model from db with the given username
     *
     * @param string $username - value for field email
     * @throws UserException
     */
    public function loadByUsername($username)
    {
        $this->loadBy('username', $username);
    }

    /**
     * Loads an user model from db with the given resetCode
     *
     * @param string $resetCode - value for field resetCode
     * @throws UserException
     */
    public function loadByResetCode($resetCode)
    {
        $this->loadBy('reset_token', $resetCode);
    }

    /**
     * Checks if given password is valid
     *
     * @param string $password - password to be checked
     * @return bool
     */
    public function checkPassword($password)
    {
        return ($password === $this->salt);
    }

    /**
     * Checks if given email already exist in db
     *
     * @param string $email - email to be checked
     * @return bool
     */
    public function checkIfEmailInUse($email)
    {
        return $this->checkIfFieldInUse('email', $email);
    }

    /**
     * Checks if given username already exist in db
     *
     * @param string $username - username to be checked
     * @return bool
     */
    public function usernameInUse($username)
    {
        return $this->checkIfFieldInUse('username', $username);
    }

    /**
     * Checks if given uuid already exist in db
     *
     * @param string $uuid - uuid to be checked
     * @return bool
     */
    public function uuidInUse($uuid)
    {
        return $this->checkIfFieldInUse('uuid', $uuid);
    }

    /**
     * Checks if given field value combination already exist in db
     *
     * @param string $field - field to be checked
     * @param string $value - value of field to be checked
     * @return bool
     */
    private function checkIfFieldInUse($field, $value)
    {
        $result = $this->db
            ->table(self::USERS_TABLE)
            ->where($field, $value)
            ->select('id')
            ->first();

        return isset($result->id);
    }

    /**
     * Generates a random username
     *
     * @return string
     */
    public function generateUserName()
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randCode = '';
        for ($i = 0; $i < User::USERNAME_LENGTH; $i++) {
            $randCode .= $characters[rand(0, $charactersLength-1)];
        }
        $username = strtolower(substr($this->firstName, 0, 1)) . strtolower(substr($this->lastName, 0, 1)) . $randCode;

        if ($this->userNameInUse($username)) {
            $this->generateUserName();
        } else {
            return $username;
        }
    }

    /**
     * Generates a random username
     *
     * @return string
     */
    public function generateUuid()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randCode = '';
        for ($i = 0; $i < User::UUID_LENGTH; $i++) {
            $randCode .= $characters[rand(0, $charactersLength-1)];
        }

        if ($this->uuidInUse($randCode)) {
            $this->generateUuid();
        } else {
            return $randCode;
        }
    }

    /**
     * Saves the current user model information into database. If user is not inserted false will be returned
     *
     * @return bool - true => user inserted, false => error inserting
     */
    public function save()
    {
        try {
            $id = $this->db->table(self::USERS_TABLE)
                ->insertGetId(
                    array(
                        'username' => $this->userName,
                        'email' => $this->email,
                        'first_name' => $this->firstName,
                        'last_name' => $this->lastName,
                        'salt' => $this->salt,
                        'uuid' => $this->uuid,
                        'avatar' => $this->avatar,
                        'phone' => $this->phone,
                        'address' => $this->address,
                        'city_id' => $this->cityId,
                    )
                );
        } catch (\Exception $e) {
            Log::error('Error inserting user. Info - ' . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }

        $this->id = $id;
        return true;
    }

    /**
     * Updates user from database with the current user model information
     *
     * @return bool - 0 error while updating, 1 - updated
     */
    public function update()
    {
        try {
            $this->db->table(self::USERS_TABLE)
                ->where('id', $this->id)
                ->update(
                    array(
                        'username' => $this->userName,
                        'email' => $this->email,
                        'first_name' => $this->firstName,
                        'last_name' => $this->lastName,
                        'salt' => $this->salt,
                        'uuid' => $this->uuid,
                        'avatar' => $this->avatar,
                        'phone' => $this->phone,
                        'address' => $this->address,
                        'city_id' => $this->cityId,
                    )
                );
        } catch (\Exception $e) {
            Log::error('Error updating user with id '. $this->id .'. Info - ' . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }

        return true;
    }

    /**
     * remove user
     *
     * @return bool
     */
    public function remove()
    {
        $this->db->beginTransaction();
        $listOfTablesToDelete = array (
            self::USERS_TABLE => ['id'],
            self::USER_PERMISSIONS_TABLE => ['user_id'],
        );
        //go thru each related table and delete
        foreach ($listOfTablesToDelete as $deleteFromTable => $columns) {
            try {
                $query = $this->db->table($deleteFromTable);
                $query->where($columns[0], $this->id);
                $query->delete();
            } catch (\Exception $e) {
                Log::error("'User::remove error: " . $e->getMessage());
                $this->db->rollBack();
                return false;
            }
        }

        $this->db->commit();

        return true;
    }
}
