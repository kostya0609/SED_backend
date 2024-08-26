<?php

namespace SED\DocumentRoutes\Commands;

use Illuminate\Console\Command;
use SED\DocumentRoutes\Seeders\Test\DatabaseSeeder;

class DocumentRoutesTestSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sed-document-routes:seeding-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение тестовыми данными модуля управления маршрутами документов СЭД';

    public function handle(DatabaseSeeder $seeder)
    {
        $this->info('Заполнение таблиц модуля маршрутов документов тестовыми данными');
        $seeder->run();
    }
}
