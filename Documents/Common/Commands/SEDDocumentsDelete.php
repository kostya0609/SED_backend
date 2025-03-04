<?php

namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\Review\Services\ReviewService;

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
	protected $description = 'Удаление документов СЭД для которых были удалены шаблоны документов';

	public function handle(ESZService $eszService, DirectiveService $directiveService, ReviewService $reviewService)
	{
		$bad_esz = $this->getBadEsz();
		$bad_directives = $this->getBadDirectives();
		$bad_reviews = $this->getBadReviews();

		$this->info('Найдены следующие документы для удаления:');

		$this->table(
			['Тип', 'Количество', 'ID'],
			[
				['ЭСЗ', $bad_esz->count(), $bad_esz->implode(', ')],
				['Поручения', $bad_directives->count(), $bad_directives->implode(', ')],
				['Ознакомления', $bad_reviews->count(), $bad_reviews->implode(', ')],
			]
		);

		if ($this->confirm('Вы уверены, что хотите удалить ЭСЗ?')) {
			$bad_esz->each(function (int $id) use ($eszService) {
				$eszService->forceDelete($id);
			});
			$this->info('ЭСЗ удалены.');
		}

		if ($this->confirm('Вы уверены, что хотите удалить поручения?')) {
			$bad_directives->each(function (int $id) use ($directiveService) {
				$directiveService->forceDelete($id);
			});
			$this->info('Поручения удалены.');
		}

		if ($this->confirm('Вы уверены, что хотите удалить ознакомления?')) {
			$bad_reviews->each(function (int $id) use ($reviewService) {
				$reviewService->forceDelete($id);
			});
			$this->info('Ознакомления удалены.');
		}

		$this->info('Операция завершена.');
	}

	public function getBadEsz(): Collection
	{
		return \DB::table('l_esz')
			->whereNotNull('tmp_doc_id')
			->whereNotExists(function ($query) {
				$query->select(\DB::raw(1))
					->from('l_route_tmp_docs')
					->whereRaw('l_route_tmp_docs.id = l_esz.tmp_doc_id');
			})
			->pluck('id');
	}

	public function getBadDirectives(): Collection
	{
		return \DB::table('l_directive')
			->whereNotNull('tmp_doc_id')
			->whereNotExists(function ($query) {
				$query->select(\DB::raw(1))
					->from('l_route_tmp_docs')
					->whereRaw('l_route_tmp_docs.id = l_directive.tmp_doc_id');
			})
			->pluck('id');
	}

	public function getBadReviews(): Collection
	{
		return \DB::table('l_review')
			->whereNotNull('tmp_doc_id')
			->whereNotExists(function ($query) {
				$query->select(\DB::raw(1))
					->from('l_route_tmp_docs')
					->whereRaw('l_route_tmp_docs.id = l_review.tmp_doc_id');
			})
			->pluck('id');
	}
}
