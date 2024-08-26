<?php
namespace SED\Documents\ESZ\Listeners;

use App\Modules\Processes\BaseEvents\ProcessEventInterface;
use SED\Documents\ESZ\Config\{
	CoordinationProcessConfig,
	SigningProcessConfig,
	ResolutionProcessConfig
};
use App\Modules\Processes\Events\{
	ProcessCreated,
	ProcessRunned,
	ProcessCancelled,
	ProcessDecided,
	ProcessCompleted,
	ParticipantCancelledProcess,
	ExecutorCancelledProcess
};

class ProcessListener
{
	protected $listeners = [];

	public function __construct()
	{
		$this->listeners = [
			CoordinationProcessConfig::getTemplateId() => [
				ProcessCreated::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Coordination\OnProcessCreated::class,
				],
				ProcessRunned::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Coordination\OnProcessRunned::class,
				],
				ParticipantCancelledProcess::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Coordination\OnParticipantCancelled::class,
				],
				ExecutorCancelledProcess::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Coordination\OnExecutorCancelled::class,
				],
				ProcessDecided::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Coordination\OnProcessDecided::class,
				],
				ProcessCompleted::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Coordination\OnProcessCompleted::class,
				],
			],

			SigningProcessConfig::getTemplateId() => [
				ProcessCreated::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Signing\OnProcessCreated::class,
				],
				ProcessRunned::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Signing\OnProcessRunned::class,
				],
				ProcessCancelled::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Signing\OnProcessCancelled::class,
				],
				ProcessDecided::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Signing\OnProcessDecided::class,
				],
				ProcessCompleted::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Signing\OnProcessCompleted::class,
				],
			],

			ResolutionProcessConfig::getTemplateId() => [
				ProcessCreated::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Resolution\OnProcessCreated::class,
				],
				ProcessRunned::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Resolution\OnProcessRunned::class,
				],
				ProcessCancelled::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Resolution\OnProcessCancelled::class,
				],
				ProcessDecided::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Resolution\OnProcessDecided::class,
				],
				ProcessCompleted::class => [
					\SED\Documents\ESZ\ProcessEventListeners\Resolution\OnProcessCompleted::class,
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
