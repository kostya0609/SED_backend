<?php
namespace SED\DocumentRoutes\Features\ApprovalRoutes\Controllers;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\DocumentRoutes\Features\ApprovalRoutes\Services\ApprovalRouteService;

class ApprovalRouteController extends BaseController
{
	private ApprovalRouteService $service;

	public function __construct(ApprovalRouteService $service)
	{
		$this->service = $service;
	}

	public function getAll(Request $request)
	{
		$request->validate(
			[
				'tmp_doc_id' => 'required|integer',
				'process_template_id' => 'nullable|integer',
			],
			[
				'tmp_doc_id.required' => 'Идентификатор шаблона документ обязателен!',
				'tmp_doc_id.integer' => 'Идентификатор шаблона должен быть целым числом!',

				'process_template_id' => 'Идентификатор шаблона процесса обязателен!',
				'process_template_id.integer' => 'Идентификатор шаблона процесса должен быть целым числом!',
			]
		);

		return $this->sendResponse($this->service->getAll($request->input('tmp_doc_id'), $request->input('process_template_id')));
	}

	public function create(Request $request)
	{
		$validated = (object) $request->validate(
			[
				'tmp_doc_id' => 'required|integer',
				'title' => 'required|string|max:255',
				'process_template_id' => 'required|integer',
				'is_active' => 'required|boolean',
				'stages' => 'required|array',
				'stages.*.groups' => 'required|array',
			],
			[
				'tmp_doc_id.required' => 'Идентификатор шаблона документа обязателен!',
				'tmp_doc_id.integer' => 'Идентификатор шаблона документа должен быть целым числом!',
				'title.required' => 'Название маршрута обязательно!',
				'title.string' => 'Название маршрута должно быть строкой!',
				'title.max' => 'Название маршрута не может быть длиннее 255 символов!',
				'process_template_id.required' => 'Идентификатор шаблона процесса обязателен!',
				'process_template_id.integer' => 'Идентификатор шаблона процесса должен быть целым числом!',
				'is_active.required' => 'Активность маршрута обязательна!',
				'is_active.boolean' => 'Активность маршрута должна быть логическим значением!',
				'stages.required' => 'Этапы обязательны!',
				'stages.array' => 'Этапы должны быть массивом!',
				'stages.*.groups.required' => 'Группы этапа обязательны!',
                'stages.*.groups.array' => 'Группы этапа должны быть массивом!',
			]
		);

		return $this->sendResponse(
			$this->service->create(
				$validated->tmp_doc_id,
				$validated->title,
				$validated->process_template_id,
				$validated->is_active,
				$validated->stages
			)
		);
	}

	public function update(Request $request)
	{
		$validated = (object) $request->validate(
			[
				'id' => 'required|integer',
				'title' => 'required|string|max:255',
				'is_active' => 'required|boolean',
				'stages' => 'required|array',
				'stages.*.groups' => 'required|array',
			],
			[
				'id.required' => 'Идентификатор маршрута обязателен!',
				'id.integer' => 'Идентификатор маршрута должен быть целым числом!',
				'title.required' => 'Название маршрута обязательно!',
				'title.string' => 'Название маршрута должно быть строкой!',
				'title.max' => 'Название маршрута не может быть длиннее 255 символов!',
				'is_active.required' => 'Активность маршрута обязательна!',
				'is_active.boolean' => 'Активность маршрута должна быть логическим значением!',
				'stages.required' => 'Этапы обязательны!',
				'stages.array' => 'Этапы должны быть массивом!',
				'stages.*.groups.required' => 'Группы этапа обязательны!',
                'stages.*.groups.array' => 'Группы этапа должны быть массивом!',
			]
		);

		return $this->sendResponse(
			$this->service->update(
				$validated->id,
				$validated->title,
				$validated->is_active,
				$validated->stages
			)
		);
	}

	public function delete(Request $request)
	{
		$validated = (object) $request->validate(
			[
				'id' => 'required|integer',
				'tmp_doc_id' => 'required|integer',
			],
			[
				'id.required' => 'Идентификатор маршрута обязателен!',
				'id.integer' => 'Идентификатор маршрута должен быть целым числом!',
				'tmp_doc_id.required' => 'Идентификатор шаблона документа обязателен!',
				'tmp_doc_id.integer' => 'Идентификатор шаблона документа должен быть целым числом!',
			]
		);

		$this->service->delete($validated->id, $validated->tmp_doc_id);

		return $this->sendResponse();
	}
}