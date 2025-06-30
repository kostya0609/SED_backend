<?php
namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;
use SED\Facades\SEDDirectiveFacade;
use SED\Facades\SEDDocumentBuilderFacade;
use SED\Facades\SEDESZFacade;
use SED\Facades\SEDReviewFacade;

class SEDDocumentsTransferHierarchy extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-documents:test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Тестирование иерархии документов';

	public function handle()
	{
		\DB::transaction(function () {

			/** Получаем ознакомление */
			$review = SEDReviewFacade::findById(1);

			/** Если ознакомление на статусе Черновик, то получаем участников ознакомления для дальнейшей обработки */
			if ($review->isDraft()) {
				$receivers = SEDReviewFacade::getReceivers($review->id);
			}

			$directive = SEDDocumentBuilderFacade::directive()
				->createByTemplate(85)
				->setCreatorId(14317)
				->setAuthor(SEDDocumentBuilderFacade::createUser(14317, false))
				->addController(SEDDocumentBuilderFacade::createDynamicRole(2, false))
				->addObserver(SEDDocumentBuilderFacade::createStaticRole(35, false))
				->setContent('Тестовый документ')
				->setPortfolio('Тестовый портфолио')
				->save();

			$esz = SEDDocumentBuilderFacade::esz()
				->createByTemplate(90, $directive->document_hierarchy_id)
				->setInitiatorId(14317)
				->addReceiver(SEDDocumentBuilderFacade::createUser(14317, false))
				->save();

			$review = SEDDocumentBuilderFacade::review()
				->createEmpty()
				->setParentDocumentId($esz->document_hierarchy_id)
				->setThemeTitle('Тестовый тема')
				->setInitiatorId(14317)
				->setContent('Тестовый отзыв')
				->setPortfolio('Тестовый портфолио')
				->addReceiver(SEDDocumentBuilderFacade::createUser(14317, false))
				->save();

			$this->info("Поручение: {$directive->number}, ЭСЗ: {$esz->number}");
		});
	}
}
