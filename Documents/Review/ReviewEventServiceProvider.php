<?php
namespace SED\Documents\Review;

use SED\Documents\Review\Listeners\ProcessListener;
use SED\Documents\Review\Listeners\AddActiveParticipantListener;
use App\Modules\Processes\BaseEvents\ProcessEventInterface;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use App\Modules\Processes\Events\AddedActiveParticipant;

class ReviewEventServiceProvider extends EventServiceProvider
{
	protected $listen = [
		ProcessEventInterface::class => [
			ProcessListener::class,
		],
		
		AddedActiveParticipant::class => [
			AddActiveParticipantListener::class,
		]
	];
}