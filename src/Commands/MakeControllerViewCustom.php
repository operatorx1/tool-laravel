<?php

namespace ToolLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeControllerViewCustom extends Command
{
    protected $signature    = 'make:controllerviewku {name}';
    protected $description  = 'Buat File Controller dan View Custom dengan stub';

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
        $subNamespace   = trim(str_replace('/', '\\', dirname($argumentName)), '\\');
        $view_path      = str_replace(['/', '_controller'], ['.', ''], $this->camelToSnake($argumentName)) . ".";
        $sview_path     = "'{$view_path}'";
        
        $parts          = array_values(array_filter(explode('.', $view_path )));        
        $view_name      = $parts[array_key_last($parts)];
        $form           = "'{$view_name}'";
    
        $namespace = 'App\Http\Controllers' . ($subNamespace ? '\\' . $subNamespace : '');
    
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ view_path }}', '{{ form }}'],
            [$namespace, $className, $sview_path, $form],
            $stub
        );
    
        File::ensureDirectoryExists(dirname($controllerPath));
        File::put($controllerPath, $content);
    
        $this->info("Controller [{$argumentName}] berhasil dibuat 🎉");


        /* VIEW */        
        $name = str_replace(['.', '_controller'], ['/', ''], $this->camelToSnake($argumentName));
        $path = resource_path("views/{$name}/{$view_name}_index.blade.php");

        $customStubPath = base_path("stubs/viewku.stub");
        $defaultStubPath = __DIR__ . '/../../stubs/viewku.stub';
         // Gunakan custom stub jika tersedia, fallback ke bawaan package
        $stubPath = File::exists($customStubPath) ? $customStubPath : $defaultStubPath;

        if (File::exists($path)) {
            $this->error("View [{$name}] sudah ada!");
            return;
        }

        if (!File::exists($stubPath)) {
            $this->error("File stub tidak ditemukan di: {{$stubPath}}");
            return;
        }

        $stub = File::get($stubPath);
        $content = str_replace('{{ $viewName }}', $name, $stub);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("View [{$name}] created");

        return Command::SUCCESS;
    }

    public function camelToSnake($input){
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
    
}