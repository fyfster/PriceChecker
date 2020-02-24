<?php
namespace App\Http\Helpers;

class Services
{
    private static $instance = null;

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;


    private $storage;

    /**
     * @var \Illuminate\Config\Repository
     */
    private $config;

    /**
     * @var Http
     */
    private $httpHelper;

    /**
     *
     * @var \Illuminate\Session\SessionManager
     */
    private $session;

    /**
     * @return Services
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function getRequest()
    {
        if (empty($this->request)) {
            $this->request = app()->make('request');
        }

        return $this->request;
    }

    public function setRequest(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    public function getStorage()
    {
        if (empty($this->storage)) {
            $this->storage = app()->make('filesystem');
        }

        return $this->storage;
    }

    public function getConfig()
    {
        if (empty($this->config)) {
            $this->config = app()->make('config');
        }

        return $this->config;
    }


    public function getHttpHelper()
    {
        if (empty($this->httpHelper)) {
            $this->httpHelper = new Http();
        }

        return $this->httpHelper;
    }

    public function getSession()
    {
        if (empty($this->session)) {
            $this->session = app()->make('session');
        }

        return $this->session;
    }

}
