<?php

namespace App\Http\Controllers\Auth;

use App\Http\Helpers\Services;
use App\Http\Helpers\Token;
use App\Http\Helpers\Validator;
use App\Models\Company;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Session as UserSession;
use App\Models\User\Exception\User as UserException;
use Log;

class PasswordController extends Controller
{
    CONST RESET_PASSWORD_PAGE = 'resetPassword';
    CONST EMPLOYEE_DASHBOARD_URL = 'employee-dashboard';
    CONST LOGIN_URL = 'login';

    private $user;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function resetPassPage(Request $request, $resetCode = null)
    {
        $data = array();
        $sessionInfo = array();
        $this->user = new User();
        if (null !== $resetCode) {
            try {
                $this->user->loadByResetCode($resetCode);
            } catch (UserException $e) {
                Log::error("resetPassPage: Error loading user. Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
                $this->setSessionMessage('auth.no_token_or_not_logged', self::MESSAGE_TYPE_DANGER);
                return redirect()->route(self::LOGIN_URL);
            }
            $data['url'] = route('reset-password', array('code' => $resetCode));
        } else {
            $sessionInfo = $request->session()->get(UserSession::NAME);
            try {
                $this->user->loadById($sessionInfo['id']);
            } catch (UserException $e) {
                Log::error("resetPassPage: Error loading user. Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
                $this->setSessionMessage('auth.no_token_or_not_logged', self::MESSAGE_TYPE_DANGER);
                return redirect()->route(self::LOGIN_URL);
            }
            $data['url'] = route('reset-password');
        }

        //if method is post reset password.
        if ($request->isMethod('post')) {
            $data['message'] = $this->resetPassword($request);
            if ($data['message']['type'] === self::MESSAGE_TYPE_SUCCESS) {
                $request->session()->forget(UserSession::NAME);
                $this->setSessionMessage('auth.password_reset', self::MESSAGE_TYPE_SUCCESS);
                return redirect()->route(self::LOGIN_URL);
            }
        }

        return $this->httpHelper->displayView(
            $sessionInfo,
            $data,
            self::RESET_PASSWORD_PAGE
        );
    }

    public function forgotPass(Request $request)
    {
        $input = $request->all();
        $user = new User();
        $sessionInfo = $request->session()->get(User\Session::NAME);
        $redirectUrl = self::LOGIN_URL;

        if (!isset($sessionInfo['id'])) {
            $validator = new Validator($input);
            //general validation for inputs
            $validatorResult = $validator->validate(array(
                'username' => array(Validator::REQUIRED)
            ));

            if (!empty($validatorResult)) {
                $this->setSessionMessage('validator.' . $validatorResult, self::MESSAGE_TYPE_DANGER);
                return redirect()->route(self::LOGIN_URL);
            }

            $user = new User();
            $username = $input['username'];
            try {
                $user->loadByUsername($username);
            } catch (UserException $e) {
                Log::error("forgotPass: Error loading user. Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
                $this->setSessionMessage('auth.pass_request_sent', self::MESSAGE_TYPE_DANGER);
                return redirect()->route(self::LOGIN_URL);
            }
        } else {
            $redirectUrl = self::EMPLOYEE_DASHBOARD_URL;
            try {
                $user->loadById($sessionInfo['id']);
            } catch (UserException $e) {
                Log::error("forgotPass: Error loading user. Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
                $this->setSessionMessage('auth.invalid_login', self::MESSAGE_TYPE_DANGER);
                return redirect()->route($redirectUrl);
            }
        }
        $toEmail = $user->getEmail();
        if (empty($toEmail)) {
            $this->setSessionMessage('auth.pass_request_sent', self::MESSAGE_TYPE_DANGER);
            return redirect()->route(self::LOGIN_URL);
        }

        $resetCode = (new Token())->generateRandomToken(User::TOKEN_LENGTH);
        $user->setResetToken($resetCode);
        if (false == $user->update()) {
            $this->setSessionMessage('auth.error_updating', self::MESSAGE_TYPE_DANGER);
            return redirect()->route(self::LOGIN_URL);
        };

        $companyEntity = new Company(true);
        $company = $companyEntity->getMainAddress();
        $company['name'] = $companyEntity->getPresentationName();
        $fromEmail = $company['email'];
        try {
            Services::getInstance()->getMailer()->send(
                'email.forgot_password',
                array(
                    "url" => route('reset-password', array('code' => $resetCode)),
                    "company" => $company,
                ),
                function ($message) use ($toEmail, $fromEmail) {
                    $message
                        ->to($toEmail)
                        ->from($fromEmail, $fromEmail)
                        ->subject(app('translator')->trans('emails.password.title'));
                }
            );
        } catch (\Exception $e) {
            Services::getInstance()->getLog()->error('Mail error: ' . $e->getFile() . ': ' . $e->getLine() . ' ' . $e->getMessage());
            $this->setSessionMessage('auth.pass_request_not_sent', self::MESSAGE_TYPE_DANGER);
            return redirect()->route(self::LOGIN_URL);
        }

        if (!isset($sessionInfo['id'])) {
            $this->setSessionMessage('auth.pass_request_sent', self::MESSAGE_TYPE_SUCCESS);
            return redirect()->route($redirectUrl);
        } else {
            $this->setSessionMessage('auth.pass_request_sent_logged', self::MESSAGE_TYPE_SUCCESS);
            return redirect()->route($redirectUrl);
        }
    }

    private function resetPassword($request)
    {
        $input = $request->all();
        $validator = new Validator($input);

        //general validation for inputs
        $validatorResult = $validator->validate(array(
            'password' => array(Validator::REQUIRED, [Validator::CHECK_MIN_LENGTH => User::PASSWORD_LENGTH]),
            'confirm_password' => array(Validator::REQUIRED),
        ));

        if (!empty($validatorResult)) {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'validator.' . $validatorResult);
        }

        if ($input['password'] !== $input['confirm_password']) {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.pass_dont_match');
        }

        if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $input['password']))
        {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.pass_not_alphanumeric');
        }

        if (!$this->user->isActivated()) {
            $this->user->activateAccount();
        }
        $this->user->setSalt($input['password']);
        $this->user->setResetToken(NULL);
        if (false == $this->user->update()) {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.reseting_pass');
        };

        return array('type' => self::MESSAGE_TYPE_SUCCESS, 'body' => 'auth.reseting_pass');
    }

}
