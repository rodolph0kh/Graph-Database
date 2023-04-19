<?php

namespace App\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class CreateRelationshipMigrationAction
{
    public function __construct(protected string $name)
    {  
    }

    public function run()
    {
        $tableName = $this->name;
        $columns = ['directed', 'source', 'destination', 'properties'];
        
        // i called another command to create migration class
        Artisan::call('make:migration', [
            'name' => 'create_' . $tableName . '_table',
            '--create' => $tableName,
            '--table' => $tableName,
        ]);
    
        // Get the latest migration file path
        $path = database_path('migrations/' . collect(scandir(database_path('migrations')))
            ->last(function ($file) {
                return Str::endsWith($file, '.php');
            }));
    
        // Add the columns to the migration file
        $content = file_get_contents($path);
        $content = str_replace('$table->id();', '$table->id();' . PHP_EOL . $this->generateColumns($columns), $content);
        file_put_contents($path, $content);

        Artisan::call('migrate');
    }

    /**
     * Generate the columns string for the migration file.
     *
     * @param  array  $columns
     * @return string
     */
    private function generateColumns(array $columns): string
    {
        $columnsString = '';
        foreach ($columns as $column) {
            if ($column === 'directed') {
                $columnsString .= '$table->boolean(\'directed\')->default(1);' . PHP_EOL . "\t\t\t";
            } elseif ($column === 'source') {
                $columnsString .= '$table->unsignedBigInteger(\'source_id\');' . PHP_EOL . "\t\t\t";
                $columnsString .= '$table->string(\'source_type\');' . PHP_EOL . "\t\t\t";
            } elseif ($column === 'destination') {                
                $columnsString .= '$table->unsignedBigInteger(\'destination_id\');' . PHP_EOL . "\t\t\t";
                $columnsString .= '$table->string(\'destination_type\');' . PHP_EOL . "\t\t\t";
            } else {
                $columnsString .= '$table->string(\'' . $column . '\');' . PHP_EOL . "\t\t\t";
            }
        }
        return $columnsString;
    }
}