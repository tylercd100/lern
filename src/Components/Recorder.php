<?php

namespace Tylercd100\LERN\Components;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tylercd100\LERN\Models\ExceptionModel;

class Recorder {

    /**
     * @var mixed
     */
    protected $config = [];

    /**
     * The constructor
     */
    public function __construct() {
        $this->config = config('lern.record');
    }

    /**
     * Records an Exception to the database
     * @param  Exception $e The exception you want to record
     * @return ExceptionModel
     */
    public function record(Exception $e)
    {
        $opts = [
            'class'       => get_class($e),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'trace'       => $e->getTraceAsString(),
        ];

        $opts['status_code'] = $this->getStatusCode($e);
        $opts['user_id'] = $this->getUserId($e);
        $opts['method'] = $this->getMethod($e);
        $opts['data'] = $this->getData($e);
        $opts['url'] = $this->getUrl($e);

        return ExceptionModel::create($opts);
    }

    /**
     * Checks the config to see if you can collect certain information
     * @param  string $type the config value you want to check
     * @return boolean      
     */
    private function canCollect($type) {
        if (!empty($this->config) && !empty($this->config['collect']) && !empty($this->config['collect'][$type])) {
            return $this->config['collect'][$type] === true;
        }
        return false;
    }

    /**
     * Gets the ID of the User that is logged in
     * @return integer|null The ID of the User or Null if not logged in
     */
    protected function getUserId() {
        $user = Auth::user();
        if ($this->canCollect('user_id') && is_object($user)) {
            return $user->id;
        } else {
            return null;
        }
    }

    /**
     * Gets the Method of the Request
     * @return string|null Possible values are null or GET, POST, DELETE, PUT, etc...
     */
    protected function getMethod() {
        $method = Request::method();
        if ($this->canCollect('method') && !empty($method)) {
            return $method;
        } else {
            return null;
        }
    }

    /**
     * Gets the input data of the Request
     * @return array|null The Input data or null
     */
    protected function getData() {
        $data = Input::all();
        if ($this->canCollect('data') && is_array($data)) {
            return $data;
        } else {
            return null;
        }
    }

    /**
     * Gets the URL of the Request
     * @return string|null Returns a URL string or null
     */
    protected function getUrl() {
        $url = Request::url();
        if ($this->canCollect('url') && is_array($url)) {
            return $url;
        } else {
            return null;
        }
    }

    /**
     * Gets the status code of the Exception
     * @param  Exception $e The Exception to check
     * @return string|integer The status code value
     */
    protected function getStatusCode(Exception $e) {
        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        } else {
            return 0;
        }
    }
}