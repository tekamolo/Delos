<?php

namespace Delos\Response;

class Response implements ResponseInteface
{
    /**
     * @var string
     */
    public $content;

    /**
     * Response_Response constructor.
     * @param $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    public function process()
    {
        echo $this->content;
        die();
    }
}