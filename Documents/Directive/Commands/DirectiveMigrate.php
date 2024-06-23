<?php
namespace SED\Documents\Directive\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DirectiveMigrate extends Command
{
	protected $signature = 'sed-directive:migrate
                            {--reset : Переустановить таблицы}
                            {--remove : Удалить таблицы включая память миграций}
                            {--back= : Откатить миграцию на n шагов}';

	protected $description = 'Миграции модуля поручения СЭД';

	public function handle()
	{
		$table_name = $this->getTableName();
		$path = $this->getMigrationsPath();

		if ($this->option('remove')) {
			$this->remove($table_name, $path);
		} elseif ($this->option('reset')) {
			$this->reset($table_name, $path);
		} elseif ($this->option('back') > 0) {
			$back = (int) $this->option('back');
			$this->back($table_name, $path, $back);
		} else {
			$this->migrate($table_name, $path);
		}
	}

	protected function getTableName(): string
	{
		return 'l_directive_migration';
	}

	protected function getMigrationsPath(): string
	{
		return '/Modules/SED/Documents/Directive/Migrations';
	}

	protected function remove(string $table_name, string $path)
	{
		if (Schema::hasTable($table_name)) {

			$collection = DB::table($table_name)->select('id', 'migration')->orderBy('id', 'desc')->get();
			foreach ($collection as $el) {
				$classes = get_declared_classes();
				include app_path() . $path . '/' . $el->migration;
				$diff = array_diff(get_declared_classes(), $classes);
				foreach ($diff as $class) {
					if (false === strripos($class, '\\')) {
						$obj = new $class();
						$obj->down();
					}
				}
				$this->info($el->migration);
			}

			Schema::dropIfExists($table_name);

			$this->info('Remove migration completed!');

			return 0;
		} else {
			$this->info('No remove migrations found');
			return 0;
		}
	}

	protected function reset(string $table_name, string $path)
	{
		$fileClasses = [];
		if (Schema::hasTable($table_name)) {

			$collection = DB::table($table_name)->select('id', 'migration')->orderBy('id', 'desc')->get();

			foreach ($collection as $el) {
				$classes = get_declared_classes();
				include app_path() . $path . '/' . $el->migration;
				$diff = array_diff(get_declared_classes(), $classes);
				foreach ($diff as $class) {
					if (false === strripos($class, '\\')) {
						$fileClasses[$el->migration] = $class;
						$obj = new $class();
						$obj->down();
					}
				}
				$this->info($el->migration);
			}

			Schema::dropIfExists($table_name);

			$this->info('Remove migration completed!');
		} else {
			$this->info('No remove migrations found');
		}

		Schema::create($table_name, function (Blueprint $table) {
			$table->id();
			$table->string('migration');
			$table->integer('batch')->unsigned();
		});

		$batch = 1;

		$files = array_diff(scandir(app_path() . $path), ['.', '..']);
		$migrationFile = [];
		foreach ($files as $file) {

			$info = new \SplFileInfo(app_path() . $path . '/' . $file);
			if ($info->getExtension() == 'php') {
				if (!empty($fileClasses) && $fileClasses[$file]) {
					$obj = new $fileClasses[$file]();
					$obj->up();
					DB::table($table_name)->insert(['migration' => $file, 'batch' => $batch]);
					$this->info($file);
				} else {
					$migrationFile[] = $file;
					$classes = get_declared_classes();
					include app_path() . $path . '/' . $file;
					$diff = array_diff(get_declared_classes(), $classes);
					foreach ($diff as $class) {
						if (false === strripos($class, '\\')) {
							$obj = new $class();
							$obj->up();
							DB::table($table_name)->insert(['migration' => $file, 'batch' => $batch]);
							$this->info($file);
						}
					}
				}
			}
		}
		$this->info("Reset migration completed!");

		return 0;
	}

	protected function back(string $table_name, string $path, int $back)
	{
		while ($back > 0) {

			$tableMigration = DB::table($table_name);
			$batch = $tableMigration->max('batch');
			$collection = DB::table($table_name)
				->select('id', 'migration')
				->where('batch', $batch)
				->orderBy('id', 'desc')
				->get();

			foreach ($collection as $el) {
				$classes = get_declared_classes();
				include app_path() . $path . '/' . $el->migration;
				$diff = array_diff(get_declared_classes(), $classes);
				foreach ($diff as $class) {
					if (false === strripos($class, '\\')) {
						$fileClasses[$el->migration] = $class;
						$obj = new $class();
						$obj->down();
					}
				}
				DB::table($table_name)->delete($el->id);
				$this->info($el->migration . ' back');
			}
			$back--;
		}
	}

	protected function migrate(string $table_name, string $path)
	{
		if (!Schema::hasTable($table_name)) {
			Schema::create($table_name, function (Blueprint $table) {
				$table->id();
				$table->string('migration');
				$table->integer('batch')->unsigned();
			});
		}
		$batch = 1;
		$collection = DB::table($table_name)->get();
		$arrFilesData = ['.', '..'];
		foreach ($collection as $el) {
			if ($el->batch >= $batch)
				$batch = $el->batch + 1;
			$arrFilesData[] = $el->migration;
		}


		$files = array_diff(scandir(app_path() . $path), $arrFilesData);
		$migrationFile = [];
		foreach ($files as $file) {

			$info = new \SplFileInfo(app_path() . $path . '/' . $file);
			if ($info->getExtension() == 'php') {
				$migrationFile[] = $file;
				$classes = get_declared_classes();
				include app_path() . $path . '/' . $file;
				$diff = array_diff(get_declared_classes(), $classes);
				foreach ($diff as $class) {

					if (false === strripos($class, '\\')) {
						try {
							$obj = new $class();
							$obj->up();
							DB::table($table_name)->insert(['migration' => $file, 'batch' => $batch]);
							$this->info($file);
						} catch (\Exception $e) {
							$obj->down();
							throw $e;
						}
					}
				}

			}
		}
		if (empty($migrationFile))
			$this->info('No migrations found');
		else
			$this->info("Migration completed!");
		return 0;
	}
}
