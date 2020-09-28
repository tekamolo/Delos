<?php

namespace Delos\Service\Extension;

use DateTime;
use Delos\Model\Session\Session;
use Delos\Repository\SessionRepository;
use Delos\Request\ArrayVars;
use Exception;

class SessionDatabase extends ArrayVars
{
    /**
     * @var SessionRepository
     */
    private $sessionsRepo;

    /**
     * @var string
     */
    private $identifier;

    /**
     * SessionDatabase constructor.
     * @param SessionRepository $sessionsRepo
     */
    public function __construct(SessionRepository $sessionsRepo)
    {
        $this->sessionsRepo = $sessionsRepo;
        parent::__construct(array());
    }

    public function initialize()
    {
        /** if we have the identifier set we need to retrieve the data from the database */
        if (!empty($this->identifier)) {
            /** @var Session $sessionObject */
            $sessionObject = $this->sessionsRepo->getSession($this->identifier);
            $this->data = unserialize($sessionObject->data);
        }
    }

    /**
     * @throws Exception
     */
    public function persistSessionData()
    {
        $session = new Session();
        $session->data = serialize($this->data);
        $session->date_creation = new DateTime();
        if (empty($this->identifier)) {
            $this->identifier = substr(md5(openssl_random_pseudo_bytes(20)), -32);
            $session->identifier = $this->identifier;
            $this->sessionsRepo->updateOrCreate($session);
        }
    }

    public function purgeAllTheSession()
    {
        if (!empty($this->identifier)) {
            $this->sessionsRepo->deleteByIdentifier($this->identifier);
        }
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     * @return SessionDatabase
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Add desired variables to the session.
     *
     * @param string $name - Session variable (key) name.
     * @param mixed $value - Variable value.
     */
    public function setVar($name, $value)
    {
        if (!empty($name)) {
            $this->data[$name] = $value;
        }
    }

    /**
     * @param $name
     */
    public function purge($name)
    {
        unset($this->data[$name]);
    }
}