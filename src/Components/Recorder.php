<?php

namespace Tylercd100\LERN\Components;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tylercd100\LERN\Exceptions\RecorderFailedException;
use Tylercd100\LERN\Models\ExceptionModel;

class Recorder extends Component {

    /**
     * @var mixed
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $absolutelyDontHandle = [
        \Tylercd100\LERN\Exceptions\RecorderFailedException::class,
    ];

    /**
     * The constructor
     */
    public function __construct() {
        $this->config = config('lern.record');
    }

    /**
     * Records an Exception to the database
     * @param  Exception $e The exception you want to record
     * @return false|ExceptionModel
     * @throws RecorderFailedException
     */
    public function record(Exception $e)
    {
        if ($this->shouldntHandle($e)) {
            return false;
        }

        $opts = [
            'class'       => get_class($e),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'code'        => (is_int($e->getCode()) ? $e->getCode() : 0),
            'message'     => $e->getMessage(),
            'trace'       => $e->getTraceAsString(),
        ];

        $configDependant = array_keys($this->config['collect']);

        try {
            foreach ($configDependant as $key) {
                if ($this->canCollect($key)) {
                    $value = $this->collect($key, $e);
                    if ($value !== null) {
                        $opts[$key] = $value;
                    }
                }
            }

            $class = config('lern.recorder.model');
            $class = !empty($class) ? $class : ExceptionModel::class;

            $model = new $class();
            foreach($opts as $key => $value) {
                $model->{$key} = $value;
            }

            $model->save();
            return $model;
        } catch (Exception $e) {
            $code = (is_int($e->getCode()) ? $e->getCode() : 0);
            throw new RecorderFailedException($e->getMessage(), $code, $e);
        }
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
     * @param string $key
     * @param Exception $e
     * @return array|int|null|string
     * @throws Exception
     */
    protected function collect($key, Exception $e = null) {
        switch ($key) {
            case 'user_id':
                return $this->getUserId();
            case 'method':
                return $this->getMethod();
            case 'url':
                return $this->getUrl();
            case 'data':
                return $this->getData();
            case 'ip':
                return $this->getIp();
            case 'status_code':
                if ($e === null) {
                    return 0;
                }
                return $this->getStatusCode($e);
            default:
                throw new Exception("{$key} is not supported! Therefore it cannot be collected!");
        }
    }

    /**
     * Gets the ID of the User that is logged in
     * @return integer|null The ID of the User or Null if not logged in
     */
    protected function getUserId() {
        $user = Auth::user();
        if (is_object($user) && !empty($user->id)) {
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
        if (!empty($method)) {
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
        if (is_array($data)) {
            return $this->excludeKeys($data);
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
        if (is_string($url)) {
            return $url;
        } else {
            return null;
        }
    }

    /**
     * Returns the IP from the request
     *
     * @return string
     */
    protected function getIp() {
        return Request::ip();
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

    /**
     * This function will remove all keys from an array recursively as defined in the config file
     * @param  array $data The array to remove keys from
     * @return void
     */
    protected function excludeKeys(array $data) {
        $keys = isset($this->config['excludeKeys']) ? $this->config['excludeKeys'] : [];
        foreach ($data as $key => &$value) {
            if (in_array($key, $keys)) {
                unset($data[$key]);
            } else if (is_array($value)) {
                $value = $this->excludeKeys($value);
            }
        }

        return $data;
    }
}
