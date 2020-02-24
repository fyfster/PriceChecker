<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Validator;
use App\Models\User;
use App\Models\User\Session as UserSession;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $user;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * Loads the login page
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function loginPage(Request $request)
    {
        $sessionInfo = $request->session()->get(UserSession::NAME);
        $user = new UserSession();
        if ($user->isLoggedIn()) {
            $user->loadById($sessionInfo['id']);
            return redirect()->route('dashboard');
        }

        $data = array();
        if ($request->session()->has('message')) {
            $data['message'] = $request->session()->get('message');
        }

        if ($request->session()->has('input')) {
            $data['username'] = $request->session()->get('input')['username'];
        }

        return $this->httpHelper->displayView(
            $sessionInfo,
            $data
        );
    }

    /**
     * Checks the login input and sets the session
     * @param \Illuminate\Http\Request $request
     * @redirect to dashboard page or login page if errors occur
     */
    public function loginPost(Request $request)
    {
        $user = new UserSession();
        if ($user->isLoggedIn()) {
            $sessionInfo = $request->session()->get(UserSession::NAME);
            $user->loadById($sessionInfo['id']);
            return redirect()->route('dashboard');
        }

        $validatorResult = $this->validator->validate(array(
            'username' => array(Validator::REQUIRED),
            'password' => array(Validator::REQUIRED, [Validator::CHECK_MIN_LENGTH => User::PASSWORD_LENGTH]),
        ));
        if (!empty($validatorResult)) {
            if($request->has('username')) {
                $request->session()->flash('input', array('username' => $request->get('username')));
            }
            $this->setSessionMessage('auth.' . $validatorResult, self::MESSAGE_TYPE_DANGER);
            return redirect(self::LOGIN_URL);
        }

        if (!$user->login($request->get('username'), $request->get('password'))) {
            $request->session()->flash('input', array('username' => $request->get('username')));
            $this->setSessionMessage('auth.invalid_login', self::MESSAGE_TYPE_DANGER);
            return redirect()->route(self::LOGIN_URL);
        }

        return redirect()->route('dashboard');
    }

    /**
     * Logging out the user (clear session)
     * @param \Illuminate\Http\Request $request
     * @redirect to login page
     */
    public function logout(Request $request)
    {
        $request->session()->forget(UserSession::NAME);
        $this->setSessionMessage('auth.success_logout', static::MESSAGE_TYPE_SUCCESS);
        return redirect()->route(self::LOGIN_URL);
    }
}
