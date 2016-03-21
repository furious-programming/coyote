<?php

namespace Coyote\Elasticsearch\Aggs;

use Coyote\Elasticsearch\Aggs;
use Coyote\Elasticsearch\DslInterface;
use Coyote\Elasticsearch\QueryBuilderInterface;

class Nested extends Aggs implements DslInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @return array
     */
    public function apply(QueryBuilderInterface $queryBuilder)
    {
        $body = $queryBuilder->getBody();

        $body['aggs'] = [
            $this->name => [
                'nested' => [
                    'path' => $this->name
                ],
                'aggs' => [
                    $this->field => [
                        'terms' => [
                            'field' => $this->field
                        ]
                    ]
                ]
            ]
        ];

        return $body;
    }
}