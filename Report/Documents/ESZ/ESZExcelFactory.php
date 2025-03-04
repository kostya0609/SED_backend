<?php
namespace SED\Report\Documents\ESZ;

use Illuminate\Support\Collection;
use SED\Report\Documents\BaseExcelFactory;

class ESZExcelFactory extends BaseExcelFactory
{
	private ESZFactory $factory;

	public function __construct(ESZFactory $factory)
	{
		$this->factory = $factory;
	}

	protected function getDocuments(): Collection
	{
		return $this->factory->getAll();
	}

	protected function getPropertyMapping(): array
	{
		return [
			'number' => 'A',
			'created_at' => 'B',
			'status_title' => 'C',
			'initiator' => 'D',
			'participants' => 'E',
			'signatory' => 'F',
			'receivers' => 'G',
			'fine_initiator_on_approval' => 'H',
			'fine_initiator_on_resolution' => 'I',
			'fine_receivers_on_resolution' => 'J',
		];
	}
}