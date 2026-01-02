<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\TravelCertificate::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:national_co,international'],
            'language' => ['required', 'in:es,en,es_en'],
            'clinic_name' => ['required', 'string'],
            'clinic_nit' => ['required', 'string'],
            'clinic_address' => ['required', 'string'],
            'clinic_phone' => ['nullable', 'string'],
            'clinic_city' => ['nullable', 'string'],
            'vet_name' => ['required', 'string'],
            'vet_license' => ['required', 'string'],
            'owner_name' => ['required', 'string'],
            'owner_document_type' => ['nullable', 'string'],
            'owner_document_number' => ['nullable', 'string'],
            'owner_phone' => ['nullable', 'string'],
            'owner_email' => ['nullable', 'email'],
            'owner_address' => ['nullable', 'string'],
            'owner_city' => ['nullable', 'string'],
            'pet_id' => ['nullable', 'integer'],
            'pet_name' => ['required', 'string'],
            'pet_species' => ['required', 'string'],
            'pet_breed' => ['nullable', 'string'],
            'pet_sex' => ['nullable', 'string'],
            'pet_age_months' => ['nullable', 'integer'],
            'pet_weight_kg' => ['nullable', 'numeric'],
            'pet_color' => ['nullable', 'string'],
            'pet_marks' => ['nullable', 'string'],
            'pet_microchip' => ['nullable', 'string'],
            'travel_departure_date' => ['required', 'date'],
            'travel_departure_time' => ['nullable'],
            'transport_type' => ['nullable', 'in:air,land,other'],
            'transport_company' => ['nullable', 'string'],
            'flight_number' => ['nullable', 'string'],
            'origin_type' => ['nullable', 'in:co,international'],
            'origin_department_id' => ['required_if:type,national_co', 'nullable', 'exists:geo_departments,id'],
            'origin_municipality_id' => ['required_if:type,national_co', 'nullable', 'exists:geo_municipalities,id'],
            'destination_department_id' => ['required_if:type,national_co', 'nullable', 'exists:geo_departments,id'],
            'destination_municipality_id' => ['required_if:type,national_co', 'nullable', 'exists:geo_municipalities,id'],
            'origin_country_code' => ['required_if:type,international', 'nullable', 'string', 'size:2'],
            'destination_country_code' => ['required_if:type,international', 'nullable', 'string', 'size:2'],
            'origin_city' => ['nullable', 'required_if:type,international', 'string'],
            'destination_city' => ['nullable', 'required_if:type,international', 'string'],
            'clinical_exam_at' => ['required', 'date'],
            'clinical_notes' => ['nullable', 'string'],
            'fit_for_travel' => ['boolean'],
            'declaration_text' => ['required', 'string'],
            'extras' => ['nullable', 'array'],
            'extras.*' => ['nullable'],
            'vaccinations' => ['nullable', 'array'],
            'vaccinations.*.vaccine_name' => ['required_with:vaccinations', 'string'],
            'vaccinations.*.product_name' => ['nullable', 'string'],
            'vaccinations.*.batch_lot' => ['nullable', 'string'],
            'vaccinations.*.applied_at' => ['required_with:vaccinations', 'date'],
            'vaccinations.*.valid_until' => ['nullable', 'date'],
            'vaccinations.*.notes' => ['nullable', 'string'],
            'dewormings' => ['nullable', 'array'],
            'dewormings.*.kind' => ['required_with:dewormings', 'in:internal,external'],
            'dewormings.*.product_name' => ['required_with:dewormings', 'string'],
            'dewormings.*.active_ingredient' => ['nullable', 'string'],
            'dewormings.*.batch_lot' => ['nullable', 'string'],
            'dewormings.*.applied_at' => ['required_with:dewormings', 'date'],
            'dewormings.*.notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.title' => ['required_with:attachments', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'attachments.*.file' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ];
    }
}
