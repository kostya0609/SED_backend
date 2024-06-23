<?php
namespace SED\Documents\Review\Listeners;

use App\Modules\Processes\BaseEvents\ProcessEventInterface;
use SED\Documents\Review\Config\DecideProcessConfig;
use App\Modules\Processes\Events\{
	ProcessCreated,
	ProcessRunned,
	ProcessCancelled,
	ProcessDecided,
	ProcessCompleted
};
use SED\Documents\Review\ProcessEventListeners\{
	OnProcessCreated,
	OnProcessRunned,
	OnProcessCancelled,
	OnProcessDecided,
	OnProcessCompleted
};

class ProcessListener
{
	protected $listeners = [];

	public function __construct()
	{
		$this->listeners = [
			DecideProcessConfig::getProcessTemplateId() => [
					ProcessCreated::class => [
					OnProcessCreated::class,
				],
				ProcessRunned::class => [
					OnProcessRunned::class,
				],
				ProcessCancelled::class => [
					OnProcessCancelled::class,
				],
				ProcessDecided::class => [
					OnProcessDecided::class,
				],
				ProcessCompleted::class => [
					OnProcessCompleted::class,
				],

			],
			
		];
	}

	public function handle(ProcessEventInterface $event)
	{
		$process = $event->getProcess();

		foreach ($this->listeners as $template_id => $events) {
			if ($template_id !== $process->template_id) {
				continue;
			}

			foreach ($events as $event_cls => $listeners) {
				if (get_class($event) !== $event_cls) {
					continue;
				}

				foreach ($listeners as $listener) {
					\App::call([$listener, 'handle'], ['event' => $event]);
				}
			}
		}
	}
}
