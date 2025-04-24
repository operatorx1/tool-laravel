<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeViewCustom extends Command
{
    protected $signature = 'make:viewku {name}';
    protected $description = 'Buat File View Custom';

    public function handle()
    {
        $name = str_replace('.', '/', $this->argument('name'));
        $path = resource_path("views/{$name}.blade.php");
        $stubPath = base_path("stubs/viewku.stub");

        if (File::exists($path)) {
            $this->error("View [{$name}] sudah ada!");
            return;
        }

        if (!File::exists($stubPath)) {
            $this->error("File stub tidak ditemukan di: stubs/viewku.stub");
            return;
        }

        $stub = File::get($stubPath);
        $content = str_replace('{{ $viewName }}', $name, $stub);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("View [{$name}] created");
        return Command::SUCCESS;
    }
}
