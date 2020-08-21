<?php


namespace Delos\Subscribers\Container;


use Delos\Collection;
use Delos\Request\Request;

class Subscribers
{
    /**
     * @var Collection
     */
    private $applicationSubscribers;

    public function __construct()
    {
        $this->applicationSubscribers = new Collection();
    }

    /**
     * @return string[]
     */
    public function getDelosSubscribers(){
        return [
            Request::class,
        ];
    }


    /**
     * @param string $applicationSubscriber
     * @return Subscribers
     */
    public function addSubscriberApplication(string $applicationSubscriber){
        $this->applicationSubscribers->add($applicationSubscriber);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getApplicationSubscribers(){
        return $this->applicationSubscribers;
    }


}