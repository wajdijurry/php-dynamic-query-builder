# php-dynamic-query-builder v1.0
- Package includes:
    - Dynamic MySQL query builder
    - Dynamic MySQL Recursive Query Builder

- Requirements:
    - PHP 7.x
    - MySQL 8.x for RecursiveQueryBuilder
    
# Examples:
##### Query Builder:
    <?php
        /**
        * An Example usage of QueryBuilder
        */
        use Wjurry\DBTools\QueryBuilder;
        use Wjurry\DBTools\Enums\QueryOperatorsEnums;
        
        $queryBuilder = new QueryBuilder(
            'suppliers',
            ['supplier_id', 'supplier_name'],
            [
                [
                    'CONDITIONS' => [
                        'supplier_id' => [QueryOperatorsEnum::OP_LESS_THAN_EQUAL => 500],
                        'is_deleted' => [QueryOperatorsEnum::OP_EQUALS => false]
                    ]
                ],
                [
                    'UNION' => [
                        'type' => 'ALL',
                        'table' => 'companies',
                        'columns' => ['company_id', 'company_name'],
                        'alias' => 'c'
                    ]
                ],
                [
                    'JOIN' => [
                        'table' => 'stores',
                        'alias' => 's2',
                        'side' => 'LEFT',
                        'conditions' => [
                             's2.supplier_id' => [QueryOperatorsEnum::OP_EQUALS => 'c.company_id', 'process' => false],
                             's2.is_deleted' => [QueryOperatorsEnum::OP_EQUALS => false]
                        ]
                    ]
                ]
            ],
            [
                'column' => 'supplier_id',
                'direction' => 'DESC'
            ],
            [
                'limit' => 100,
                'offset' => 0
            ]
        );
        
        $query = $queryBuilder->getQuery();
        // SELECT s8.supplier_id,s8.supplier_name FROM suppliers AS s8
        // WHERE s8.supplier_id <= :YGV0 AND s8.is_deleted = :TCP0
        // UNION ALL SELECT c.supplier_id,c.supplier_name FROM companies AS c
        // LEFT JOIN stores AS s2 ON s2.supplier_id = c.company_id AND s2.is_deleted = :QRI0
        // ORDER BY s8.supplier_id DESC LIMIT 100;
        
        $binds = $queryBuilder->getbinds();
        // array (
        //   'YGV0' => 500,
        //   'TCP0' => false,
        //   'QRI0' => false,
        // )
        
##### Recursive Query Builder
       <?php
       /**
       * An Example usage of QueryBuilder
       */
       use Wjurry\DBTools\QueryBuilder;
       use Wjurry\DBTools\Enums\QueryOperatorsEnums;
       
       $queryBuilder = new RecursiveQueryBuilder(
           'categories',
           ['category_id', 'category_parent_id', 'category_order'],
           'category_recursive_query',
           [
               [
                   'CONDITIONS' => [
                       'category_id' => [QueryOperatorsEnum::OP_EQUALS => '100'],
                       'is_deleted' => [QueryOperatorsEnum::OP_EQUALS => false]
                   ]
               ],
               [
                   'UNION' => [
                       'type' => 'ALL',
                       'table' => 'categories',
                       'alias' => 'c0',
                       'columnsAlias' => 'c' // refers to JOIN statement's alias
                   ]
               ],
               [
                   'JOIN' => [
                       'table' => 'categories',
                       'alias' => 'c',
                       'conditions' => [
                           'c.category_id' => [QueryOperatorsEnum::OP_EQUALS => 'c0.category_parent_id', 'process' => false], // c0 here refers to UNION statement's alias
                           'c.is_deleted' => [QueryOperatorsEnum::OP_EQUALS => false]
                       ]
                   ]
               ]
           ],
           [
               'column' => 'category_order',
               'direction' => 'ASC'
           ],
           [
               'limit' => 10,
               'offset' => 0
           ]
       );
       
       $query = $queryBuilder->getQuery();
       // WITH RECURSIVE category_recursive_query (category_id,category_parent_id,category_order)
       // AS (SELECT c9.category_id,c9.category_parent_id,c9.category_order FROM categories AS c9
       // WHERE c9.category_id = :RVF0 AND c9.is_deleted = :NKX0
       // UNION ALL SELECT c.category_id,c.category_parent_id,c.category_order FROM categories AS c0
       // JOIN categories AS c ON c.category_id = c0.category_parent_id AND c.is_deleted = :CLK0)
       // SELECT * FROM category_recursive_query ORDER BY category_order ASC LIMIT 10;
       
       $binds = $queryBuilder->getbinds();
       // array (
       //   'RVF0' => '100',
       //   'NKX0' => false,
       //   'CLK0' => false,
       // )
       
---
       
- To open issues, please submit on this link: https://github.com/wajdijurry/php-dynamic-query-builder/issues
- Any contributions are welcome
