<?php
namespace SED\Common\Services;

use Dotenv\Dotenv;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{

	/**
	 * Загружает переменные окружения по пути $path
	 */
	protected function loadEnvironmentsFrom(string $path): void
	{
		$env_file_name = '.env' . (!empty(\App::environment()) ? '.' . \App::environment() : '');
		$dotenv = Dotenv::createImmutable($path, [
			$env_file_name,
		]);

		$configs = $dotenv->load();

		foreach ($configs as $key => $value) {
			\Config::set($key, $value);
		}
	}
}