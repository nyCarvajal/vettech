<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TravelCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'code', 'type', 'status', 'issued_at', 'expires_at', 'clinic_name', 'clinic_nit',
        'clinic_address', 'clinic_phone', 'clinic_city', 'vet_name', 'vet_license', 'vet_signature_path',
        'vet_seal_path', 'owner_name', 'owner_document_type', 'owner_document_number', 'owner_phone',
        'owner_email', 'owner_address', 'owner_city', 'pet_id', 'pet_name', 'pet_species', 'pet_breed',
        'pet_sex', 'pet_age_months', 'pet_weight_kg', 'pet_color', 'pet_marks', 'pet_microchip',
        'travel_departure_date', 'travel_departure_time', 'transport_type', 'transport_company',
        'flight_number', 'origin_type', 'origin_country_code', 'origin_city', 'origin_department_id',
        'origin_municipality_id', 'destination_country_code', 'destination_city',
        'destination_department_id', 'destination_municipality_id', 'clinical_exam_at', 'clinical_notes',
        'fit_for_travel', 'declaration_text', 'language', 'extras', 'canceled_reason', 'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'travel_departure_date' => 'date',
        'travel_departure_time' => 'datetime:H:i',
        'clinical_exam_at' => 'datetime',
        'extras' => 'array',
        'fit_for_travel' => 'boolean',
    ];

    public function vaccinations(): HasMany
    {
        return $this->hasMany(TravelCertificateVaccination::class);
    }

    public function dewormings(): HasMany
    {
        return $this->hasMany(TravelCertificateDeworming::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TravelCertificateAttachment::class);
    }

    public function originDepartment()
    {
        return $this->belongsTo(GeoDepartment::class, 'origin_department_id');
    }

    public function originMunicipality()
    {
        return $this->belongsTo(GeoMunicipality::class, 'origin_municipality_id');
    }

    public function destinationDepartment()
    {
        return $this->belongsTo(GeoDepartment::class, 'destination_department_id');
    }

    public function destinationMunicipality()
    {
        return $this->belongsTo(GeoMunicipality::class, 'destination_municipality_id');
    }

    public function scopeForTenant($query, $tenantId = null)
    {
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        return $query;
    }

    public static function generateCode(?int $tenantId = null): string
    {
        $query = static::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId));
        $last = $query->orderByDesc('id')->value('code');
        $number = $last ? ((int) Str::after($last, 'CV-')) + 1 : 1;

        return 'CV-' . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}
