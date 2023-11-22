<?php

declare(strict_types=1);

namespace App\Model\Orm\OrderItems;

use Nextras\Orm\Entity\Entity;
use App\Model\Orm\Orders\Order;
use App\Model\Orm\Products\Product;


/**
 * @property int $id {primary}
 * @property int $quantity {default 1}
 * @property Product $product {m:1 Product, oneSided=true}
 * @property Order $order {m:1 Order::$orderItems}
 */
class OrderItem extends Entity
{
}