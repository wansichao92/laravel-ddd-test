<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    public function __construct($index=null, $from=0, $size=10)
    {
        $this->index = $index ?? 'my_index';
        $this->from = $from;
        $this->size = $size;
        $this->client = ClientBuilder::create()->build();
    }

    /**
     * 创建索引
     */
    public function createIndex($mapping=[])
    {
        $params = [
            'index' => $this->index
        ];
        $response = $this->client->indices()->create($params);

        if ($mapping) {
            $params['body']['properties'] = $mapping;
            $response = $this->mapping($params);
        }

        return (string)$response['acknowledged'];
    }

    /**
     * 设置格式
     */
    private function mapping($params)
    {
        return $this->client->indices()->putMapping($params);
    }

    /**
     * 删除索引
     */
    public function deleteIndex()
    {
        $isIndex = $this->get();
        if ($isIndex == "索引:{$this->index}不存在")
            return "索引:{$this->index}不存在";

        $params = [
            'index'=>$this->index
        ];
        $response = $this->client->indices()->delete($params);

        return (string)$response['acknowledged'];
    }

    /**
     * 获取文档
     */
    public function get($id=1)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'client' => [
                'ignore' => 404
            ]
        ];
        $response = $this->client->get($params);

        if (isset($response['error']) && $response['error']['type'] == 'index_not_found_exception')
            return "索引:{$this->index}不存在";

        if (isset($response['found']) && !empty($response['found']))
            return $response['_source'];

        return [];
    }

    /**
     * 搜索文档
     */
    public function search($page, $query=[], $order=[])
    {
        if ($page) {
            $this->from = ($page['page']-1)*$page['pageSize'];
            $this->size = $page['pageSize'];
        }

        $params = [
            'index' => $this->index,
            'body' => [
                "sort" => $order,
                "from" => $this->from,
                "size" => $this->size,
                "query" => $query
            ]
        ];
        $response = $this->client->search($params);

        $data = [
            'count' => $response['hits']['total']['value'],
            'data' => $response['hits']['hits']
        ];

        return $data;
    }

    /**
     * 新增文档
     */
    public function created($id, $body=[])
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'body' => $body
        ];
        $response = $this->client->index($params);

        return $response['_shards'];
    }

    /**
     * 修改文档
     */
    public function update($id, $data)
    {
        $params = [
            'index' => $this->index,
            'id' => $id,
            'body' => [
                'doc' => $data
            ]
        ];
        $response = $this->client->update($params);

        return $response['_shards'];
    }

    /**
     * 删除文档
     */
    public function delete($id)
    {
        $isData = $this->get($id);
        if (!$isData)
            return "id:{$id}不存在，无法删除";

        $params = [
            'index' => $this->index,
            'id' => $id
        ];
        $response = $this->client->delete($params);

        return $response['_shards'];
    }
}
