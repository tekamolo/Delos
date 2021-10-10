<?php
declare(strict_types=1);

namespace Delos\Exception;

class Exception extends \Exception
{
    /**
     * @param $projectFolder
     * @return string|void
     */
    public function getMessageHtml($projectFolder)
    {
        $message = $this->getMessage();
        require $projectFolder . "/views/exception.php";
        die();
    }
}