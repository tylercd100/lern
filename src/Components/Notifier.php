<?php

namespace Tylercd100\LERN\Components;

use Exception;
use Illuminate\Container\Container;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Tylercd100\LERN\Exceptions\NotifierFailedException;
use Tylercd100\Notify\Drivers\FromConfig as Notify;

class Notifier extends Component {
    protected $config;
    protected $log;
    protected $messageCb;
    protected $subjectCb;

    /**
     * You can provide a Monolog Logger instance to use in the constructor 
     * @param Logger|null $log Logger instance to use
     */
    public function __construct(Logger $log = null) {
        if ($log === null) {
            $log = new Logger(config('lern.notify.channel'));
        }

        $this->config = config('lern.notify');
        $this->log = $log;
    }

    /**
     * Transforms a value into a closure that returns itself when called
     * @param  callable|string $cb The value that you want to wrap in a closure
     * @return callable
     */
    private function wrapValueInClosure($cb) {
        if (is_callable($cb)) {
            return $cb;
        } else {
            return function() use ($cb) { return $cb; };
        }
    }

    /**
     * Set a string or a closure to be called that will generate the message body for the notification
     * @param callable|string $cb A closure or string that will be set for the message
     */
    public function setMessage($cb)
    {
        $this->messageCb = $this->wrapValueInClosure($cb);
        return $this;
    }

    /**
     * Returns the result of the message closure
     * @param  Exception $e The Exception instance that you want to build the message around
     * @return string       The message string
     */
    public function getMessage(Exception $e) {
        if (is_callable($this->messageCb)) {
            return $this->messageCb->__invoke($e);
        } else {
            $msg = get_class($e) . " was thrown! \n" . $e->getMessage();
            if ($this->config['includeExceptionStackTrace'] === true) {
                $msg .= "\n\n" . $e->getTraceAsString();
            }
            return $msg;
        }
    }

    /**
     * Set a string or a closure to be called that will generate the subject line for the notification
     * @param callable|string $cb A closure or string that will be set for the subject line
     */
    public function setSubject($cb)
    {
        $this->subjectCb = $this->wrapValueInClosure($cb);
        return $this;
    }

    /**
     * Returns the result of the subject closure
     * @param  Exception $e The Exception instance that you want to build the subject around
     * @return string       The subject string
     */
    public function getSubject(Exception $e) {
        if (is_callable($this->subjectCb)) {
            return $this->subjectCb->__invoke($e);
        } else {
            return get_class($e);
        }
    }

    /**
     * Pushes on another Monolog Handler
     * @param  HandlerInterface $handler The handler instance to add on
     * @return Notifier                  Returns this
     */
    public function pushHandler(HandlerInterface $handler) {
        $this->log->pushHandler($handler);
        return $this;
    }

    /**
     * Triggers the Monolog Logger instance to log an error to all handlers
     * @param  Exception $e The exception to use
     * @param  array $context Additional information that you would like to pass to Monolog
     * @return bool
     */
    public function send(Exception $e, array $context = []) {
        
        if ($this->shouldntHandle($e)) {
            return false;
        }

        $message = $this->getMessage($e);
        $subject = $this->getSubject($e);
        
        try {
            $context = $this->buildContext($context, $e);

            $notify = new Notify($this->config, $this->log, $subject);

            $notify->critical($message, $context);

            return true;
        } catch (Exception $e) {
            $code = (is_int($e->getCode()) ? $e->getCode() : 0);
            throw new NotifierFailedException($e->getMessage(), $code, $e);
        }
    }

    /**
     * Builds a context array to pass to Monolog
     *
     * @param  array $context Additional information that you would like to pass to Monolog
     * @param \Exception $e
     *
     * @return array The modified context array
     */
    protected function buildContext(array $context = [], Exception $e)
    {
        $app = app();

        // Add sound ro pushover
        if(in_array('pushover', $this->config['drivers']))
        {
            $context['sound'] = $this->config['pushover']['sound'];
        }

        // Add exception context for raven for better handling in Sentry
        if(in_array('raven', $this->config['drivers']))
        {
            $context['exception'] = $e;
        }

        // Add auth data if available.
        if(isset($app['auth']) && $user = $app['auth']->user())
        {
            if(empty($context['user']) or !is_array($context['user']))
            {
                $context['user'] = [];
            }

            if(!isset($context['user']['id']) && method_exists($user, 'getAuthIdentifier'))
            {
                $context['user']['id'] = $user->getAuthIdentifier();
            }

            if(!isset($context['user']['id']) && method_exists($user, 'getKey'))
            {
                $context['user']['id'] = $user->getKey();
            }

            if(!isset($context['user']['id']) && isset($user->id))
            {
                $context['user']['id'] = $user->id;
            }
        }

        // Add session data if available.
        if(isset($app['session']) && $session = $app['session']->all())
        {
            if(empty($context['user']) or !is_array($context['user']))
            {
                $context['user'] = [];
            }

            if(!isset($context['user']['id']))
            {
                $context['user']['id'] = $app->session->getId();
            }

            if(isset($context['user']['data']))
            {
                $context['user']['data'] = array_merge($session, $context['user']['data']);
            } else
            {
                $context['user']['data'] = $session;
            }
        }
        // Automatic tags
        $tags = [
            'environment' => $app->environment(),
            'server'      => $app->request->server('HTTP_HOST'),
            'php_version' => phpversion(),
        ];

        // Add tags to context.
        if(isset($context['tags']))
        {
            $context['tags'] = array_merge($tags, $context['tags']);
        } else
        {
            $context['tags'] = $tags;
        }

        // Automatic extra data.
        $extra = [
            'ip' => $app->request->getClientIp(),
        ];

        // Everything that is not 'user', 'tags' or 'level' is automatically considered
        // as additonal 'extra' context data.
        $extra = array_merge($extra, array_except($context, ['user', 'tags', 'level', 'extra']));

        // Add extra to context.
        if(isset($context['extra']))
        {
            $context['extra'] = array_merge($extra, $context['extra']);
        } else
        {
            $context['extra'] = $extra;
        }

        // Clean out other values from context.
        $context = array_only($context, ['user', 'tags', 'level', 'extra', 'exception']);

        return $context;
    }
}