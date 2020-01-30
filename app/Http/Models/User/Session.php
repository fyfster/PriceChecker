<?php

namespace App\Models\User;

use App\Http\Helpers\Services;
use App\Models\Company;
use App\Models\User;
use App\Models\User\Exception\User as UserException;
use Log;

class Session extends User
{
    CONST NAME = "user";

    private $session;
    /**
     * Session constructor.
     *
     */
    public function __construct($id = null)
    {
        $this->session = Services::getInstance()->getSession();
        parent::__construct($id);
    }

    /**
     * Checks if username and password are valid and adds user info to session
     *
     * @param string $username - value for user username
     * @param string $password - value for user password
     * @return bool
     */
    public function login($username, $password)
    {
        try {
            $this->loadByUsername($username);
        } catch (UserException $e) {
            Log::error('SessionUser::login error: ' . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }

        if ($this->checkPassword(hash('sha256' ,$password))) {
            $userData = array(
                'username' => $this->userName,
                'id' => $this->id
            );
            $this->session->put(Session::NAME, $userData);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Clears the seesion info
     *
     */
    public function isLoggedIn()
    {
        return $this->session->has(Session::NAME) ? true : false;
    }

}
