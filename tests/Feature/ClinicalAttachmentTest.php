<?php

namespace Tests\Feature;

use App\Models\HistoriaClinica;
use App\Models\Patient;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ClinicalAttachmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('database.connections.mysql', Config::get('database.connections.sqlite'));
        Config::set('database.connections.tenant', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Artisan::call('migrate');
        Artisan::call('migrate', ['--database' => 'tenant']);
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => database_path('migrations/tenant'),
            '--realpath' => true,
        ]);
    }

    /** @test */
    public function it_uploads_an_image_attachment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $historia = HistoriaClinica::create([
            'paciente_id' => $patient->id,
            'estado' => 'borrador',
        ]);

        $fakeResponse = new class
        {
            public function getResult(): array
            {
                return [
                    'public_id' => 'test/public-id',
                    'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v1/test.webp',
                    'resource_type' => 'image',
                    'format' => 'webp',
                    'width' => 800,
                    'height' => 600,
                    'bytes' => 1024,
                ];
            }
        };

        Cloudinary::shouldReceive('uploadFile')
            ->once()
            ->andReturn($fakeResponse);

        $file = UploadedFile::fake()->image('rayos-x.jpg')->size(1024);

        $response = $this->post(route('historias-clinicas.adjuntos.store', $historia), [
            'titulo' => 'Rayos X',
            'files' => [$file],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('clinical_attachments', [
            'historia_id' => $historia->id,
            'paciente_id' => $patient->id,
            'file_type' => 'image',
        ], 'tenant');
    }

    /** @test */
    public function it_rejects_invalid_files_and_titles(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $historia = HistoriaClinica::create([
            'paciente_id' => $patient->id,
            'estado' => 'borrador',
        ]);

        $badFile = UploadedFile::fake()->create('script.exe', 200, 'application/octet-stream');

        $response = $this->from(route('historias-clinicas.show', $historia))
            ->post(route('historias-clinicas.adjuntos.store', $historia), [
                'titulo' => '@@@',
                'files' => [$badFile],
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
