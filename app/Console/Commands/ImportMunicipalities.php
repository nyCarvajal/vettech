<?php

namespace App\Console\Commands;

use App\Models\GeoDepartment;
use App\Models\GeoMunicipality;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ImportMunicipalities extends Command
{
    protected $signature = 'geo:import-municipalities {file}';
    protected $description = 'Import municipalities from CSV or JSON file';

    public function handle(): int
    {
        $path = $this->argument('file');
        if (! File::exists($path)) {
            $this->error('File not found');
            return self::FAILURE;
        }

        $municipalities = $this->readFile($path);
        foreach ($municipalities as $row) {
            if (! isset($row['department'], $row['name'])) {
                continue;
            }
            $department = GeoDepartment::firstOrCreate(['nombre' => $row['department']]);
            GeoMunicipality::updateOrCreate(
                ['departamento_id' => $department->id, 'nombre' => $row['name']],
                ['codigo' => $row['code'] ?? null]
            );
        }

        $this->info('Import completed');
        return self::SUCCESS;
    }

    protected function readFile(string $path): Collection
    {
        if (str_ends_with($path, '.json')) {
            return collect(json_decode(File::get($path), true));
        }

        $rows = collect();
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return $rows;
        }
        $header = null;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (! $header) {
                $header = $data;
                continue;
            }
            $rows->push(array_combine($header, $data));
        }
        fclose($handle);

        return $rows;
    }
}
