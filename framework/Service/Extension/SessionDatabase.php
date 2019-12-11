<?php

namespace Delos\Service\Extension;


use ArrayVars;

class SessionDatabase extends ArrayVars
{
    /**
     * @var \Model_Sessions
     */
    private $sessionsRepo;

    /**
     * @var string
     */
    private $identifier;

    /**
     * SessionDatabase constructor.
     * @param \Model_Sessions $sessionsRepo
     */
    public function __construct(\Model_Sessions $sessionsRepo)
    {
        $this->sessionsRepo = $sessionsRepo;
        parent::__construct(array());
    }

    public function initialize()
    {
        /** if we have the identifier set we need to retrieve the data from the database */
        if (!empty($this->identifier)) {
            /** @var \Dao_Sessions $sessionObject */
            $sessionObject = $this->sessionsRepo->getSession($this->identifier);
            $this->data = unserialize($sessionObject->data);
        }
    }

    /**
     * @throws \Exception
     */
    public function persistSessionData()
    {
        $session = new \Dao_Sessions();
        $session->data = serialize($this->data);
        $session->date_creation = new \DateTime();
        if (empty($this->identifier)) {
            $this->identifier = substr(md5(openssl_random_pseudo_bytes(20)), -32);
            $session->identifier = $this->identifier;
            $this->sessionsRepo->create($session);
        } else {
            $session->identifier = $this->identifier;
            $this->sessionsRepo->update($session);
        }
    }

    public function purgeAllTheSession()
    {
        if (!empty($this->identifier)) {
            $this->sessionsRepo->delete($this->identifier);
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