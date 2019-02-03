<?php
/**
 * Created by PhpStorm.
 * User: lsimonin
 * Date: 2018-12-03
 * Time: 14:32
 */

declare(strict_types=1);

namespace App;

final class OrderEvents
{
    const ORDER_STATE_UPDATED = 'order.state.updated';
    const ORDER_DELETE = 'order.delete';
}
