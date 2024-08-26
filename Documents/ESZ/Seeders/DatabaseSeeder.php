<?php
namespace SED\Documents\ESZ\Seeders;

use Illuminate\Support\Facades\App;

abstract class DatabaseSeeder implements SeederInterface
{
	protected $classes = [];

	final public function run(): void
	{
		$this->call($this->classes);
	}

	private function call(array $classes): void
	{
		foreach ($classes as $class) {
			if (!is_a($class, SeederInterface::class, true)) {
				throw new \Exception($class . ' не соответствует интерфейсу ' . SeederInterface::class);
			}

			App::make($class)->run();
		}
	}
}