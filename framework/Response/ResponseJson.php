<?php
namespace Delos\Response;

class ResponseJson implements ResponseInteface
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
        echo json_encode($this->content);
        die();
    }
}