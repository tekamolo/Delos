<?php
declare(strict_types=1);

namespace Delos\Repository;

use Delos\Model\Session\Session;

final class SessionRepository implements RepositoryInterface
{
    /**
     * @param $identifier
     * @return mixed
     */
    public function getSession($identifier)
    {
        return Session::where("identifier", "=", $identifier);
    }

    /**
     * @param Session $session
     */
    public function updateOrCreate(Session $session)
    {
        Session::updateOrCreate(
            ['identifier' => $session->identifier],
            ['data' => $session->data]
        );
    }

    /**
     * @param $identifier
     */
    public function deleteByIdentifier($identifier): void
    {
        $session = $this->getSession($identifier);
        if (!empty($session)) $session->delete();
    }
}