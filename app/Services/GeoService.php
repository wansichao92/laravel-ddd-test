<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class GeoService
{
    protected $redis;

    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    /**
     * 通过当前经纬度 获取附近的列表
     * @param array $cache_list key
     * @param array $long_site 经度
     * @param array $lati_site 纬度
     */
    public function getGeoList($cache_list, $long_site, $lati_site)
    {
        return $this->redis->georadius($cache_list, $long_site, $lati_site, '1000000000000', 'm', 'WITHDIST', 'ASC', 'COUNT', 20);
    }

    /**
     * 写入位置信息 ，也可用于编辑时自动会覆盖
     * @param $cache_list key
     * @param $long_site 经度
     * @param $lati_site 纬度
     * @param $team_id 团长ID
     */
    public function addGeo($cache_list,$long_site,$lati_site,$team_id)
    {
        if (empty($lati_site) || empty($lati_site) || empty($team_id) || empty($cache_list)) throw new \Exception('参数不完整');

        return $this->redis->geoadd($cache_list, $long_site, $lati_site, $team_id);
    }

    /**
     * 清除缓存列表
     * @param $cache_list key
     */
    public function clearGeo($cache_list)
    {
        return $this->redis->del($cache_list);
    }

    /**
     * 删除一条geo信息
     * @param $cache_list
     * @param $key
     */
    public function delOneGeo($cache_list,$key)
    {
        return $this->redis->zrem($cache_list, $key);
    }

    /**
     * 获取一条redis geo
     * @param $cache_list
     * @param $key
     */
    public function getOneGeo($cache_list,$key)
    {
        return $this->redis->geopos($cache_list, $key);
    }
}
