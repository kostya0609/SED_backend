<?php
namespace SED\Documents\Directive\Listeners;

use App\Modules\Processes\BaseEvents\ProcessEventInterface;
use SED\Documents\Directive\Config\{ExecutionControlProcessConfig, ExecutionProcessConfig};
use App\Modules\Processes\Events\{
	ProcessCreated,
	ProcessRunned,
	ProcessCancelled,
	ProcessDecided,
	ProcessCompleted
};

class ProcessListener
{
	protected $listeners = [];

	public function __construct()
	{
		$this->listeners = [
			ExecutionProcessConfig::getTemplateId() => [
				ProcessCreated::class => [
					\SED\Documents\Directive\ProcessEventListeners\Execution\OnProcessCreated::class,
				],
				ProcessRunned::class => [
					\SED\Documents\Directive\ProcessEventListeners\Execution\OnProcessRunned::class,
				],
				ProcessCancelled::class => [
					\SED\Documents\Directive\ProcessEventListeners\Execution\OnProcessCancelled::class,
				],
				ProcessDecided::class => [
					\SED\Documents\Directive\ProcessEventListeners\Execution\OnProcessDecided::class,
				],
				ProcessCompleted::class => [
					\SED\Documents\Directive\ProcessEventListeners\Execution\OnProcessCompleted::class,
				],
			],

			ExecutionControlProcessConfig::getTemplateId() => [
				ProcessCreated::class => [
					\SED\Documents\Directive\ProcessEventListeners\ExecutionControl\OnProcessCreated::class,
				],
				ProcessRunned::class => [
					\SED\Documents\Directive\ProcessEventListeners\ExecutionControl\OnProcessRunned::class,
				],
				ProcessCancelled::class => [
					\SED\Documents\Directive\ProcessEventListeners\ExecutionControl\OnProcessCancelled::class,
				],
				ProcessDecided::class => [
					\SED\Documents\Directive\ProcessEventListeners\ExecutionControl\OnProcessDecided::class,
				],
				ProcessCompleted::class => [
					\SED\Documents\Directive\ProcessEventListeners\ExecutionControl\OnProcessCompleted::class,
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
					/** TODO: Похоже, что метод call под капотом вызывает указанный метод handle статически */
					\App::call([$listener, 'handle'], ['event' => $event]);
				}
			}
		}
	}
}
