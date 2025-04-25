<?php

namespace ToolLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeControllerCustom extends Command
{
    protected $signature = 'make:controllerku {name}';
    protected $description = 'Buat File Controller Custom dengan stub';

    public function handle()
    {
        $name = str_replace('/', '\\', $this->argument('name'));
        $controllerPath = app_path("Http/Controllers/{$name}.php");

        $customStubPath = base_path("stubs/controllerku.stub");
        $defaultStubPath = __DIR__ . '/../../stubs/controllerku.stub';
        $stubPath = File::exists($customStubPath) ? $customStubPath : $defaultStubPath;

        if (!File::exists($stubPath)) {
            $this->error("File stub tidak ditemukan di: {$stubPath}");
            return Command::FAILURE;
        }

        if (File::exists($controllerPath)) {
            $this->error("Controller [{$name}] sudah ada!");
            return Command::FAILURE;
        }

        $stub = File::get($stubPath);

        // Ambil class name dan namespace dari input
        $className = class_basename($name);
        $namespace = "App\Http\Controllers" . (str_contains($name, '\\') ? '\\' . dirname(str_replace('\\', '/', $name)) : '');

        $namespace = str_replace('/', '\\', trim($namespace, '\\'));

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );

        File::ensureDirectoryExists(dirname($controllerPath));
        File::put($controllerPath, $content);

        $this->info("Controller [{$name}] berhasil dibuat 🎉");
        return Command::SUCCESS;
    }
}
