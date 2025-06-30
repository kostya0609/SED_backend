<?php
namespace SED\Documents\Common\Dto;

class UserRoleAggregator
{
	public const IS_USER = 1;
	public const IS_STATIC_ROLE = 2;
	public const IS_DYNAMIC_ROLE = 3;

	public int $id;
	public int $type_id;
	public ?int $user_id;
	public ?int $static_role_id;
	public ?int $dynamic_role_id;
	public bool $can_deletable;

	public function __construct(array $data)
	{
		$this->id = $data['id'];
		$this->type_id = $data['type_id'];
		$this->user_id = $data['user_id'] ?? null;
		$this->static_role_id = $data['static_role_id'] ?? null;
		$this->dynamic_role_id = $data['dynamic_role_id'] ?? null;
		$this->can_deletable = $data['can_deletable'];

		if (!in_array($data['type_id'], [self::IS_USER, self::IS_STATIC_ROLE, self::IS_DYNAMIC_ROLE])) {
			throw new \InvalidArgumentException('Неизвестный тип участника: ' . $data['type_id']);
		}
	}

	public static function createUser(int $user_id, bool $can_deletable = true): UserRoleAggregator
	{
		return new self([
			'id' => $user_id . self::IS_USER,
			'type_id' => self::IS_USER,
			'user_id' => $user_id,
			'static_role_id' => null,
			'dynamic_role_id' => null,
			'can_deletable' => $can_deletable,
		]);
	}
	public static function createStaticRole(int $static_role_id, bool $can_deletable = true): UserRoleAggregator
	{
		return new self([
			'id' => $static_role_id . self::IS_STATIC_ROLE,
			'type_id' => self::IS_STATIC_ROLE,
			'user_id' => null,
			'static_role_id' => $static_role_id,
			'dynamic_role_id' => null,
			'can_deletable' => $can_deletable,
		]);
	}
	public static function createDynamicRole(int $dynamic_role_id, bool $can_deletable = true): UserRoleAggregator
	{
		return new self([
			'id' => $dynamic_role_id . self::IS_DYNAMIC_ROLE,
			'type_id' => self::IS_DYNAMIC_ROLE,
			'user_id' => null,
			'static_role_id' => null,
			'dynamic_role_id' => $dynamic_role_id,
			'can_deletable' => $can_deletable,
		]);
	}

	public function isUser()
	{
		return $this->type_id === self::IS_USER;
	}

	public function isStaticRole()
	{
		return $this->type_id === self::IS_STATIC_ROLE;
	}

	public function isDynamicRole()
	{
		return $this->type_id === self::IS_DYNAMIC_ROLE;
	}
}