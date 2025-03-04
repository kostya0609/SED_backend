<?php
namespace SED\Report\Documents\Directive;

use SED\Report\Components\Link;
use SED\Report\Interfaces\Document;
use SED\Report\Components\FinedUser;

class Directive implements Document
{
	public Link $number;
	public string $created_at;
	public string $status_title;
	public string $deadline;
	public string $creator;
	public string $author;
	public array $executor;
	public array $controller = [];

	/**
	 * @var array<FinedUser>
	 */
	public ?array $fine_executor_on_execution = [];

	/**
	 * 
	 * @var array<FinedUser>
	 */
	public ?array $fine_controller_on_execution_control = [];
	public ?FinedUser $fine_author_on_change = null;

	public function __construct(\SED\Documents\Directive\Models\Directive $document)
	{
		$this->number = new Link($document->number, "https://bitrix.bsi.local/sed/documents/directive/detail/$document->id");
		$this->created_at = $document->created_at->format('Y-m-d H:i:s');
		$this->status_title = $document->status->title;
		$this->deadline = $document->executed_at->format('Y-m-d');
		$this->creator = $document->creator->user->full_name;
		$this->author = $document->author->user->full_name;
		$this->executor = $document->executors->map(fn($executor) => $executor->user->full_name)->toArray();
		$this->controller = $document->controllers->map(fn($controller) => $controller->user->full_name)->toArray();

		if ($document->isExecutionInWork()) {
			$this->fine_executor_on_execution = $document->executors->map(fn($executor) => new FinedUser($executor->user))->toArray();
		}

		if ($document->isExecutionControl()) {
			$this->fine_controller_on_execution_control = $document->controllers->map(fn($controller) => new FinedUser($controller->user))->toArray();
		}

		if ($document->isExecutionChangeRequest()) {
			$this->fine_author_on_change = new FinedUser($document->author->user);
		}
	}
}
