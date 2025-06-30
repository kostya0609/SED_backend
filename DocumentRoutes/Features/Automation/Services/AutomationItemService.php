<?php
namespace SED\DocumentRoutes\Features\Automation\Services;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\AutomationSetting;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;
use SED\Documents\Common\Models\Document;
use SED\Documents\Common\Services\BasedCreation\BasedCreationService;

class AutomationItemService
{
	private AutomationService $automationService;
	private BasedCreationService $basedCreationService;

	public function __construct(AutomationService $automationService, BasedCreationService $basedCreationService)
	{
		$this->automationService = $automationService;
		$this->basedCreationService = $basedCreationService;
	}

	/**
	 * Автозапуск (автоматическое создание экземпляров документов).
	 * Создает экземпляр документа маршрута со всеми настройками после успешного завершения родительского документа в маршруте.
	 * Ищет дочерние шаблоны документов с включенным автозапуском и создает их экземпляры.
	 */
	public function autorun_old(int $tmp_doc_id, int $common_document_id): Collection
	{
		/**
		 * sql запрос id шаблонов документов, в котором учитывается,
		 * что у одного шаблона документа может быть несколько родительских
		 * и в каждой ветки у одного и того же шаблона документа может быть разная иерархия.
		 */

		$child_template_ids = \DB::table('l_route_tmp_doc_relations')
			->select('child_template_id')
			->where('parent_template_id', $tmp_doc_id)
			->where('root_template_id', function ($query) use ($common_document_id) {
				$query->select('tmp_doc_id')
					->from('l_sed_documents')
					->whereIn('id', function ($subquery) use ($common_document_id) {
						$subquery->select('start_document_id')
							->from('l_sed_document_hierarchy')
							->where('document_id', $common_document_id);
					});
			})
			->pluck('child_template_id');

		$autorun_templates = collect([]);

		foreach ($child_template_ids as $template_id) {
			$is_active = $this->automationService->getSetting($template_id, AutomationSetting::AUTORUN)->is_active;

			if ($is_active) {
				$autorun_templates->push($template_id);
			}
		}

		return $this->basedCreationService->createFrom($common_document_id, $autorun_templates->toArray());
	}

    public function autorun(int $tmp_doc_id, int $common_document_id): Collection
    {
//        $tmp_doc_id = 217;
//        $common_document_id = 2158;

        $tmp = DocumentTemplate::find($tmp_doc_id);
        $common_document = Document::find($common_document_id);
        $root_tmp_id = $common_document->root_tmp_id;

        if(!$root_tmp_id)
        {
            return collect();
        }

        $regularChildren = $tmp->children ?? collect();
        $childTemplatePartitions = $tmp->childrenTemplatePartitions ?? collect();
        $allChildren = $regularChildren->merge($childTemplatePartitions);

        $autorun_templates = collect();
        $collectChildrenId = collect();
        foreach ($allChildren as $child)
        {
            if($child->pivot->child_template_type === 'partition')
            {
                $collectChildrenId = $collectChildrenId->merge($this->filterChildrenTemplatePartitionsAutomation($allChildren, $root_tmp_id, $collectChildrenId));
            }
            else
            {
                $collectChildrenId->push($child->pivot->child_template_id);
            }
        }
        $collectChildrenId = $collectChildrenId->unique()->values();
        foreach($collectChildrenId as $template_id)
        {
            $is_active = $this->automationService->getSetting($template_id, AutomationSetting::AUTORUN)->is_active;

            if ($is_active) {
                $autorun_templates->push($template_id);
            }
        }
        return $this->basedCreationService->createFrom($common_document_id, $autorun_templates->toArray());


	}

    public function filterChildrenTemplatePartitionsAutomation(Collection $children, int $root_id,Collection $collectChildrenId):Collection
    {
        if ($children->isEmpty()) {
            return collect();
        }
        $children = $children->sortBy('title')
            ->filter(function($child) use ($root_id)
            {
                return isset($child->pivot)
                    && $child->pivot->root_template_id === $root_id
                    && $child->is_active;
            });
        foreach ($children as $child)
        {
            $children = $child->children ?? collect();
            $childTemplatePartitions = $child->childrenTemplatePartitions ?? collect();
            if ($child->pivot->child_template_type === 'partition')
            {
                $allChildren = $children->merge($childTemplatePartitions);
                $children = $this->filterChildrenTemplatePartitionsAutomation($allChildren, $root_id, $collectChildrenId);
                if($children->isNotEmpty())
                {
                    $collectChildrenId = $collectChildrenId->merge($children);
                }
            }

            if($child->pivot->child_template_type === 'template')
            {
                $collectChildrenId->push($child->pivot->child_template_id);
            }
        }
        return $collectChildrenId;
    }
}
