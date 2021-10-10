<?php
declare(strict_types=1);

namespace Delos\Service\Cache;

use Delos\Repository\QueryCacheRepository;

trait Cache
{
    private ?QueryCacheRepository $queryCache;
    private int $time;

    public function setModelQueryCache(QueryCacheRepository $queryCache = null)
    {
        $this->queryCache = $queryCache;
        $this->time = time();
    }

    public function Cache($methodCache, $callerClass, ...$parameters)
    {
        return $this->CacheExecute($methodCache, $callerClass, "", $parameters);
    }

    public function CacheExpirationDate($methodCache, $callerClass, $expireAt, ...$parameters)
    {
        return $this->CacheExecute($methodCache, $callerClass, $expireAt, $parameters);
    }

    private function CacheExecute($methodCache, $callerClass, $expireAt, $parameters)
    {
        $string = "";
        if ($this->queryCache !== null) {
            $string = $this->getParametersString($parameters);
            $cache = $this->queryCache->getCache($methodCache . $string);

            if (!empty($cache) && !$this->isCacheExpired($cache)) {
                return unserialize($cache[0]['cache_value']);
            } else if (!empty($cache)) {
                $this->queryCache->cleanCache($cache[0]['cache_index']);
            }
        }
        $method = preg_replace("#Cache$#", "", $methodCache);
        $method = preg_replace("#Action$#", "", $method);
        $method = preg_replace("#(.)*\:\:#", "", $method);
        $result = call_user_func_array(array($callerClass, $method), $parameters);
        if (!empty($result)) {
            $this->queryCache->storeCache($methodCache . $string, $result, $expireAt);
        }
        return $result;
    }

    private function isCacheExpired($cache): bool
    {
        if (strtotime($cache[0]['expires_at']) > $this->time
            || strtotime($cache[0]['expires_at']) < 0) {
            return false;
        } else {
            return true;
        }
    }

    private function getParametersString($parameters): string
    {
        $string = "";
        foreach ($parameters as $p) {
            if (is_object($p)) {
                $string .= "-" . substr(md5(serialize($p)));
            } else if (is_array($p)) {
                $string .= "-" . substr(md5(implode('-', $p)), -8);
            } else {
                $string .= "-" . (string)$p;
            }
        }
        return $string;
    }
}