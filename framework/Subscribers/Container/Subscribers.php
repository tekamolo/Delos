<?php


namespace Delos\Subscribers\Container;


use Delos\Collection;
use Delos\Request\Request;
use Delos\Subscribers\SubscriberInterface;

class Subscribers
{
    public static $applicationSubscribers = array();

    /**
     * @return string[]
     */
    static public function getDelosSubscribers(){
        return [

        ];
    }


    /**
     * @param string $applicationSubscriber
     */
    static public function addSubscriberApplication(string $applicationSubscriber){
        self::$applicationSubscribers[] = $applicationSubscriber;
    }

    /**
     * @return array
     */
    static public function getApplicationSubscribers(){
        return self::$applicationSubscribers;
    }

    /**
     * @param SubscriberInterface $interface
     */
    static public function injectSubscriber(SubscriberInterface $interface){
        foreach ($interface->getSubscribers() as $s){
            self::addSubscriberApplication($s);
        }
    }
}