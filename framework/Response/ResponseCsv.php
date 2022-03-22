<?php
declare(strict_types=1);

namespace Delos\Response;

final class ResponseCsv implements ResponseInterface
{
    public array $informationArray;
    public string $filename;
    public array $firstLine;
    public string $dateFormat;
    public string $dateIndex;

    public function __construct(string $filename, array $information)
    {
        $this->filename = $filename;
        $this->informationArray = $information;
    }

    private function setHeaders(): void
    {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->filename . '.csv"');

        header('Pragma: no-cache');
        header('Expires: 0');
    }

    public function setFirstCsvLine(array $fistLine): void
    {
        $this->firstLine = $fistLine;
    }

    public function setDateFormat(string $dateFormat, string $dateIndex): void
    {
        $this->dateFormat = $dateFormat;
        $this->dateIndex = $dateIndex;
    }

    public function process(): void
    {
        $this->setHeaders();
        $file = fopen('php://output', 'w');
        if (!empty($this->firstLine)) {
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
    }
}