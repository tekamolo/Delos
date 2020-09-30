<?php

namespace Delos\Service;

trait JsonResponseArrayTrait
{
    /**
     * @var array
     */
    private $jsonResponseArray = array(
        'success' => false,
        'error' => array(),
    );

    /**
     * @param $error
     */
    public function addError($error){
        $this->jsonResponseArray['success'] = false;
        $this->jsonResponseArray['error'][] = $error;
    }

    /**
     * @return array
     */
    public function getJsonResponseArray(){
        if($this->isErrorEmpty()) $this->setSuccessToTrue();
        return $this->jsonResponseArray;
    }

    /**
     * @return bool
     */
    public function isErrorEmpty(){
        return empty($this->jsonResponseArray['error']);
    }

    private function setSuccessToTrue(){
        $this->jsonResponseArray['success'] = true;
    }
}