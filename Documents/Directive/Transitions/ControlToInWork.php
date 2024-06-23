<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;
use App\Modules\Processes\Facades\ProcessFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use SED\Documents\Directive\Transitions\BaseTransition;
use SED\Documents\Directive\Config\ExecutionControlProcessConfig;

class ControlToInWork extends BaseTransition
{
	public function handle(Directive $directive): Directive
	{
		return \DB::transaction(function () use ($directive): Directive {
			$directive->process_template_id = ExecutionControlProcessConfig::getTemplateId();
			$directive->status_id = Status::EXECUTION_IN_WORK;
			$directive->save();

			return parent::execute($directive);
		});
	}
}