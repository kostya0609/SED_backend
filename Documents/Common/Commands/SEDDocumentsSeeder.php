<?php

namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;
use SED\Documents\Common\Seeders\Initial\DatabaseSeeder;

class SEDDocumentsSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sed-documents:seeding-initial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение начальными данными модуля управления документами СЭД';

    public function handle(DatabaseSeeder $seeder)
    {
        $this->info('Заполнение таблиц модуля начальными данными');
        $seeder->run();
    }
}
