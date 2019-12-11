<?php

namespace Delos\Response;

class ResponseCsv implements ResponseInteface
{
    /**
     * @var array
     */
    public $informationArray;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var array
     */
    public $firstLine;

    /**
     * @var string
     */
    public $dateFormat;

    /**
     * @var string
     */
    public $dateIndex;

    /**
     * Response_Response constructor.
     * @param string $filename
     * @param array $information
     */
    public function __construct($filename, array $information)
    {
        $this->filename = $filename;
        $this->informationArray = $information;
    }

    private function setHeaders()
    {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="'.$this->filename.'.csv"');

        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * @param array $fistLine
     */
    public function setFirstCsvLine(array $fistLine)
    {
        $this->firstLine = $fistLine;
    }

    /**
     * @param $dateFormat
     * @param $dateIndex
     */
    public function setDateFormat($dateFormat, $dateIndex)
    {
        $this->dateFormat = $dateFormat;
        $this->dateIndex = $dateIndex;
    }

    /**
     * @return void
     */
    public function process()
    {
        $this->setHeaders();
        $file = fopen('php://output', 'w');
        if(!empty($this->firstLine)){
            fputcsv($file, $this->firstLine);
        }

        /**
         * For speed purposes the foreach has to be done once only. That is why we are creating the set date method above
         * if we need more complicated operation we would have to insert an intermediary object instead of the method.
         */
        foreach ($this->informationArray as $array)
        {
            if(!empty($this->dateFormat) && !empty($this->dateIndex)){
                $array[$this->dateIndex] = date($this->dateFormat,$array[$this->dateIndex]);
            }
            fputcsv($file, $array);
        }
        die();
    }
}