<?php
namespace SED\Documents\Directive;

use App\Modules\Processes\Events\AddedActiveParticipant;
use SED\Documents\Directive\Listeners\AddActiveParticipantListener;
use SED\Documents\Directive\Listeners\ProcessListener;
use App\Modules\Processes\BaseEvents\ProcessEventInterface;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class DirectiveEventServiceProvider extends EventServiceProvider
{
	protected $listen = [
		ProcessEventInterface::class => [
			ProcessListener::class,
		],
		AddedActiveParticipant::class => [
			AddActiveParticipantListener::class,
		],
	];
}