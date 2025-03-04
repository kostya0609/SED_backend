<?php
namespace SED\Documents\Common\Services;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserItemDto;
use App\Modules\Roles\Facades\StaticRoleFacade;
use App\Modules\Roles\Facades\DynamicRoleFacade;
use SED\Documents\Common\Dto\UserRoleAggregator;
use SED\Documents\Common\Exceptions\ManyUsersException;

/**
 * Сервис для агрегации и извлечения информации о пользователях и их ролях.
 *
 * Этот класс предоставляет методы для работы с объектами UserRoleAggregator,
 * извлечения информации о пользователях на основе их ролей и идентификаторов.
 * 
 * TODO: Протестировать и перенести класс в модуль ролей
 */
class UserRoleAggregatorService
{
	private ?int $document_initiator_id = null;

	public function setDocumentInitiator(int $document_initiator_id)
	{
		$this->document_initiator_id = $document_initiator_id;
	}

	/**
	 * Извлекает информацию о пользователе из объекта UserRoleAggregator.
	 *
	 * @param UserRoleAggregator $user_role_aggregator Объект, содержащий информацию о пользователе и его ролях
	 * @param int $user_id Идентификатор пользователя, по которому будут извлекаться динамические роли (обычно инициатор документа)
	 * @return UserItemDto|null Объект с информацией о пользователе или null, если пользователь не найден
	 * @throws ManyUsersException Если найдено более одного пользователя
	 */
	public function extractUser(UserRoleAggregator $user_role_aggregator, int $user_id): ?UserItemDto
	{
		$users = $this->getUsersByParticipantItem($user_role_aggregator, $user_id);

		if ($users->count() > 1) {
			throw new ManyUsersException('Получено больше 1 участника!');
		}

		if ($users->isEmpty()) {
			return null;
		}

		return new UserItemDto($users->first(), $user_role_aggregator->can_deletable);
	}

	/**
	 * Извлекает информацию о нескольких пользователях из коллекции объектов UserRoleAggregator.
	 *
	 * @param Collection<UserRoleAggregator> $user_roles Коллекция объектов с информацией о пользователях и их ролях
	 * @param int $user_id Идентификатор пользователя, по которому будут извлекаться динамические роли (обычно инициатор документа)
	 * @return Collection<UserItemDto> Коллекция объектов с информацией о пользователях
	 */
	public function extractManyUsers(Collection $user_roles, int $user_id): Collection
	{
		$user_items = new Collection();

		foreach ($user_roles as $user_role) {
			$users = $this->getUsersByParticipantItem($user_role, $user_id);

			foreach ($users as $user_id) {
				if (!$user_items->contains('user_id', $user_id)) {
					$participant = new UserItemDto($user_id, $user_role->can_deletable);
					$user_items->push($participant);
				}
			}
		}

		return $user_items;
	}

	/**
	 * Получает коллекцию идентификаторов пользователей на основе объекта UserRoleAggregator.
	 *
	 * @param UserRoleAggregator $user_role_aggregator Объект, содержащий информацию о пользователе и его ролях
	 * @param int $user_id Идентификатор пользователя, по которому будут извлекаться динамические роли (обычно инициатор документа)
	 * @return Collection Коллекция идентификаторов пользователей
	 * @throws \LogicException Если тип участника неизвестен
	 */
	private function getUsersByParticipantItem(UserRoleAggregator $user_role_aggregator, int $user_id): Collection
	{
		$users = new Collection();

		if ($user_role_aggregator->isUser()) {
			$users->push($user_role_aggregator->user_id);
		} else if ($user_role_aggregator->isStaticRole()) {
			$_users = StaticRoleFacade::getUsersByRoleId($user_role_aggregator->static_role_id);

			foreach ($_users as $user) {
				$users->push($user->id);
			}
		} else if ($user_role_aggregator->isDynamicRole()) {
			$_users = DynamicRoleFacade::getUsersByRoleId($user_role_aggregator->dynamic_role_id, $user_id, [
				'document_initiator_id' => $this->document_initiator_id
			]);

			foreach ($_users as $user) {
				$users->push($user->id);
			}
		} else {
			throw new \LogicException("Неизвестный тип участника: {$user_role_aggregator->type_id}");
		}

		return $users;
	}
}