<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\CreateRelationshipMigrationAction;

class RelationshipTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relationship:table {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new relationships table with different type';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        (new CreateRelationshipMigrationAction($this->argument('name')))->run();
    }
}
