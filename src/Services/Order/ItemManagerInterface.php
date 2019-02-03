<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 2019-01-12
 * Time: 12:54
 */

namespace App\Services\Order;

use App\Entity\Item;
use App\Entity\OrderVersion;

interface ItemManagerInterface
{
    public function save(Item $order): Item;
    public function remove(Item $order): void;
    public function setOriginalItems(OrderVersion $orderVersion): void;
    public function removeOldItems(OrderVersion $orderVersion): OrderVersion;
    public function checkStateUpdateQuantity(OrderVersion $orderVersion): void;
    public function addOriginalItems(OrderVersion &$orderVersion): void;
}
