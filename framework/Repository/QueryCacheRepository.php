<?php
declare(strict_types=1);

namespace Delos\Repository;

use Delos\Model\Cache\QueryCache;

final class QueryCacheRepository implements RepositoryInterface
{

    /**
     * @param $index
     * @return mixed
     */
    public function getCache($index)
    {
        return QueryCache::where("cache_index", "=", $index);
    }

    /**
     * @param $index
     * @param $value
     * @param bool $expirationIn
     */
    public function storeCache($index, $value, $expirationIn = false)
    {
        $queryCache = new QueryCache();
        $queryCache->cache_index = $index;
        $queryCache->cache_value = serialize($value);
        $queryCache->expires_at = $datetimeExpiration = $this->getExpirationDatetime($expirationIn);
        $queryCache->save();
    }

    /**
     * @param $expireIn
     * @return false|int
     */
    private function getExpirationDatetime($expireIn){
        if(empty($expireIn)) return "0000-00-00 00:00:00";
        return $expireIn;
    }

    /**
     * @param $cache_index
     * @return mixed
     */
    public function cleanCache($cache_index){
        return QueryCache::where("cache_index","=",$cache_index)->delete();
    }
}