<?php
declare(strict_types=1);

namespace Delos\Response;

final class ResponseJson implements ResponseInterface
{
    private array $content;
    private int $http_code;
    private array $methods;

    public function __construct(array $content, int $http_code = 200, array $methods = ["GET"])
    {
        $this->content = $content;
        $this->http_code = $http_code;
        $this->methods = $methods;
    }

    private function getMethods(): string
    {
        $stringMethods = "";
        $i = 0;
        foreach ($this->methods as $method) {
            $stringMethods .= "$method";
            if ($i !== 0) {
                $stringMethods .= ", ";
            }
            $i++;
        }
        return $stringMethods;
    }

    public function process(): void
    {
        http_response_code($this->http_code);
        $methods = $this->getMethods();
        header('Content-Type: application/json; charset=utf-8');
        header("Access-Control-Allow-Methods: $methods");
        echo json_encode($this->content);
        die();
    }
}