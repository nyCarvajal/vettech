# Tablas del módulo de hospitalización

A continuación se listan las tablas creadas por la migración `2025_09_01_000000_create_hospitalization_tables.php`, incluyendo columnas relevantes, tipos y llaves declaradas.

## cages
- id (bigint, PK)
- name (string)
- active (boolean, default true)
- created_at / updated_at (timestamps)

## hospital_stays
- id (bigint, PK)
- patient_id (FK -> patients)
- owner_id (FK -> owners)
- cage_id (FK -> cages, nullable)
- admitted_at (datetime)
- discharged_at (datetime, nullable)
- status (enum: active, discharged; default active)
- severity (enum: stable, observation, critical; default stable)
- primary_dx (text, nullable)
- plan (text, nullable)
- diet (text, nullable)
- created_by (FK -> users)
- created_at / updated_at (timestamps)
- Índices: patient_id, status, admitted_at

## hospital_days
- id (bigint, PK)
- stay_id (FK -> hospital_stays, cascade on delete)
- date (date)
- day_number (unsigned integer)
- notes (text, nullable)
- created_at / updated_at (timestamps)
- Unique: (stay_id, date)

## hospital_orders
- id (bigint, PK)
- stay_id (FK -> hospital_stays, cascade on delete)
- day_id (FK -> hospital_days, nullable, null on delete)
- type (enum: medication, procedure, feeding, fluid, other)
- source (enum: inventory, manual)
- product_id (FK -> products, nullable)
- manual_name (string, nullable)
- dose (string, 80, nullable)
- route (string, 50, nullable)
- frequency (string, 50, nullable)
- schedule_json (json, nullable)
- start_at (datetime)
- end_at (datetime, nullable)
- instructions (text, nullable)
- status (enum: active, stopped; default active)
- created_by (FK -> users)
- created_at / updated_at (timestamps)
- Índices: stay_id, type, status

## hospital_administrations
- id (bigint, PK)
- order_id (FK -> hospital_orders, cascade on delete)
- stay_id (FK -> hospital_stays, cascade on delete)
- day_id (FK -> hospital_days, cascade on delete)
- scheduled_time (time, nullable)
- administered_at (datetime, nullable)
- dose_given (string, 80, nullable)
- status (enum: done, skipped, late; default done)
- notes (text, nullable)
- administered_by (FK -> users)
- created_at / updated_at (timestamps)
- Índices: stay_id, day_id, administered_at

## hospital_vitals
- id (bigint, PK)
- stay_id (FK -> hospital_stays, cascade on delete)
- day_id (FK -> hospital_days, cascade on delete)
- measured_at (datetime)
- temp (decimal 4,1, nullable)
- hr (unsigned smallint, nullable)
- rr (unsigned smallint, nullable)
- spo2 (decimal 5,2, nullable)
- bp (string 30, nullable)
- weight (decimal 6,2, nullable)
- pain_scale (unsigned tinyint, nullable)
- hydration (string 30, nullable)
- mucous (string 30, nullable)
- crt (string 30, nullable)
- notes (text, nullable)
- measured_by (FK -> users)
- created_at / updated_at (timestamps)
- Índices: stay_id, day_id, measured_at

## hospital_progress_notes
- id (bigint, PK)
- stay_id (FK -> hospital_stays, cascade on delete)
- day_id (FK -> hospital_days, cascade on delete)
- logged_at (datetime)
- shift (enum: manana, tarde, noche; nullable)
- content (text)
- author_id (FK -> users)
- created_at / updated_at (timestamps)
- Índices: stay_id, day_id, logged_at

## hospital_charges
- id (bigint, PK)
- stay_id (FK -> hospital_stays, cascade on delete)
- day_id (FK -> hospital_days, nullable, null on delete)
- source (enum: service, inventory, manual)
- product_id (FK -> products, nullable)
- description (string)
- qty (integer, default 1)
- unit_price (decimal 12,2, default 0)
- total (decimal 12,2, default 0)
- created_by (FK -> users)
- created_at (datetime)
- Índices: stay_id, day_id
