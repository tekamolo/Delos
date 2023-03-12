<?php
declare(strict_types=1);

namespace Delos\Routing;

use Delos\Exception\Exception;
use Delos\Parser\XmlParser;

final class RouterAdminXmlProvider
{
    public XmlParser $xmlParser;
    public \SimpleXMLElement $selectedNode;
    public \SimpleXMLElement $nodes;
    public string $language;

    static private array $languagesArrayWithPrefix = [
        "es",
        "fr",
    ];

    public function __construct(XmlParser $parser)
    {
        $this->xmlParser = $parser;
    }

    public function getRouteByRequest(
        array $requestArray,
        string $url,
        string $method,
    ): array
    {
        $params = [];
        $requestArray = array_reverse($requestArray);
        $this->language = "en";
        if (empty($url)) {
            $url = "/";
        } elseif (preg_match("/^\//", $url) == 0) {
            $url = "/" . $url;
        }

        if (!empty($requestArray)) {
            foreach ($requestArray as $r) {
                if ($url == "///" || $url == "//") $url = "/";
                $this->SelectNode(
                    $url,
                    $method
                );
                if (!empty($this->selectedNode)) {
                    break;
                } else {
                    $url = preg_replace("#$r(/)?$#", "", $url);
                    $params[] = $r;
                }
            }
        }
        if (empty($this->selectedNode)) {
            $url = "/";
            $this->SelectNode(
                $url,
                $method
            );
            $params = $requestArray;
        }

        return array($url, array_reverse($params), $this->language);
    }

    private function SelectNode(
        string $pathVar,
        string $method,
    ): void
    {
        if (empty($this->nodes))
            $this->nodes = $this->xmlParser->getXpath("/routes")[0];
        foreach ($this->nodes as $n) {
            foreach ($n->url as $url) {
                if ($pathVar == $url->__toString() ) {
                    $this->selectedNode = $n;
                    $this->language = $url->attributes()['lang']->__toString();
                }
            }
        }
    }


    private function isMethodValid(
        \SimpleXMLElement $element,
        string $requestMethod,
    ): bool
    {
        if(in_array($requestMethod, $this->getMethodListedInNode($element)))
        {
            return true;
        }
        return false;
    }
    private function getMethodListedInNode(\SimpleXMLElement $element): array
    {
        if(empty($element->methods->__toString())) {
            return ['GET'];
        }
        return explode('|',$element->methods->__toString());
    }

    public function getRoute($pathName): ?array
    {
        $result = $this->xmlParser->getXpath('/routes/route[@alias="' . $pathName . '"]');
        if (!empty($result)) {
            return $result;
        }
        return null;
    }

    public function getAllowedMethods(): array
    {
        if(empty($this->selectedNode[0]->methods))
        {
            return [];
        }
        return explode('|',$this->selectedNode[0]->methods->__toString());
    }

    private function getRouteNodeByUrl(string $url, string $language): array
    {
        $nodes = $this->xmlParser->searchNodeByChildrenTagValue("url[@lang='" . $language . "']", $url);
        if (empty($nodes)) {
            throw new Exception("There is no node with the locator: $url");
        }

        return $nodes;
    }

    public function getBaseControllerNamespace(): string
    {
        $result = $this->xmlParser->getXpath('/routes/@namespaceBaseController');
        if (empty($result)) {
            return "Delos\\Controller\\";
        }
        return $result[0]->__toString();
    }

    public function getSelectedNodeController(string $url, string $language): string
    {
        [$node] = $this->selectedNode;
        $controllerExplode = explode(":", $node->controller->__toString());
        $controller = $this->getBaseControllerNamespace() . $controllerExplode[0];


        if (!class_exists($controller)) {
            throw new Exception("The class $controller does not exist! \n</br>" . __FILE__ . ' line:' . __LINE__ . " </br></br>
                                Hints: You may have forgotten to set the extension '.php' to your controller");
        }
        return $controller;
    }

    public function getSelectedNodeMethod(): string
    {
        $node = $this->selectedNode;
        $controllerExplode = explode(":", $node->controller->__toString());
        $controller = $this->getBaseControllerNamespace() . $controllerExplode[0];
        if (!class_exists($controller)) {
            $controller = $controllerExplode[0];
        }

        $method = $controllerExplode[1];

        if (!method_exists($controller, $method)) {
            throw new Exception("The method '$method' inside $controller does not exist!  \n" . __FILE__ . ' line:' . __LINE__ . " </br></br>");
        }
        return $method;
    }

    public function getSelectedNodeAccess(string $url): string
    {
        $node = $this->selectedNode;
        $access = $node[0]->access->__toString();
        if (empty($access)) {
            throw new Exception("There is no security access for the locator: $url");
        }
        return $access;
    }

    public function getAccessByUrl(string $url, string $language): string
    {
        $node = $this->getRouteNodeByUrl($url, $language);
        $access = $node[0]->access->__toString();
        if (empty($access)) {
            throw new Exception("There is no security access for the locator: $url");
        }
        return $access;
    }
}