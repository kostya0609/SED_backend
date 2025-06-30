<?php

namespace App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Services;

use App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Models\TemplatePartition;
use Illuminate\Support\Facades\DB;
use SED\Common\Exceptions\NotFoundException;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplateRelation;
use SED\DocumentRoutes\Features\TemplatePartitions\Dto\CreateTemplatePartitionDto;
use SED\DocumentRoutes\Features\TemplatePartitions\Dto\DeleteTemplatePartitionDto;
use SED\DocumentRoutes\Features\TemplatePartitions\Dto\EditTemplatePartitionDto;

class TemplatePartitionService
{
    public function create(CreateTemplatePartitionDto $dto): TemplatePartition
    {
        return DB::transaction(function () use ($dto): TemplatePartition
        {
            $tmp_partition                      = new TemplatePartition();
            $tmp_partition->title               = $dto->title;
            $tmp_partition->route_id            = $dto->route_id;
            $tmp_partition->creator_id          = $dto->user_id;
            $tmp_partition->last_editor_id      = $dto->user_id;
            $tmp_partition->parent_template_id  = $dto->parent_template_id;
            $tmp_partition->save();

            $parent = [
                'id'                    => (int) ($dto->parent_id . $dto->root_id),
                'parent_template_id'    => $dto->parent_id,
                'child_template_id'     => $tmp_partition->id,
                'root_template_id'      => $dto->root_id,
                'parent_template_type'  => $dto->parent_type,
                'child_template_type'   => 'partition',
            ];

            DocumentTemplateRelation::insert($parent);

            //Получение id ветки
//            $documentTemplateRelation = DocumentTemplateRelation::where([
//                ['root_template_id','=',$dto->root_id],
//                ['parent_template_id','=',$dto->parent_id],
//            ])->first();
            $tmp_partition->branch_id = $parent['id'];

            return $tmp_partition;
        });
    }

    public function edit(EditTemplatePartitionDto $dto)
    {
        return DB::transaction(function () use ($dto): TemplatePartition {
            $tmp_partition = TemplatePartition::find($dto->template_partition_id);
            $tmp_partition->title = $dto->title;
            $tmp_partition->last_editor_id = $dto->user_id;
            $tmp_partition->save();

            $tmp_partition->branch_id = (int) ($dto->parent_id . $dto->root_id);

            return $tmp_partition;
        });
    }

    public function delete(DeleteTemplatePartitionDto $dto)
    {
        return DB::transaction(function () use ($dto): TemplatePartition {

            $tmp_partition = TemplatePartition::find($dto->template_partition_id);

            if (!$tmp_partition) {
                throw new NotFoundException("Не удалось найти шаблон группы по id $dto->template_partition_id");
            }

            if ($tmp_partition->children->isNotEmpty() || $tmp_partition->childrenTemplatePartitions->isNotEmpty()) {
                throw new \DomainException("Нельзя удалить шаблон группы, так как он имеет дочерние шаблоны! Начните удаление с последнего элемента в ветки или проверьте другие ветки.");
            }
            $tmp_partition->children()->sync([]);
            $tmp_partition->childrenTemplatePartitions()->sync([]);
            $tmp_partition->delete();

            $tmp_partition->branch_id = (int) ($dto->parent_id . $dto->root_id);

            return $tmp_partition;
        });
    }
}
