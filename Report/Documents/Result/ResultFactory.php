<?php
namespace SED\Report\Documents\Result;

use SED\Report\Components\Link;
use Illuminate\Support\Collection;
use SED\Report\Components\FinedUser;
use SED\Report\Documents\ESZ\ESZFactory;
use SED\Report\Documents\Review\ReviewFactory;
use SED\Report\Interfaces\DocumentFactory;
use SED\Report\Documents\Directive\DirectiveFactory;
use App\Modules\Departments\Facades\DepartmentFacade;

class ResultFactory implements DocumentFactory
{
	private ESZFactory $eszFactory;
	private DirectiveFactory $directiveFactory;
	private ReviewFactory $reviewFactory;
	private ResultCollection $report;

	public function __construct(ESZFactory $eszFactory, DirectiveFactory $directiveFactory, ReviewFactory $reviewFactory)
	{
		$this->eszFactory = $eszFactory;
		$this->directiveFactory = $directiveFactory;
		$this->reviewFactory = $reviewFactory;
	}

	public function getAll(): Collection
	{
		$this->report = new ResultCollection();

		$this->createEszFines();
		$this->createDirectiveFines();
		$this->createReviewFines();

		return $this->report->getItems();
	}

	/**
	 * Добавляет в итоговую таблицу штрафы ЭСЗ
	 */
	private function createEszFines()
	{
		$esz_documents = $this->eszFactory->getAll();

		/**
		 * @var \SED\Report\Documents\ESZ\Esz $esz
		 */
		foreach ($esz_documents as $esz) {
			// Инициатор.Согласование 500 р
			$this->addFine('fine_esz_initiator_on_approval', 500, $esz->fine_initiator_on_approval);

			// Инициатор.Резолюция 50 р
			$this->addFine('fine_esz_initiator_on_resolution', 50, $esz->fine_initiator_on_resolution);

			// Адресат.Резолюция. 450 р
			foreach ($esz->fine_receivers_on_resolution as $fined_receiver) {
				$this->addFine('fine_esz_reciver_on_resolution', 450, $fined_receiver);
			}
		}
	}

	/**
	 * Добавляет в итоговую таблицу штрафы поручений
	 */
	private function createDirectiveFines(): void
	{
		$directive_documents = $this->directiveFactory->getAll();

		/**
		 * @var \SED\Report\Documents\Directive\Directive $directive
		 */
		foreach ($directive_documents as $directive) {
			// Исполнение. Исполнитель 500р.
			foreach ($directive->fine_executor_on_execution as $fined_user) {
				$this->addFine('fine_directive_executor_on_execution', 500, $fined_user);
			}

			// Контроль исполнения. Контроллер 100р.
			foreach ($directive->fine_controller_on_execution_control as $fined_user) {
				$this->addFine('fine_directive_controller_on_control', 100, $fined_user);
			}

			// Запрос переноса срока. Автор 50р.
			$this->addFine('fine_directive_author_on_request_change', 50, $directive->fine_author_on_change);
		}
	}

	/**
	 * Добавляет в итоговую таблицу штрафы ознакомлений
	 */
	private function createReviewFines(): void
	{
		$review_documents = $this->reviewFactory->getAll();

		/**
		 * @var \SED\Report\Documents\Review\Review $review
		 */
		foreach ($review_documents as $review) {
			// Ознакомление. Ознакомляющийся 500р.
			foreach ($review->fine_receivers_on_review as $fine_field) {
				$this->addFine('fine_reviewer_on_review', 500, $fine_field);
			}
		}
	}

	/**
	 * Добавляет штраф в итоговую таблицу
	 * 
	 * @param string $fine_field поле штрафа в классе Result
	 * @param float $fine_amount сумма штрафа
	 * @param FinedUser|null $user ошрафованный пользователь
	 */
	private function addFine(string $fine_field, float $fine_amount, ?FinedUser $user = null): void
	{
		try {
			if (is_null($user)) {
				return;
			}

			$fined_user = $user;
			$department = DepartmentFacade::getByUserId($fined_user->user_id);

			$result = new Result();
			$result->department = new Link($department->title, "https://bitrix.bsi.local/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=$department->id");
			$result->user = new Link($fined_user->full_name, $fined_user->link);
			$result->{$fine_field} = $fine_amount;
			$result->setUserId($fined_user->user_id);

			$this->report->push($result);
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
		}
	}
}