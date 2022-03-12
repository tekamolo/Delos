<?php
declare(strict_types=1);

namespace Delos\Request;

final class Request
{

    /**
     * HTTP GET method
     */
    const METHOD_GET = 'GET';

    /**
     * HTTP POST method
     */
    const METHOD_POST = 'POST';

    /**
     * @var string HTTP method.
     */
    public $method;

    /**
     * @var GetVars The GET parameters.
     */
    public $get;

    /**
     * @var PostVars The POST parameters.
     */
    public $post;

    /**
     * @var Server The SERVER parameters.
     */
    public $server;

    /**
     * @var Cookie - The COOKIE parameters.
     */
    public $cookie;

    /**
     * @var Session - The SESSION parameters.
     */
    public $session;

    /**
     * @var Files - The FILES parameters.
     */
    public $files;

    /**
     * @var string Request body
     */
    private $content;

    /**
     * Returns the client IP address.
     */
    public function getClientIp()
    {
        return filter_var($this->server->getUserIp(), FILTER_VALIDATE_IP);
    }

    /**
     * Creates a new request with values from PHP's superglobals.
     *
     * @return Request
     */
    public static function createFromGlobals()
    {
        $instance = new self();
        $_COOKIE = empty($_COOKIE) ? array() : $_COOKIE;
        $_SESSION = empty($_SESSION) ? array() : $_SESSION;
        $_FILES = empty($_FILES) ? array() : $_FILES;
        $content = file_get_contents('php://input');
        $instance->init(
            new GetVars(),
            new PostVars(),
            new Server(),
            new Cookie(),
            new Session(),
            new Files(),
            $content
        );

        return $instance;
    }

    /**
     * Set parameters for this request.
     *
     * @param GetVars $get The GET parameters.
     * @param PostVars $post The POST parameters.
     * @param Server $server The SERVER parameters.
     * @param Cookie $cookie The Cookie parameters.
     * @param Session $session The Session parameters.
     * @param Files $files The Files parameters.
     * @param string $content
     */
    public function init(
        GetVars $get,
        PostVars $post,
        Server $server,
        Cookie $cookie,
        Session $session,
        Files $files,
        $content
    ) {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->cookie = $cookie;
        $this->session = $session;
        $this->files = $files;
        $this->content = $content;

        $this->method = null;
    }

    /**
     * Get request method.
     *
     * @return string The request method.
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = $this->server->getRequestMethod();
        }

        if (self::METHOD_POST !== $this->method) {
            // assume GET as default method.
            $this->method = self::METHOD_GET;
        }

        return $this->method;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gets index request from Post request if null then from Get
     *
     * @param string $key
     * @param string $type
     * @param mixed $default
     *
     * @return mixed
     */
    public function getRequestByKey($key, $type = VarFilter::NONE, $default = null)
    {
        $var = $this->post->get($key, $type);
        return is_null($var) ? $this->get->get($key, $type, $default) : $var;
    }

    /**
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function getRequestString($key, $default = "")
    {
        return $this->getRequestByKey($key, VarFilter::STRING, $default);
    }

    /**
     * @param string $key
     * @param int $default
     *
     * @return integer
     */
    public function getRequestInt($key, $default = 0)
    {
        return $this->getRequestByKey($key, VarFilter::INT, $default);
    }

    /**
     * @param string $key
     * @param float|int $default
     *
     * @return float
     */
    public function getRequestFloat($key, $default = 0)
    {
        return $this->getRequestByKey($key, VarFilter::FLOAT, $default);
    }
}
