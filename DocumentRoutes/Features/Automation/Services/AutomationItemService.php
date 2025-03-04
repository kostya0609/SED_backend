<?php
namespace SED\DocumentRoutes\Features\Automation\Services;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\AutomationSetting;
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
	public function autorun(int $tmp_doc_id, int $common_document_id): Collection
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
}