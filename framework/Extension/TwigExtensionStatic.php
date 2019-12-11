<?php

namespace Delos\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtensionStatic extends AbstractExtension
{
    public function getFunctions() {
        return array(
            new TwigFunction('static_call', array($this, 'staticCall')),
        );
    }

    function staticCall($class, $function, $args = array()) {
        if (class_exists($class) && method_exists($class, $function)) {
            return call_user_func_array(array($class, $function), $args);
        }

        return null;
    }
}