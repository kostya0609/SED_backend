<?php

namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;
use SED\Documents\Common\Seeders\Test\DatabaseSeeder;

class SEDDocumentsTestSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sed-documents:seeding-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение тестовыми данными модуля управления документами СЭД';

    public function handle(DatabaseSeeder $seeder)
    {
        $this->info('Заполнение таблиц модуля тестовыми данными');
        $seeder->run();
    }
}
