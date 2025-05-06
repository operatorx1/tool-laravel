<?php

namespace ToolLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeControllerCustom extends Command
{
    protected $signature    = 'make:controllerku {name}';
    protected $description  = 'Buat File Controller Custom dengan stub';

    public function handle()
    {
        $argumentName = $this->argument('name'); // tetap pakai slash sebagai pemisah folder
    
        $controllerPath = app_path('Http/Controllers/' . $argumentName . '.php');
    
        $customStubPath = base_path("stubs/controllerku.stub");
        $defaultStubPath = __DIR__ . '/../../stubs/controllerku.stub';
        $stubPath = File::exists($customStubPath) ? $customStubPath : $defaultStubPath;
    
        if (!File::exists($stubPath)) {
            $this->error("File stub tidak ditemukan di: {$stubPath}");
            return Command::FAILURE;
        }
    
        if (File::exists($controllerPath)) {
            $this->error("Controller [{$argumentName}] sudah ada!");
            return Command::FAILURE;
        }
    
        $stub = File::get($stubPath);
    
        $className = class_basename($argumentName);
        $subNamespace = trim(str_replace('/', '\\', dirname($argumentName)), '\\');
    
        $namespace = 'App\Http\Controllers' . ($subNamespace ? '\\' . $subNamespace : '');
    
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );
    
        File::ensureDirectoryExists(dirname($controllerPath));
        File::put($controllerPath, $content);
    
        $this->info("Controller [{$argumentName}] berhasil dibuat 🎉");
        return Command::SUCCESS;
    }
    
}
