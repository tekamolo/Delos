<?php
declare(strict_types=1);

namespace Delos\Subscribers\Container;

use Delos\Subscribers\SubscriberInterface;

class Subscribers
{
    public static array $applicationSubscribers = array();

    /**
     * @return string[]
     */
    static public function getDelosSubscribers(): array
    {
        return [

        ];
    }


    static public function addSubscriberApplication(string $applicationSubscriber): void
    {
        self::$applicationSubscribers[] = $applicationSubscriber;
    }

    static public function getApplicationSubscribers(): array
    {
        return self::$applicationSubscribers;
    }

    static public function injectSubscriber(SubscriberInterface $interface): void
    {
        foreach ($interface->getSubscribers() as $s) {
            self::addSubscriberApplication($s);
        }
    }
}