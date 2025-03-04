<?php
namespace SED\Report\Documents\Directive;

use SED\Report\Documents\BaseExcelFactory;

class DirectiveExcelFactory extends BaseExcelFactory
{
	private DirectiveFactory $factory;

	public function __construct(DirectiveFactory $factory)
	{
		$this->factory = $factory;
	}

	protected function getDocuments(): \Illuminate\Support\Collection
	{
		return $this->factory->getAll();
	}

	protected function getPropertyMapping(): array
	{
		return [
			'number' => 'A',
			'created_at' => 'B',
			'status_title' => 'C',
			'deadline' => 'D',
			'creator' => 'E',
			'author' => 'F',
			'executor' => 'G',
			'controller' => 'H',
			'fine_executor_on_execution' => 'I',
			'fine_controller_on_execution_control' => 'J',
			'fine_author_on_change' => 'K',
		];
	}
}