<?php
declare(strict_types=1);

namespace Delos\Service\Extension;

use DateTime;
use Delos\Model\Session\Session;
use Delos\Repository\SessionRepository;
use Delos\Request\ArrayVars;

class SessionDatabase extends ArrayVars
{
    private SessionRepository $sessionsRepo;
    private string $identifier;

    public function __construct(SessionRepository $sessionsRepo)
    {
        $this->sessionsRepo = $sessionsRepo;
        parent::__construct(array());
    }

    public function initialize(): void
    {
        /** if we have the identifier set we need to retrieve the data from the database */
        if (!empty($this->identifier)) {
            /** @var Session $sessionObject */
            $sessionObject = $this->sessionsRepo->getSession($this->identifier);
            $this->data = unserialize($sessionObject->data);
        }
    }

    public function persistSessionData(): void
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

    public function purgeAllTheSession(): void
    {
        if (!empty($this->identifier)) {
            $this->sessionsRepo->deleteByIdentifier($this->identifier);
        }
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier): SessionDatabase
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
    public function setVar(string $name, $value): void
    {
        if (!empty($name)) {
            $this->data[$name] = $value;
        }
    }

    public function purge(string $name): void
    {
        unset($this->data[$name]);
    }
}