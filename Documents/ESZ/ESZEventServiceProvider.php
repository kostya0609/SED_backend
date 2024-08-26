<?php
namespace SED\Documents\ESZ;

use App\Modules\Processes\Events\AddedActiveParticipant;
use SED\Documents\ESZ\Listeners\AddActiveParticipantListener;
use SED\Documents\ESZ\Listeners\ProcessListener;
use App\Modules\Processes\BaseEvents\ProcessEventInterface;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class ESZEventServiceProvider extends EventServiceProvider
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