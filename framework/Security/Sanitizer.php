<?php
declare(strict_types=1);

namespace Delos\Security;

final class Sanitizer
{
    static public function sanitizePassword(string $password): string
    {
        /** We have to decide here what is acceptable and what is not
         *  but I suppose this will depend upon centralpay rules too
         *  Ex: tags are not allowed
         *  Ex: semicolons maybe banned since they can lead to a php/Sql execution code
         *  Stripping spaces between password
         */
        $password = trim($password);
        $password = preg_replace("#\\s#", "", $password);
        return preg_replace("#\\s|(<[a-zA-Z0-9\/=':.]*>)*#", "", $password);
    }
}