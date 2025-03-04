<?php

namespace SED\DocumentRoutes\Features\Partitions\Services;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\Partitions\Dto\{
	CreatePartitionDto,
	EditPartitionDto,
};
use SED\DocumentRoutes\Features\Partitions\Models\{Partition, SimplePartitionTree};
use SED\Common\Exceptions\NotFoundException;

class PartitionService
{
	protected TreeCreatorService $treeCreatorService;

	public function __construct(TreeCreatorService $treeCreatorService)
	{
		$this->treeCreatorService = $treeCreatorService;
	}

	public function create(CreatePartitionDto $dto): Partition
	{
		$partition = new Partition();

		$partition->title = $dto->title;
		$partition->parent_id = $dto->parent_id ?? null;

		$partition->creator_id = $dto->user_id;
		$partition->last_editor_id = $dto->user_id;
		$partition->is_active = $dto->is_active;

		$partition->save();

		return $partition;
	}

	public function edit(EditPartitionDto $dto): Partition
	{
		$partition = Partition::find($dto->id);

		if (!$partition) {
			throw new NotFoundException("Не удалось найти раздел по id $dto->id");
		}

		if (isset($dto->title)) {
			$partition->title = $dto->title;
		}


		if (isset($dto->parent_id)) {
			$partition->parent_id = $dto->parent_id;
		}

		$partition->last_editor_id = $dto->user_id;
		$partition->is_active = $dto->is_active;

		$partition->save();

		return $partition;
	}

	public function delete(int $id): void
	{
		\DB::transaction(function () use ($id) {
			$partition = Partition::find($id);

			if (!$partition) {
				throw new NotFoundException("Не удалось найти раздел по id $id");
			}

			try {
				$partition->delete();
			} catch (\Illuminate\Database\QueryException $e) {
				
				if ($e->getCode() == 23000) { // код означает нарушение ограничения целостности
					throw new \DomainException('Невозможно удалить раздел с подразделами/маршрутами!');
				}

				throw $e;
			}
		});
	}

	public function getTree(): Collection
	{
		$tree = SimplePartitionTree::query()
			->whereNull('parent_id')
			->get();

		return $tree;
	}

	public function getBreadcrumbs(?int $partition_id): Collection
	{
		$breadcrumbs = collect([]);

		if (is_null($partition_id)) {

			$breadcrumbs->prepend(new Partition(['title' => 'Маршруты', 'parent_id' => null]));

		} else {

			$partition = Partition::findOrFail($partition_id);
			$breadcrumbs->prepend($partition);
			while ($partition->parent_id) {
				$partition = Partition::findOrFail($partition->parent_id);
				$breadcrumbs->prepend($partition);
			}
			$breadcrumbs->prepend(new Partition(['title' => 'Маршруты', 'parent_id' => null]));

		}

		return $breadcrumbs;
	}

	public function getTreeForSelectTemplate(int $user_id): Collection
	{
		return $this->treeCreatorService->createTree($user_id);
	}

	public function get(int $id): Partition
	{
		$partition = Partition::with('children')->find($id);

		if (!$partition) {
			throw new NotFoundException("Не удалось найти раздел по id $id");
		}

		return $partition;
	}
}
