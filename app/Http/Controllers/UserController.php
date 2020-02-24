<?php

namespace App\Http\Controllers\Auth;

use App\Http\Helpers\File;
use App\Http\Helpers\Http;
use App\Http\Helpers\Services;
use App\Http\Helpers\Token;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\Validator;
use Log;

class UserController extends Controller
{
    private $messageInfo;
    private $user;

    /**
     * Create user request function
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function createUser(Request $request)
    {
        $data = array(
            "formRoute" => route(self::CREATE_USER_URL),
            "title" => "title_create_user"
        );

        $sessionInfo = $request->session()->get(User\Session::NAME);
        $country = new Country();
        $data["countries"] = $country->getAllCountries();
        //if method is post create user.
        if ($request->isMethod(Http::METHOD_POST)) {
            $data['message'] = $this->userCreate($request);
            if ($data['message']['type'] == self::MESSAGE_TYPE_DANGER) {
                $data['firstName'] = ($request->has('first_name'))? $request->get('first_name') : "";
                $data['lastName'] = ($request->has('last_name'))? $request->get('last_name') : "";
                $data['email'] = ($request->has('email'))? $request->get('email') : "";
                $data['phone'] = ($request->has('phone'))? $request->get('phone') : "";
                $data['description'] = ($request->has('description'))? $request->get('description') : "";
                $data['countryId'] = ($request->has('country'))? $request->get('country') : "";
                $data['countyId'] = ($request->has('county'))? $request->get('county') : "";
                $data['cityId'] = ($request->has('city'))? $request->get('city') : "";
                $data['address'] = ($request->has('address'))? $request->get('address') : "";
            } else {
                $this->setSessionMessage('common.success', static::MESSAGE_TYPE_SUCCESS);
                return redirect()->route(self::USER_ACTIVE_LIST_URL);
            }
        }

        $this->httpHelper->addSelectedToItem(self::CREATE_USER_URL);
        return $this->httpHelper->displayView(
            $sessionInfo,
            $data,
            self::CREATE_USER_PAGE
        );
    }

    /**
     * Create user post function
     *
     * @param Request $request
     * @return array
     */
    private function userCreate($request)
    {
        //general validation for inputs
        $input = $request->all();
        if (false == $this->generalUserValidation($input)) {
            return $this->messageInfo;
        }

        //check if email is already used in database
        $this->user = new User();
        if ($this->user->checkIfEmailInUse($input['email'])) {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.email_already_exists');
        }

        //check if file is valid
        if (false == $this->checkIfValidAddressCombination($input)) {
            return $this->messageInfo;
        }

        //check if file is valid
        if ($request->hasFile('avatar_file') && false == $this->checkIfValidFile($request)) {
            return $this->messageInfo;
        }

        $db = Services::getInstance()->getDb();
        $db->beginTransaction();

        $password = (new Token())->generateRandomToken(User::PASSWORD_LENGTH);
        //save user information and add it to db
        if (false == $this->setUserInfo($request, $password)) {
            $db->rollBack();
            return $this->messageInfo;
        }

        //send mail
        if (false == $this->sendMailToUser($this->user, $password)) {
            $db->rollBack();
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'setup.error_sending_mail_info');
        }
        $db->commit();

        return array(
            'type' => self::MESSAGE_TYPE_SUCCESS,
            'body' => 'user.success_create_employee'
        );
    }

    /**
     * General validation for user input
     *
     * @param array $input array with user info
     * @return bool
     */
    private function generalUserValidation($input)
    {
        $validator = new Validator($input);
        $validatorResult = $validator->validate(array(
            'first_name' => array(Validator::REQUIRED),
            'last_name' => array(Validator::REQUIRED),
            'email' => array(Validator::REQUIRED, Validator::CHECK_EMAIL),
            'country' => array(Validator::REQUIRED),
            'phone' => array(Validator::REQUIRED, Validator::CHECK_PHONE),
            'address' => array(Validator::REQUIRED)
        ));
        if (!empty($validatorResult)) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'validator.' . $validatorResult);
            return false;
        }

        if (!preg_match('/^[0-9\-\(\)\/\+\s]*$/', $input["phone"])) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'validator.wrong_phone_format');
            return false;
        }

        return true;
    }

    /**
     * Check if file sent is valid
     *
     * @param Request $request
     * @return bool
     */
    private function checkIfValidFile($request)
    {
        $configService = Services::getInstance()->getConfig()->get('general');
        $file = $request->file('avatar_file');
        $extension = $file->extension();
        $allowedTypes = $configService['files']['allowed_types'];
        if (!in_array($extension, $allowedTypes)) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.invalid_file_extension');
            return false;
        }
        if ($file->getError() == UPLOAD_ERR_INI_SIZE) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.file_too_big');
            return false;
        }

        $fileValidation = (new File())->validateImage($file);
        if (true !== $fileValidation) {
            Log::error("File validation error: " . $fileValidation);
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.error_invalid_file');
            return false;
        }
        return true;
    }

    /**
     * Check if city coresponds to county and country
     *
     * @param array $input
     * @return bool
     */
    private function checkIfValidAddressCombination($input)
    {
        $country = new Country();
        $input['city'] = isset($input['city']) ? $input['city'] : null;
        if ($input['city'] != null && false == $country->checkAddressCombination($input['country'], $input['county'], $input['city'])) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.error_address_combination');
            return false;
        }
        return true;
    }

    /**
     * Set user info to user model and save in DB
     *
     * @param Request $request
     * @return bool
     */
    private function setUserInfo($request, $password)
    {
        $input = $request->all();
        $uuid = $this->user->generateUuid();

        // Save avatar, if this is the first upload by this user, create folders
        if ($request->hasFile('avatar_file')) {
            $this->saveFile($request->file('avatar_file'));
        }

        //set user model properties
        $this->user->setFirstName($input['first_name']);
        $this->user->setLastName($input['last_name']);

        //generate username by fisrt and last name
        $username = $this->user->generateUserName();

        //save user info
        $this->user->setEmail($input['email']);
        $this->user->setPhone($input['phone']);
        $this->user->setUserName($username);
        $this->user->setUuid($uuid);
        $this->user->setSalt($password);
        $this->user->setAddress($input['address']);
        $this->user->setCityId($input['city']);

        if (false === $this->user->save()) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.error_inserting_user');
            return false;
        }

        if (false === (new User\Permission())->insertUserPermissions(["permission_id" => User\Permission::PERMISSION_EMPLOYEE_ID, "user_id" => $this->user->getId()])) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'user.error_inserting_permissions');
            return false;
        }

        return true;
    }

    /**
     * Save usewr profile picture
     *
     * @param File $file
     * @return bool
     */
    private function saveFile($file)
    {
        $configService = Services::getInstance()->getConfig()->get('general');
        $extension = $file->extension();
        $uploadPath = $configService['files']['avatar_upload_path'];
        $filePermissions = $configService['files']['uploaded_file_permissions'];
        $fileService = Services::getInstance()->getFile();
        if (!$fileService->isDirectory($uploadPath)) {
            $fileService->makeDirectory($uploadPath, $filePermissions, true);
        }
        $fileService->put(
            $uploadPath . '/' . $uuid . '.' . $extension,
            $fileService->get($file)
        );
        $this->user->setAvatar($uuid . '.' . $extension);
        return true;
    }

    private function sendMailToUser($user, $password)
    {
        $configService = Services::getInstance()->getConfig()->get('general');
        $fromEmail = $configService['default_from_email'];
        $toEmail = $user->getEmail();
        try {
            Services::getInstance()->getMailer()->send(
                'email.initial_setup',
                array(
                    "username" => $user->getUserName(),
                    "password" => $password,
                    "url" => route('login'),
                ),
                function ($message) use ($toEmail, $fromEmail) {
                    $message
                        ->to($toEmail)
                        ->from($fromEmail, $fromEmail)
                        ->subject(app('translator')->trans('emails.user.title'));
                }
            );
        } catch (\Exception $e) {
            Log::error("sendMailToUser: Error sending mail. Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }
        return true;
    }

}
