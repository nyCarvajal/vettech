<?php

namespace Tests\Feature;

use App\Models\Clinica;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicSettingsTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        DB::connection('mysql')->getPdo();

        Artisan::call('migrate', [
            '--database' => 'mysql',
            '--path' => 'database/migrations/tenant',
        ]);
    }

    public function test_actualiza_datos_basicos_de_clinica(): void
    {
        $clinica = Clinica::create(['nombre' => 'Clinica Inicial']);
        $user = User::make(['role' => 'admin', 'clinica_id' => $clinica->id]);

        $response = $this->actingAs($user)->put(route('settings.clinica.update'), [
            'name' => 'Clinica Central',
            'nit' => '900123456',
            'phone' => '3001234567',
            'payment_terms' => 'Pago contra entrega.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clinicas', [
            'id' => $clinica->id,
            'name' => 'Clinica Central',
            'nombre' => 'Clinica Central',
            'nit' => '900123456',
        ]);
    }

    public function test_subir_logo_guarda_ruta_y_archivo(): void
    {
        Storage::fake('public');

        $clinica = Clinica::create(['nombre' => 'Clinica Logo']);
        $user = User::make(['role' => 'admin', 'clinica_id' => $clinica->id]);

        $response = $this->actingAs($user)->post(route('settings.clinica.logo.store'), [
            'logo' => UploadedFile::fake()->image('logo.png', 600, 200),
        ]);

        $response->assertRedirect();

        $clinica->refresh();

        $this->assertNotNull($clinica->logo_path);
        Storage::disk('public')->assertExists($clinica->logo_path);
    }

    public function test_imprimible_muestra_nombre_y_logo_de_clinica(): void
    {
        $clinica = Clinica::create([
            'nombre' => 'Clinica Print',
            'name' => 'Clinica Print',
            'logo_path' => 'clinicas/1/logo.png',
        ]);

        $user = User::make(['role' => 'admin', 'clinica_id' => $clinica->id]);

        $invoice = new Invoice([
            'full_number' => 'POS-1',
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'commission_total' => 0,
            'total' => 0,
            'change_total' => 0,
            'issued_at' => now(),
        ]);

        $invoice->setRelation('lines', collect([
            (object) ['description' => 'Consulta', 'quantity' => 1, 'line_total' => 0],
        ]));
        $invoice->setRelation('payments', collect());
        $invoice->setRelation('owner', (object) ['name' => 'Cliente Test']);

        $this->actingAs($user);

        $html = view('invoices.print', ['invoice' => $invoice])->render();

        $this->assertStringContainsString('Clinica Print', $html);
        $this->assertStringContainsString('storage/clinicas/1/logo.png', $html);
    }
}
