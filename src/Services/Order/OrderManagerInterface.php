<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 2019-01-12
 * Time: 12:54
 */

namespace App\Services\Order;

use App\Entity\Order;

interface OrderManagerInterface
{
    public function save(Order $order): Order;
    public function remove(Order $order): void;
}
