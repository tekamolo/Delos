<?php
declare(strict_types=1);

namespace Delos\Model\Cache;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property $cache_index
 * @property $expires_at
 * @property $cache_value
 * @property $inserted_at
 * @property $updated_at
 *
 * Class User
 * @package Obbex\Model
 */

class QueryCache extends Model
{
    protected $table = "query_cache";
    protected $guarded = [];
}