<?php

declare(strict_types=1);

namespace App\Model\Orm\FavouriteProducts;

use Nextras\Orm\Entity\Entity;
use App\Model\Orm\Products\Product;
use App\Model\Orm\Users\User;


/**
 * @property int $id {primary}
 * @property Product $product {m:1 Product, oneSided=true}
 * @property User $user {m:1 User::$favouriteProducts}
 */
class FavouriteProduct extends Entity
{
}
