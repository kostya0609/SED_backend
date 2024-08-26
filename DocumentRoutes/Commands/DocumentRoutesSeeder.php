<?php

namespace SED\DocumentRoutes\Commands;

use Illuminate\Console\Command;
use SED\DocumentRoutes\Seeders\Initial\DatabaseSeeder;

class DocumentRoutesSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sed-document-routes:seeding-initial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение начальными данными модуля управления маршрутов документов СЭД';

    public function handle(DatabaseSeeder $seeder)
    {
        $this->info('Заполнение таблиц модуля маршрутов документов начальными данными');
        $seeder->run();
    }
}
