<?php

namespace Tests\Feature;

use App\Models\HistoriaClinica;
use App\Models\Patient;
use App\Models\ClinicalAttachment;
use App\Models\User;
use App\Services\CloudinaryAttachmentService;
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

        $mockService = $this->createMock(CloudinaryAttachmentService::class);
        $mockService->method('buildFolderPath')->willReturn('tenants/demo');
        $mockService->method('upload')->willReturn([
            'public_id' => 'test/public-id',
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v1/test.webp',
            'resource_type' => 'image',
            'format' => 'webp',
            'width' => 800,
            'height' => 600,
            'bytes' => 1024,
        ]);
        $this->app->instance(CloudinaryAttachmentService::class, $mockService);

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
    public function it_uploads_a_document_attachment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $historia = HistoriaClinica::create([
            'paciente_id' => $patient->id,
            'estado' => 'borrador',
        ]);

        $mockService = $this->createMock(CloudinaryAttachmentService::class);
        $mockService->method('buildFolderPath')->willReturn('tenants/demo');
        $mockService->method('upload')->willReturn([
            'public_id' => 'test/doc-id',
            'secure_url' => 'https://res.cloudinary.com/demo/raw/upload/v1/test.docx',
            'resource_type' => 'raw',
            'format' => 'docx',
            'bytes' => 2048,
        ]);
        $this->app->instance(CloudinaryAttachmentService::class, $mockService);

        $file = UploadedFile::fake()->create(
            'resultado.docx',
            200,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );

        $response = $this->post(route('historias-clinicas.adjuntos.store', $historia), [
            'titulo' => 'Resultados',
            'files' => [$file],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('clinical_attachments', [
            'historia_id' => $historia->id,
            'paciente_id' => $patient->id,
            'file_type' => 'document',
        ], 'tenant');
    }

    /** @test */
    public function it_lists_and_deletes_attachments(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $patient = Patient::factory()->create();
        $historia = HistoriaClinica::create([
            'paciente_id' => $patient->id,
            'estado' => 'borrador',
        ]);

        $attachment = ClinicalAttachment::create([
            'historia_id' => $historia->id,
            'paciente_id' => $patient->id,
            'titulo' => 'Examen',
            'titulo_limpio' => 'examen',
            'file_type' => 'document',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1234,
            'cloudinary_public_id' => 'test/delete',
            'cloudinary_secure_url' => 'https://example.com/test.pdf',
            'cloudinary_resource_type' => 'raw',
            'created_by' => $user->id,
        ]);

        $this->get(route('historias-clinicas.adjuntos.index', $historia))
            ->assertOk()
            ->assertJsonFragment(['id' => $attachment->id]);

        $mockService = $this->createMock(CloudinaryAttachmentService::class);
        $mockService->method('delete');
        $this->app->instance(CloudinaryAttachmentService::class, $mockService);

        $response = $this->delete(route('historias-clinicas.adjuntos.destroy', $attachment));
        $response->assertRedirect();

        $this->assertDatabaseMissing('clinical_attachments', [
            'id' => $attachment->id,
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
