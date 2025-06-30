<?php
namespace SED\Facades\DocumentBuilder;

use SED\Documents\Common\Dto\UserRoleAggregator;

class DocumentBuilder
{
	public function directive(): DirectiveBuilder
	{
		return new DirectiveBuilder();
	}
	public function esz(): ESZBuilder
	{
		return new ESZBuilder();
	}
	public function review(): ReviewBuilder
	{
		return new ReviewBuilder();
	}

	public function createUser(int $user_id, bool $can_deletable = true): UserRoleAggregator
	{
		return UserRoleAggregator::createUser($user_id, $can_deletable);
	}

	public function createStaticRole(int $static_role_id, bool $can_deletable = true): UserRoleAggregator
	{
		return UserRoleAggregator::createStaticRole($static_role_id, $can_deletable);
	}

	public function createDynamicRole(int $dynamic_role_id, bool $can_deletable = true): UserRoleAggregator
	{
		return UserRoleAggregator::createDynamicRole($dynamic_role_id, $can_deletable);
	}
}