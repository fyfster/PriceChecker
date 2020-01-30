<?php

namespace App\Http\Controllers\Auth;

use App\Http\Helpers\File;
use App\Http\Helpers\Http;
use App\Http\Helpers\Services;
use App\Http\Helpers\Token;
use App\Http\Helpers\Variables;
use App\Models\Company;
use App\Models\Country;
use App\Models\FID;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\User;
use App\Models\User\IDs as UserIDs;
use App\Models\User\Exception\IDs as UserIDsException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Validator;
use Log;

class RegisterController extends Controller
{
    CONST EMPLOYEE_CREATE_PAGE = 'user/employeeForm';
    CONST EMPLOYEE_CREATE_URL = 'create-employee';
    CONST FID_CREATE_PAGE = 'fid/fidForm';
    CONST FID_CREATE_URL = 'create-fid';
    CONST EMPLOYEE_ACTIVE_LIST_URL = 'employee-active-list';
    CONST FID_ACTIVE_LIST_URL = 'fid-list';

    private $messageInfo;
    private $user;
    private $fid;

    /**
     * Create user request function
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function createEmployee(Request $request)
    {
        $data = array(
            "formRoute" => route(self::EMPLOYEE_CREATE_URL),
            "title" => "title_create_employee"
        );

        $sessionInfo = $request->session()->get(User\Session::NAME);
        $country = new Country();
        $data["countries"] = $country->getAllCountries();
        $data["rolePermissions"] = (new User\Role())->getAllRolesAndPermissions();
        $data["permissionList"] = (new User\Role())->getAllPermissionsWithRoleIds();
        //if method is post create user.
        if ($request->isMethod(Http::METHOD_POST)) {
            $data['message'] = $this->employeeCreate($request);
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
                $data['permissions'] = ($request->has('permissions'))? $request->get('permissions') : array();
                $data['roles'] = ($request->has('roles'))? $request->get('roles') : array();
                $data['showInPresentation'] = ($request->has('show_in_presentation'))? $request->get('show_in_presentation') : array();
            } else {
                $this->setSessionMessage('common.success', static::MESSAGE_TYPE_SUCCESS);
                return redirect()->route(self::EMPLOYEE_ACTIVE_LIST_URL);
            }
        }

        $this->httpHelper->addSelectedToItem(self::EMPLOYEE_CREATE_URL);
        return $this->httpHelper->displayView(
            $sessionInfo,
            $data,
            self::EMPLOYEE_CREATE_PAGE
        );
    }

    /**
     * Create employee request function
     *
     * @param Request $request
     * @return array
     */
    private function employeeCreate($request)
    {
        //general validation for inputs
        $input = $request->all();
        if (false == $this->generalEmployeeValidation($input)) {
            return $this->messageInfo;
        }

        //check if email is already used in database
        $this->user = new User();
        if ($this->user->checkIfEmailInUse($input['email'])) {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.email_already_exists');
        }

        //check if file is valid
        if (false == $this->checkIfValidFile($request)) {
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

        if (false == $this->associateUserPermissions("cdms", $this->user)) {

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
            'body' => 'auth.success_create_employee'
        );
    }

    /**
     * Create FID request function
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function createFID(Request $request)
    {
        $data = array(
            "formRoute" => route(self::FID_CREATE_URL),
            "title" => "title_create_fid",
            "useSelect2" => 1,
            "useDatePicker" => 1,
            "periods" => array(6, 12, 24),
            'coinUnit' => Variables::getInstance()->getPriceUnit()
        );
        $sessionInfo = $request->session()->get(User\Session::NAME);
        $country = new Country();
        $data["countries"] = $country->getAllCountries();

        //if method is post create user.
        if ($request->isMethod(Http::METHOD_POST)) {
            $data['message'] = $this->FIDCreate($request);
            if ($data['message']['type'] == self::MESSAGE_TYPE_DANGER) {
                $data['legalName'] = ($request->has('legal_name'))? $request->get('legal_name') : "";
                $data['email'] = ($request->has('email'))? $request->get('email') : "";
                $data['phone'] = ($request->has('phone'))? $request->get('phone') : "";
                $data['countryId'] = ($request->has('country'))? $request->get('country') : "";
                $data['countyId'] = ($request->has('county'))? $request->get('county') : "";
                $data['cityId'] = ($request->has('city'))? $request->get('city') : "";
                $data['address'] = ($request->has('address'))? $request->get('address') : "";
                $data['cityId'] = ($request->has('city'))? $request->get('city') : "";
                $data['cui'] = ($request->has('cui'))? $request->get('cui') : "";
                $data['iban'] = ($request->has('iban'))? $request->get('iban') : "";
                $data['regNr'] = ($request->has('reg_nr'))? $request->get('reg_nr') : "";
                $data['subscriptionPrice'] = ($request->has('subscription_price'))? $request->get('subscription_price') : "";
                $data['subscriptionName'] = ($request->has('subscription_name'))? $request->get('subscription_name') : "";
                $data['subscriptionDescription'] = ($request->has('subscription_description'))? $request->get('subscription_description') : "";
                $data['subscriptionStart'] = ($request->has('subscription_start')) ? $request->get('subscription_start') : "";
                $data['subscriptionEnd'] = ($request->has('subscription_end')) ? $request->get('subscription_end') : "";
            } else {
                $this->setSessionMessage('common.success', static::MESSAGE_TYPE_SUCCESS);
                return redirect()->route(self::FID_ACTIVE_LIST_URL);
            }
        }

        $this->httpHelper->addSelectedToItem(self::FID_CREATE_URL);
        return $this->httpHelper->displayView(
            $sessionInfo,
            $data,
            self::FID_CREATE_PAGE
        );
    }

    /**
     * Create FID request function
     *
     * @param Request $request
     * @return array
     */
    private function FIDCreate($request)
    {
        //general validation for inputs
        $input = $request->all();
        if (false == $this->generalFIDValidation($input)) {
            return $this->messageInfo;
        }

        //check if email is already used in database
        $this->fid = new FID();
        if ($this->fid->checkIfEmailInUse($input['email'])) {
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.email_already_exists');
        }

        $db = Services::getInstance()->getDb();
        $db->beginTransaction();

        //save user information and add it to db
        if (false == $this->setFIDInfo($request)) {
            $db->rollBack();
            return $this->messageInfo;
        }

        if (false == $this->setFIDSubscription(
                $input['subscription_price'],
                $input['subscription_start'],
                $input['subscription_end'],
                $input['subscription_name'],
                $input['subscription_description']
            )) {
            $db->rollBack();
            return $this->messageInfo;
        }

        $invoice = new Invoice();
        $subscription = new Subscription();
        if (false == $invoice->generateSubscriptionInvoice(
                $subscription->getSubscriptionForFID($this->fid->getId()),
                $this->fid->getId()
            )) {
            $db->rollBack();
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'fid.error_generating_subscription_invoice');
        }

        $subscriptionInfo = $subscription->getSubscriptionInfoForFID($this->fid->getId());
        if (false == $subscription->updateSubscriptionLastInvoiceId(
                $subscriptionInfo['user_subscription_id'],
                $invoice->getId()
            )){
            $db->rollBack();
            return array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'fid.error_inserting_subscription');
        }

        $db->commit();

        return array(
            'type' => self::MESSAGE_TYPE_SUCCESS,
            'body' => 'auth.success_create_fid'
        );
    }

    /**
     * General validation for user input
     *
     * @param array $input array with user info
     * @return bool
     */
    private function generalFIDValidation($input)
    {
        $validator = new Validator($input);
        $validatorResult = $validator->validate(array(
            'legal_name' => array(Validator::REQUIRED),
            'email' => array(Validator::REQUIRED, Validator::CHECK_EMAIL),
            'country' => array(Validator::REQUIRED),
            'phone' => array(Validator::REQUIRED, Validator::CHECK_PHONE),
            'address' => array(Validator::REQUIRED),
            'cui' => array(Validator::REQUIRED),
            'iban' => array(Validator::REQUIRED),
            'subscription_name' => array(Validator::REQUIRED),
            'subscription_price' => array(Validator::REQUIRED),
            'subscription_start' => array(Validator::REQUIRED),
            'subscription_end' => array(Validator::REQUIRED),
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
     * General validation for user input
     *
     * @param array $input array with user info
     * @return bool
     */
    private function generalEmployeeValidation($input)
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
        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $extension = $file->extension();
            $allowedTypes = $configService['files']['allowed_types'];
            if (!in_array($extension, $allowedTypes)) {
                $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.invalid_file_extension');
                return false;
            }
            if ($file->getError() == UPLOAD_ERR_INI_SIZE) {
                $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.file_too_big');
                return false;
            }
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
            $configService = Services::getInstance()->getConfig()->get('general');
            $file = $request->file('avatar_file');
            $fileValidation = (new File())->validateImage($file);
            if (true !== $fileValidation) {
                Log::error("File validation error: " . $fileValidation);
                $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.error_invalid_file');
                return false;
            }


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
        }

        $db = Services::getInstance()->getDb();
        $db->beginTransaction();

        //check if city coresponds to county and country
        $country = new Country();
        $input['city'] = isset($input['city']) ? $input['city'] : null;
        if ($input['city'] != null && false == $country->checkAddressCombination($input['country'], $input['county'], $input['city'])) {
            $db->rollBack();
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'company.error_address_combination');
        }

        //set user model properties
        $this->user->setFirstName($input['first_name']);
        $this->user->setLastName($input['last_name']);

        //generate username by fisrt and last name
        $username = $this->user->generateUserName();

        //save user info
        $this->user->setEmail($input['email']);
        $this->user->setPhone($input['phone']);
        $this->user->setParentId($request->session()->get(User\Session::NAME)["id"]);
        $this->user->setUserName($username);
        $this->user->setUuid($uuid);
        $this->user->setSalt($password);
        $this->user->setAddress($input['address']);
        $this->user->setCityId($input['city']);
        $this->user->setCountryId($input['country']);

        if (false === $this->user->save()) {
            $db->rollBack();
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.error_inserting_user');
            return false;
        }

        //save user presentation show
        if (false == $this->user->updatePresentationView(isset($input['show_in_presentation']))) {
            $db->rollBack();
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.error_updating_presentation');
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * Set fid info to user model and save in DB
     *
     * @param Request $request
     * @return bool
     */
    private function setFIDInfo($request)
    {
        $input = $request->all();

        //check if city coresponds to county and country
        $country = new Country();
        $input['city'] = isset($input['city']) ? $input['city'] : null;
        if ($input['city'] != null && false == $country->checkAddressCombination($input['country'], $input['county'], $input['city'])) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'company.error_address_combination');
        }

        //set user model properties
        $this->fid->setLegalName($input['legal_name']);

        //save user info
        $this->fid->setCui($input['cui']);
        $this->fid->setRegNr($input['reg_nr']);
        $this->fid->setIban($input['iban']);
        $this->fid->setEmail($input['email']);
        $this->fid->setPhone($input['phone']);
        $this->fid->setParentId($request->session()->get(User\Session::NAME)["id"]);
        $this->fid->setAddress($input['address']);
        $this->fid->setCityId($input['city']);
        $this->fid->setCountryId($input['country']);

        $db = Services::getInstance()->getDb();
        $db->beginTransaction();
        if (false === $this->fid->save()) {
            $db->rollBack();
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.error_inserting_user');
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * Creates a new role with the permissions given
     *
     * @param array $input input from form
     * @return bool
     */
    public function associateUserPermissions($role, $user)
    {
        $permissionEntity = new User\Permission();
        $permissionsForIT = $permissionEntity->getPermissionsForRole($role);
        foreach ($permissionsForIT as $permissionId) {
            $insertInfo[] = array(
                'permission_id' => $permissionId,
                'user_id' => $user->getId()
            );
        }

        if (empty($insertInfo)) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.error_no_permissions_selected');
            return false;
        }

        if (false === $permissionEntity->insertUserPermissions($insertInfo)) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'auth.error_inserting_permissions');
            return false;
        }

        return true;
    }

    private function sendMailToUser($user, $password)
    {
        $companyEntity = new Company(true);
        $company = $companyEntity->getMainAddress();
        $company['name'] = $companyEntity->getPresentationName();
        $fromEmail = $company['email'];
        $toEmail = $user->getEmail();
        try {
            Services::getInstance()->getMailer()->send(
                'email.initial_setup',
                array(
                    "username" => $user->getUserName(),
                    "password" => $password,
                    "url" => route('login'),
                    "company" => $company,
                ),
                function ($message) use ($toEmail, $fromEmail) {
                    $message
                        ->to($toEmail)
                        ->from($fromEmail, $fromEmail)
                        ->subject(app('translator')->trans('emails.auth.title'));
                }
            );
        } catch (\Exception $e) {
            Log::error("sendMailToUser: Error sending mail. Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
            return false;
        }
        return true;
    }

    private function setFIDSubscription($subscriptionPrice, $subscriptionStartDate, $subscriptionEndDate, $subscriptionName, $subscriptionDescription)
    {
        $subscription = new Subscription();
        $subscription->setPrice($subscriptionPrice);
        $subscription->setName($subscriptionName);
        $subscription->setDescription($subscriptionDescription);

        if (date("Y-m-d", strtotime("01-".$subscriptionStartDate)) < date("Y-m-01")) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'validator.start_is_in_past');
            return false;
        }

        if (date("Y-m-d", strtotime("01-".$subscriptionStartDate)) > date("Y-m-d", strtotime("01-".$subscriptionEndDate))) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'validator.start_bigger_than_end');
            return false;
        }

        if (false == $subscription->save()) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'fid.error_invalid_subscription');
            return false;
        }

        if ( false == $subscription->setSubscriptionForFID($this->fid->getId(), $subscriptionStartDate, $subscriptionEndDate)) {
            $this->messageInfo = array('type' => self::MESSAGE_TYPE_DANGER, 'body' => 'fid.error_inserting_subscription');
            return false;
        }

        return true;
    }

}
