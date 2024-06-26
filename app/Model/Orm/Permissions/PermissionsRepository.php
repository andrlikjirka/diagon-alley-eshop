<?php

declare(strict_types=1);

namespace App\Model\Orm\Permissions;

use Nextras\Orm\Repository\Repository;

class PermissionsRepository extends Repository
{
	public static function getEntityClassNames(): array
	{
		return [Permission::class];
	}
}
