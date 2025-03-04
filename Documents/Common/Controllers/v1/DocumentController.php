<?php
namespace SED\Documents\Common\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\Documents\Common\Dto\FilterDocumentsDto;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Common\Services\DocumentGridService;

class DocumentController extends BaseController
{
	protected DocumentService $service;
	protected DocumentGridService $documentGridService;

	public function __construct(DocumentService $service, DocumentGridService $documentGridService)
	{
		$this->service = $service;
		$this->documentGridService = $documentGridService;
	}
	public function create(Request $request)
	{
	}

	public function getAll(Request $request)
	{
		// TODO: Добавить валидацию
		$dto = new FilterDocumentsDto();

		$dto->order = $request->sort['order'] ?: 'asc';
		$dto->sort = $request->sort['name'] ?: 'id';

		if ($dto->sort === 'type') {
			$dto->sort = 'type_id';
		}

		$dto->limit = $request->count;
		$dto->offset = ($request->page - 1) * $dto->limit;
		$dto->filters = $request->input('filter', []);
		$dto->user_id = $request->input('user_id');

		$documents = $this->service->getAll($dto);
		$documents->items = $this->documentGridService->toGrid($documents->items);

		return $this->sendResponse($documents);
	}

	public function getAllV2(Request $request){
		$validated = (object) $request->validate(
			[
				'user_id' => 'required|integer',
			],
			[
				'user_id.required' => 'Поле user_id обязательно!',
                'user_id.integer' => 'Поле user_id должно быть числовым!',
			]
		);

		$documents = $this->service->getAllV2($validated->user_id);
		
		return $this->sendResponse($documents);
	}

	public function getNeedActions(Request $request)
	{
		$dto = new FilterDocumentsDto();

		$dto->order = $request->sort['order'] ?: 'asc';
		$dto->sort = $request->sort['name'] ?: 'id';

		if ($dto->sort === 'type') {
			$dto->sort = 'type_id';
		}

		$dto->limit = $request->count;
		$dto->offset = ($request->page - 1) * $dto->limit;
		$dto->filters = $request->input('filter', []);
		$dto->user_id = $request->input('user_id');

		$documents = $this->service->getNeedActions($dto);
		$documents->items = $this->documentGridService->toGrid($documents->items);

		return $this->sendResponse($documents);
	}

	public function getNeedActionsV2(Request $request){
		$validated = (object) $request->validate(
			[
				'user_id' => 'required|integer',
			],
			[
				'user_id.required' => 'Поле user_id обязательно!',
                'user_id.integer' => 'Поле user_id должно быть числовым!',
			]
		);

		$documents = $this->service->getNeedActionsV2($validated->user_id);
		
		return $this->sendResponse($documents);
	}

	public function getNeedActionSubusers(Request $request)
	{
		$dto = new FilterDocumentsDto();

		$dto->order = $request->sort['order'] ?: 'asc';
		$dto->sort = $request->sort['name'] ?: 'id';

		if ($dto->sort === 'type') {
			$dto->sort = 'type_id';
		}

		$dto->limit = $request->count;
		$dto->offset = ($request->page - 1) * $dto->limit;
		$dto->filters = $request->input('filter', []);
		$dto->user_id = $request->input('user_id');

		$documents = $this->service->getNeedActionSubusers($dto);
		$documents->items = $this->documentGridService->toGrid($documents->items);

		return $this->sendResponse($documents);
	}

	public function getNeedActionSubusersV2(Request $request){
		$validated = (object) $request->validate(
			[
				'user_id' => 'required|integer',
			],
			[
				'user_id.required' => 'Поле user_id обязательно!',
                'user_id.integer' => 'Поле user_id должно быть числовым!',
			]
		);

		$documents = $this->service->getNeedActionSubusersV2($validated->user_id);
		
		return $this->sendResponse($documents);

	}

	public function getNeedActionCount(Request $request)
	{
		$count = $this->service->getNeedActionCount($request->user_id);

		return $this->sendResponse([
			'count' => $count,
		]);
	}

	public function getNeedActionSubuserCount(Request $request)
	{
		$count = $this->service->getNeedActionSubuserCount($request->user_id);

		return $this->sendResponse([
			'count' => $count,
		]);
	}

	public function searchByNumber(Request $request)
	{
		$validated = (object) $request->validate([
			'query' => 'string',
		]);

		$documents = $this->service->searchByNumber($validated->query);

		return $this->sendResponse($documents);
	}

	public function searchByTheme(Request $request)
	{
		$validated = (object) $request->validate([
			'query' => 'string',
		]);

		$documents = $this->service->searchByTheme($validated->query);

		return $this->sendResponse($documents);
	}

	public function getAllStatuses()
	{
		$statuses = $this->service->getAllStatuses();

		return $this->sendResponse($statuses);
	}
}
