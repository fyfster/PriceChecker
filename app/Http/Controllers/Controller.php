<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Helpers\Http;
use App\Http\Helpers\Services;
use App\Http\Helpers\Validator;
use Illuminate\Http\Request;
use \Log;

class Controller extends BaseController
{
    CONST LOGIN_URL = 'login';
    CONST CREATE_USER_URL = 'create-user';
    CONST USER_ACTIVE_LIST_URL = 'user-active-list';

    CONST CREATE_USER_PAGE = 'user/form';

    protected $httpHelper;
    protected $validator;
    protected $session;

    CONST MESSAGE_TYPE_DANGER = "danger";
    CONST MESSAGE_TYPE_SUCCESS = "success";
    CONST ENVIRONMENT_PRODUCTION = "production";

    public function __construct(Request $request)
    {
        date_default_timezone_set("Europe/Bucharest");
        $servicesInstance = Services::getInstance();
        $this->httpHelper = $servicesInstance->getHttpHelper();
        $this->messageCodes = $servicesInstance->getConfig()->get('message_codes');
        $this->validator = new Validator($request->all());
        $logInfo = $request->fullUrl().' '.json_encode(array('data' => $request->all()));
        Log::info($logInfo);
        $this->loadAllMenuItems();
    }

    public function setSessionMessage($message, $type = self::MESSAGE_TYPE_DANGER)
    {
        Services::getInstance()->getSession()->flash('message', array('type' => $type, 'body' => app('translator')->trans($message)));
    }

    private function loadAllMenuItems()
    {
        $pages = Services::getInstance()->getConfig()->get('permission.pages');
        $menuItems = array(
            array(
                'name' => 'common.header_menu_dashboard',
                'icon' => 'settings',
                'role' => $pages['dashboard'],
                'url' => 'dashboard',
            )
        );
        $this->httpHelper->setMenuItems($menuItems);
    }
}
