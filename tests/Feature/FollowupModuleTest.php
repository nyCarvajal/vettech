<?php

namespace Tests\Feature;

use App\Models\Followup;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FollowupModuleTest extends TestCase
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
    public function it_creates_a_followup_with_vitals(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('followups.store'), [
            'patient_id' => $patient->id,
            'followup_at' => now()->format('Y-m-d H:i:s'),
            'improved_status' => 'yes',
            'improved_score' => 8,
            'vitals' => [
                'temperature_c' => 38.5,
                'heart_rate_bpm' => 110,
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('followups', [
            'patient_id' => $patient->id,
            'improved_status' => 'yes',
        ], 'tenant');
        $this->assertDatabaseHas('followup_vitals', [
            'temperature_c' => 38.5,
            'heart_rate_bpm' => 110,
        ], 'tenant');
    }

    /** @test */
    public function it_validates_improved_score_range(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $this->actingAs($user);

        $response = $this->from(route('followups.create'))
            ->post(route('followups.store'), [
                'patient_id' => $patient->id,
                'followup_at' => now()->format('Y-m-d H:i:s'),
                'improved_status' => 'yes',
                'improved_score' => 15,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('improved_score');
    }

    /** @test */
    public function it_handles_attachment_upload_limits(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $patient = Patient::factory()->create();
        $followup = Followup::factory()->create(['patient_id' => $patient->id]);
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 11000, 'application/pdf');

        $response = $this->from(route('followups.show', $followup))
            ->post(route('followups.attachments.store', $followup), [
                'title' => 'Adjunto grande',
                'file' => $file,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('file');
    }

    /** @test */
    public function it_lists_followups_by_patient(): void
    {
        $user = User::factory()->create();
        $patientA = Patient::factory()->create();
        $patientB = Patient::factory()->create();
        $followupA = Followup::factory()->create(['patient_id' => $patientA->id]);
        Followup::factory()->create(['patient_id' => $patientB->id]);

        $this->actingAs($user);

        $response = $this->get(route('followups.index', ['patient_id' => $patientA->id]));

        $response->assertOk();
        $this->assertTrue($response->viewData('followups')->contains('id', $followupA->id));
        $this->assertCount(1, $response->viewData('followups'));
    }
}
