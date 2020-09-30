<?php

namespace Delos\Service\Cache;

use Delos\Repository\QueryCacheRepository;

trait Cache
{
    /**
     * @var QueryCacheRepository
     */
    private $queryCache;

    /**
     * @var int
     */
    private $time;

    /**
     * Cache constructor.
     * @param QueryCacheRepository $queryCache
     */
    public function setModelQueryCache(QueryCacheRepository $queryCache = null)
    {
        $this->queryCache = $queryCache;
        $this->time = time();
    }

    /**
     * @param $methodCache
     * @param $callerClass
     * @param mixed ...$parameters
     * @return mixed|null
     * @throws \Exception
     */
    public function Cache($methodCache,$callerClass, ...$parameters){
        return $this->CacheExecute($methodCache,$callerClass,"",$parameters);
    }

    /**
     * @param $methodCache
     * @param $callerClass
     * @param $expireAt
     * @param mixed ...$parameters
     * @return mixed|null
     * @throws \Exception
     */
    public function CacheExpirationDate($methodCache,$callerClass,$expireAt, ...$parameters){
        return $this->CacheExecute($methodCache,$callerClass,$expireAt,$parameters);
    }
    /**
     * @param $methodCache
     * @param $callerClass
     * @param $expireAt
     * @param mixed ...$parameters
     * @return mixed|null
     * @throws \Exception
     */
    private function CacheExecute($methodCache,$callerClass,$expireAt, $parameters){
        $string = "";
        if($this->queryCache !== null){
            $string = $this->getParametersString($parameters);
            $cache = $this->queryCache->getCache($methodCache.$string);

            if(!empty($cache) && !$this->isCacheExpired($cache)){
                return unserialize($cache[0]['cache_value']);
            }else if(!empty($cache)){
                $this->queryCache->cleanCache($cache[0]['cache_index']);
            }
        }
        $method = preg_replace("#Cache$#","",$methodCache);
        $method = preg_replace("#Action$#","",$method);
        $method = preg_replace("#(.)*\:\:#","",$method);
        $result = call_user_func_array(array($callerClass,$method),$parameters);
            if (!empty($result)) {
            $this->queryCache->storeCache($methodCache . $string, $result, $expireAt);
            }
        return $result;
    }

    /**
     * @param $cache
     * @return bool
     */
    private function isCacheExpired($cache){
        if(strtotime($cache[0]['expires_at']) > $this->time
            || strtotime($cache[0]['expires_at']) < 0){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param $parameters
     * @return string
     */
    private function getParametersString($parameters){
        $string = "";
        foreach ($parameters as $p){
            if(is_object($p)){
                $string .= "-".substr(md5(serialize($p)));
            }else if(is_array($p)){
                $string .= "-".substr(md5(implode('-', $p)), -8);
            }else{
                $string .= "-".(string) $p;
            }
        }
        return $string;
    }
}