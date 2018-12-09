<?php
/**
 * User: Wajdi Jurry
 * Date: 05/12/18
 * Time: 11:22 م
 */

namespace Wjurry\DBTools;

class RecursiveQueryBuilder extends QueryBuilder
{
    /** @var string $recursionName */
    public $recursionName;

    /** @var array $recursionOrderBy */
    private $recursionOrderBy;

    /** @var int $recursionLimit */
    private $recursionLimit;

    /**
     * RecursiveQueryBuilder constructor.
     * @param string $table
     * @param array $columns
     * @param string $recursionName
     * @param array $options
     * @param array $recursionOrderBy
     * @param array $recursionLimit
     * @throws \Exception Example:
     * new RecursiveQueryBuilder(
     *      'table_name',
     *      ['column1', 'column2', 'column3'],
     *      'recursion_name',
     *      [
     *          [
     *              'CONDITIONS' => [
     *                    'column1' => ['=' => false],
     *                    'column2' => ['BETWEEN' => ['2018-12-01', '2018-12-06']],
     *                    'column3' => ['IN' => ['user_1_id', 'user_2_id']]
     *          ],
     *          [
     *              'UNION' => [
     *                  'type' => 'ALL', (optional)
     *                  'table' => 'table_name',
     *                  'alias' => 't1',
     *                  'columnsAlias' => 't2' // from where to select columns (refers to the join statement's alias)
     *              ]
     *          ],
     *          [
     *              'JOIN' => [
     *                   'table' => 'table_name',
     *                   'alias' => 't2',
     *                   'side' => 'INNER|OUTER|LEFT|RIGHT|FULL OUTER',
     *                   'conditions' => [
     *                         // 'process = false' (optional): returns the condition as is without binding values
     *                        'c1.column1' => ['=' => 't2.column2', 'process' => false]
     *                   ]
     *              ]
     *          ]
     *      ],
     *      [
     *          'column' => 'order_by_column',
     *          'direction' => 'ASC|DESC'
     *      ],
     *      [
     *          'limit' => 1,
     *          'offset' => 1
     *      ]
     * );
     */
    public function __construct(string $table, array $columns, string $recursionName, array $options = [], array $recursionOrderBy = [], array $recursionLimit = [])
    {
        if (empty($recursionName)) {
            throw new \Exception('You have to define recursion name', 500);
        }
        $this->recursionName = $recursionName;
        $this->recursionOrderBy = $recursionOrderBy;
        $this->recursionLimit = $recursionLimit;
        parent::__construct($table, $columns, $options);
    }

    /**
     * @param string $alias
     * @return string
     */
    protected function getColumns($alias = ''): string
    {
        return implode(',', array_map(function ($column) {
            return substr($column, strpos($column, '.')+1, strlen($column));
        }, explode(',', parent::getColumns($alias))));
    }

    /**
     * @param bool $endStatement
     * @return string
     * @throws \Exception
     */
    protected function createQuery(bool $endStatement = true)
    {
        $query = 'WITH RECURSIVE '.$this->recursionName.' ('.$this->getColumns().')
                AS ('.parent::createQuery(false).')
                    SELECT * FROM '.$this->recursionName;

        if ($this->recursionOrderBy) {
            $query .= ' ORDER BY ' . $this->recursionOrderBy['column'] . ' ' . $this->recursionOrderBy['direction'];
        }

        if ($this->recursionLimit) {
            $query .= ' LIMIT ' . $this->recursionLimit['limit'];
            if ($this->recursionLimit['offset']) {
                $query .= ' OFFSET ' . $this->recursionLimit['offset'];
            }
        }

        return $this->query = $query.';';
    }
}
