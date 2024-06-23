<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;
use App\Modules\Processes\Facades\ProcessFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use SED\Documents\Directive\Transitions\BaseTransition;
use SED\Documents\Directive\Config\ExecutionControlProcessConfig;

class InWorkToControl extends BaseTransition
{
	public function handle(Directive $directive): Directive
	{
		return \DB::transaction(function () use ($directive): Directive {
			ProcessFacade::deactivateCompletedProcess($directive->process_template_id, $directive->id);

			ProcessFacade::create(
				CreateProcessDto::create(
					$directive->author->user_id,
					$directive->id,
					ExecutionControlProcessConfig::getTemplateId(),
					$directive->creator->user_id,
				)
			);

			$directive->process_template_id = ExecutionControlProcessConfig::getTemplateId();
			$directive->status_id = Status::EXECUTION_CONTROL;
			$directive->save();

			return parent::execute($directive);
		});
	}
}