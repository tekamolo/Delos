<?php
declare(strict_types=1);

namespace Delos\Routing;

use Delos\Exception\Exception;
use Delos\Exception\ExceptionToJson;
use Delos\Request\Request;
use Delos\Request\Server;

final class RouterXml
{
    private Request $request;
    private RouterAdminXmlProvider $xmlRouteProvider;
    private string $url;
    private ?array $parameters;
    private ?array $getGetUrlParams = [];
    private string $selectedLanguage = "en";

    public function __construct(Request $request, RouterAdminXmlProvider $providerAdminXml)
    {
        $this->request = $request;
        $this->xmlRouteProvider = $providerAdminXml;
        $this->getGetUrlParams = $this->extractGetUrlParams($request);
        $this->processUrl(
            $this->request,
        );
    }

    private function extractGetUrlParams(Request $request): array
    {
        if(count($this->request->get->getRawData()) < 2) {
            return [];
        }
        $allUrlParams = $request->get->getRawData();
        array_shift($allUrlParams);
        return $allUrlParams;
    }

    public function processUrl(Request $request): void
    {
        $url = $request->get->getRawData();
        /** Gets the url and separate it between the url and the parameters */
        preg_match_all("/([@\w-]+)/", $url['url'], $urlMatches);
        $url = $this->eliminateGetParams($url['url']);
        $url = $this->addSlashAtTheEndIfNeeded($url);
        $matches = (!empty($urlMatches[0])) ? $urlMatches[0] : array("/");
        [$url, $params, $language] = $this->xmlRouteProvider->getRouteByRequest(
            $matches,
            $url,
            empty($request->server->getRequestMethod()) ? "GET" : $request->server->getRequestMethod(),
        );
        $this->url = $url;
        $this->parameters = $params;
        $this->selectedLanguage = $language;
    }

    private function eliminateGetParams(string $url): string
    {
        return preg_replace('/&.*|\\?.*/', '', $url);
    }

    private function addSlashAtTheEndIfNeeded(string $url): string
    {
        return preg_replace('#/$#', '', $url) . '/';
    }

    public function getParams(): ?array
    {
        return $this->parameters;
    }

    public function getGetUrlParams(): array
    {
        return $this->getGetUrlParams;
    }

    public function getParam($position): ?string
    {
        return !empty($this->parameters[$position]) ? $this->parameters[$position] : null;
    }

    public function getCurrentUrl(): string
    {
        return $this->url;
    }

    public function getCurrentUrlWithParams(): string
    {
        $params = "";
        if (!empty($this->parameters)) {
            foreach ($this->parameters as $p) {
                $params .= "$p/";
            }
        }
        $base = $this->url;
        $base = ($base == "///" || $base == "") ? "/" : $base;
        return $base . $params;
    }

    public function getUrl(string $pathName, string $language = null): string
    {
        $language = !empty($language) ? $language : $this->selectedLanguage;
        return $this->getUrlFromXmlAndProvider($pathName, $language);
    }

    private function getUrlFromXmlAndProvider(string $pathName, string $language): string
    {
        $nodeArray = $this->xmlRouteProvider->getRoute($pathName);
        if (!empty($nodeArray)) {
            /** @var \SimpleXMLElement $node */
            $node = $nodeArray[0];
            return (string)$node->xpath("url[@lang='" . $language . "']")[0];
        }
        throw new Exception("There is no route with the alias: $pathName");
    }

    public function getController(): string
    {
        return $this->xmlRouteProvider->getSelectedNodeController($this->url, $this->selectedLanguage);
    }

    public function getMethod(): string
    {
        return $this->xmlRouteProvider->getSelectedNodeMethod();
    }

    public function getAccess(): string
    {
        return $this->xmlRouteProvider->getSelectedNodeAccess($this->url);
    }

    public function getCurrentAlias(): ?string
    {
        /** @var \SimpleXMLElement $nodeArray */
        $nodeArray = $this->xmlRouteProvider->selectedNode;
        if (empty($nodeArray)) return '';
        return $nodeArray->attributes()->alias->__toString();
    }

    public function redirect(string $pathName, array $parameters = array())
    {
        $url = $this->getUrl($pathName);
        $stringParams = $this->getGetParameters($parameters);
        header("location: $url$stringParams");
        die();
    }

    public function redirectByLanguage(string $pathName, string $language, array $parameters = array()): void
    {
        $url = $this->getUrl($pathName, $language);
        $stringParams = $this->getGetParameters($parameters);
        header("location: $url$stringParams");
        die();
    }

    public function delosRedirect(string $pathName, array $parameters = array()): void
    {
        $url = $this->getUrl($pathName);
        $stringParams = "";
        if (!empty($parameters)) {
            foreach ($parameters as $v) {
                $stringParams .= "$v/";
            }
        }
        header("location: $url$stringParams");
        die();
    }

    private function getGetParameters(array $parameters): string
    {
        $stringParams = "";
        if (empty($parameters)) return $stringParams;
        $i = 0;
        foreach ($parameters as $k => $v) {
            $separator = ($i == 0) ? "?" : "&";
            $stringParams .= "$separator$k=$v";
            $i++;
        }
        return $stringParams;
    }

    public function getCurrentLanguage(): string
    {
        return $this->selectedLanguage;
    }

    public function getHttpHost(): ?string
    {
        return $this->request->server->get("HTTP_HOST");
    }

    public function getFullCurrentUrl(): string
    {
        $params = "";
        if (!empty($this->parameters)) {
            foreach ($this->parameters as $p) {
                $params .= "$p/";
            }
        }
        return $this->url . $params;
    }

    public function getFullCurrentUrlByLanguage(string $lang = "en"): string
    {
        $params = "";
        if (!empty($this->parameters)) {
            foreach ($this->parameters as $p) {
                $params .= "$p/";
            }
        }
        return $this->getUrl($this->getCurrentAlias(), $lang) . $params;
    }
}