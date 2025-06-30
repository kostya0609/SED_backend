<?php
namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Models\Document;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\Review\Services\ReviewService;

/**
 * 
 * TODO: Доработать и исправит ошибки в комманде удаления документов с иерархией
 */
class SEDDocumentsDelete extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed:documents:delete';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Удаление документов СЭД';

	private const FILE_DELETE_DOCUMENTS = __DIR__ . '/delete-documents.txt';

	public function handle(ESZService $eszService, DirectiveService $directiveService, ReviewService $reviewService)
	{
		$numbers = $this->getNumbers();

		$documents = Document::query()
			->whereIn('number', $numbers)
			->get();

		if ($documents->isEmpty()) {
			$this->info("\nНе найдено документов по указанным номерам.");
			return;
		}

		$this->info("\nКол-во: {$documents->count()}");

		$this->table(
			['ID', 'Номер', 'Тип', 'Статус', 'Дата создания'],
			$documents->map(function ($document) {
				return [
					'id' => $document->id,
					'number' => $document->number,
					'type' => $document->type->title,
					'status' => $document->status_title,
					'created_at' => $document->created_at->format('Y-m-d H:i:s'),
				];
			})
		);

		if ($this->confirm('Вы уверены, что хотите удалить эти документы?', false)) {
			$eszs = $documents->filter(fn(Document $document) => $document->type_id === DocumentType::ESZ);
			$directives = $documents->filter(fn(Document $document) => $document->type_id === DocumentType::DIRECTIVE);
			$reviews = $documents->filter(fn(Document $document) => $document->type_id === DocumentType::REVIEW);

			if ($eszs->count() > 0) {
				foreach ($eszs as $esz) {
					$eszService->forceDelete($esz->document_id);
				}

				$this->info("\nУдалено ЭСЗ: {$eszs->count()}");
			} else {
				$this->info("\nНет ЭСЗ для удаления.");
			}

			if ($directives->count() > 0) {
				foreach ($directives as $directive) {
					$directiveService->forceDelete($directive->document_id);
				}

				$this->info("\nУдалено поручений: {$directives->count()}");
			} else {
				$this->info("\nНет поручений для удаления.");
			}


			if ($reviews->count() > 0) {
				foreach ($reviews as $review) {
					$reviewService->forceDelete($review->document_id);
				}

				$this->info("\nУдалено ознакомлений: {$reviews->count()}");
			} else {
				$this->info("\nНет ознакомлений для удаления.");
			}
		} else {
			$this->info('Операция отменена.');
			return;
		}
	}

	private function getNumbers(): array
	{
		if (!file_exists(self::FILE_DELETE_DOCUMENTS)) {
			$this->error('Файл delete-documents.txt не найден в директории команды.');
			$this->info('Создайте файл "delete-documents.txt" со списком номеров документов для удаления (по одному номеру на строку).');
			exit(1);
		}

		$numbers = file_get_contents(self::FILE_DELETE_DOCUMENTS);

		return array_map('trim', array_filter(
			explode("\n", $numbers),
			'trim'
		));
	}
}
