<?php
declare(strict_types=1);

namespace Delos\Request;

final class Server extends ArrayVars
{
    /**
     * Request methods:
     */
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_HEAD = 'HEAD';
    const REQUEST_METHOD_PUT = 'PUT';

    /**
     * Overrides parent constructor.
     */
    public function __construct()
    {
        parent::__construct( $_SERVER );
    }

    /**
     * Returns request method (one of 'REQUEST_METHOD_' constants).
     *
     * @return string - Request method (one of REQUEST_METHOD_* constants).
     */
    public function getRequestMethod()
    {
        return strtoupper( $this->get( 'REQUEST_METHOD' ) );
    }

    public function getHeaders(): array
    {
        return getallheaders();
    }

    /**
     * Returns http user agent.
     *
     * @return string - http user agent.
     */
    public function getHttpUserAgent()
    {
        return $this->get( 'HTTP_USER_AGENT' );
    }

    /**
     * Returns the IP address of the server under which the current script is executing.
     *
     * @return string - server IP.
     */
    public function getServerAddr()
    {
        return $this->get('SERVER_ADDR');
    }

    /**
     * Returns client preferred language that comes from browser's settings ('HTTP_ACCEPT_LANGUAGE')
     * in two letters format. Examples: 'fr' for French, 'en' for English, 'it' for Italian etc.
     *
     * @return string - Client language.
     */
    public function getHttpAcceptLanguage()
    {
        return substr( $this->get( 'HTTP_ACCEPT_LANGUAGE' ), 0, 2 );
    }

    /**
     * Returns user IP.
     *
     * @return string|null - User IP address or `null` is it's not set.
     */
    public function getUserIp()
    {
        $user_ip = $this->get( 'REMOTE_ADDR' );
        $user_ip = empty( $user_ip ) ? $this->get( 'HTTP_X_FORWARDED_FOR' ) : $user_ip;

        return empty( $user_ip ) ? $this->get( 'HTTP_CLIENT_IP' ) : $user_ip;
    }

    /**
     * Checks for proxy in server headers.
     *
     * @return bool - Returns true if proxy is detected, false - otherwise.
     */
    public function isProxy()
    {
        $headers = array(
            'CLIENT_IP',
            'FORWARDED',
            'FORWARDED_FOR',
            'FORWARDED_FOR_IP',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR_IP',
            'HTTP_PC_REMOTE_ADDR',
            'HTTP_PROXY_CONNECTION',
            'HTTP_VIA',
            'HTTP_X_FORWARDED',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED_FOR_IP',
            'HTTP_X_IMFORWARDS',
            'HTTP_XROXY_CONNECTION',
            'VIA',
            'X_FORWARDED',
            'X_FORWARDED_FOR',
            'Xonnection',
            'Useragent-via'
        );

        foreach ( $headers as $value ) {
            $header = $this->get( $value );
            if ( !empty( $header ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getRemoteUser()
    {
        return $this->get('REMOTE_USER');
    }

    /**
     * @return mixed
     */
    public function getServerSelf()
    {
        return $this->get('PHP_SELF');
    }

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return $this->get('SCRIPT_NAME');
    }

    public function getVars(): array
    {
        if(empty($this->getRawData()['argv'])) {
            return [];
        }
        parse_str($this->getRawData()['argv'][0], $_SERVER_VARS);

        return $_SERVER_VARS;
    }

    public function getVar(string $index): mixed
    {
        if(empty($this->getVars()[$index]){
            return null;
        }
        return $this->getVars()[$index];
    }
}