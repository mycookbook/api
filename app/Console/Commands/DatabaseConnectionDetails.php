<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DatabaseConnectionDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads the db connection envs from secrets';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $connection = [
            "DB_HOST" => "ao9moanwus0rjiex.cbetxkdyhwsb.us-east-1.rds.amazonaws.com",
            "DB_PORT" => "3306",
            "DB_DATABASE" => "athiftsxpmxaj82c",
            "DB_USERNAME" => "w7dydvcjsog985xj",
            "DB_PASSWORD" => "iliqkyv8vbbtw603"
        ];

        $filepath = $this->envPath();

        foreach($connection as $key => $value) {
            $fileContents = $this->getFileContents($filepath);

            if (Str::contains($fileContents, $key)) {
                $this->putFileContents(
                    $filepath,
                    preg_replace(
                        "/{$key}=.*/",
                        "{$key}={$value}",
                        $fileContents
                    )
                );
            }
        }

        $this->info("db connection details set successfully.");
    }

    protected function envPath(): string
    {
        if (method_exists($this->laravel, 'environmentFilePath')) {
            return $this->laravel->environmentFilePath();
        }

        return $this->laravel->basePath('.env');
    }

    protected function getFileContents(string $filepath): string
    {
        return file_get_contents($filepath);
    }

    protected function putFileContents(string $filepath, string $data): void
    {
        file_put_contents($filepath, $data);
    }
}
