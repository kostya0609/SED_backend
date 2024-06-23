<?php
namespace SED\Documents\Directive\Commands;

use Illuminate\Console\Command;
use SED\Documents\Directive\Seeders\Initial\DatabaseSeeder;

class DirectiveInitialSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sed-directive:seeding-initial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение начальными данными модуля';

    public function handle(DatabaseSeeder $seeder)
    {
        $this->info('Заполнение таблиц модуля начальными данными');
        $seeder->run();
    }
}
