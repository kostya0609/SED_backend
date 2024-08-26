<?php

namespace SED\DocumentRoutes\Features\Partitions\Services;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\Partitions\Dto\{
	CreatePartitionDto,
	EditPartitionDto,
};
use SED\DocumentRoutes\Features\Partitions\Models\Partition;
use SED\DocumentRoutes\VerificationService;

class PartitionService
{
	protected VerificationService $verificationService;

	public function __construct(VerificationService $verificationService)
	{
		$this->verificationService = $verificationService;
	}

	public function create(CreatePartitionDto $dto): Partition
	{
		$partition = new Partition();

		$partition->title = $dto->title;
		$partition->parent_id = $dto->parent_id ?? null;

		$partition->save();

		return $partition;
	}

	public function edit(EditPartitionDto $dto): Partition
	{
		$partition = Partition::find($dto->id);

		if (!$partition) {
			throw new \Exception("Не удалось найти раздел по id $dto->id");
		}

		$partition->title = $dto->title;
		$partition->parent_id = $dto->parent_id;

		$partition->save();

		return $partition;
	}

	public function delete(int $id): void
	{
		$partition = Partition::find($id);

		if (!$partition) {
			throw new \Exception("Не удалось найти раздел по id $id");
		}

		$partition->delete();
	}

	public function getTree(): Collection
	{
		$tree = Partition::query()
			->whereNull('parent_id')
			->with(['routes'])
			->get();

		return $tree;
	}

	public function get(int $id): Partition
	{
		$partition = Partition::find($id);

		if (!$partition) {
			throw new \Exception("Не удалось найти раздел по id $id");
		}

		return $partition;
	}
}
