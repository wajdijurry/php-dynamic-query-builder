<?php
/**
 * User: Wajdi Jurry
 * Date: 05/12/18
 * Time: 10:10 م
 */

namespace Wjurry\DBTools\Enums;

class QueryOperatorsEnum
{
    const OP_EQUALS = '=';
    const OP_GREATER_THAN = '>';
    const OP_GREATER_THAN_EQUAL = '>=';
    const OP_LESS_THAN = '<';
    const OP_LESS_THAN_EQUAL = '<=';
    const OP_NOT_EQUAL = '<>';
    const OP_IN = 'IN';
    const OP_NOT_IN = 'NOT IN';
    const OP_BETWEEN = 'BETWEEN';
    const OP_IS_NULL = 'IS NULL';
    const OP_IS_NOT_NULL = 'IS NOT NULL';
}
