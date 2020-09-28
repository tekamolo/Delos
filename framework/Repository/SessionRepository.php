<?php


namespace Delos\Repository;


use Delos\Model\Session\Session;

class SessionRepository
{
    /**
     * @param $identifier
     * @return mixed
     */
    public function getSession($identifier){
        return Session::where("identifier","=",$identifier);
    }

    /**
     * @param Session $session
     */
    public function updateOrCreate(Session $session){
        Session::updateOrCreate(
            ['identifier' => $session->identifier],
            ['data' => $session->data]
        );
    }

    /**
     * @param $identifier
     */
    public function deleteByIdentifier($identifier){
        $session = $this->getSession($identifier);
        if(!empty($session)) $session->delete();
    }
}