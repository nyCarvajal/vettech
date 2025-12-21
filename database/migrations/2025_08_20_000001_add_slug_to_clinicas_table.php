<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            if (! Schema::hasColumn('clinicas', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('nombre');
            }
        });

        $clinicas = DB::table('clinicas')->select('id', 'nombre', 'slug')->get();

        foreach ($clinicas as $peluqueria) {
            if (! empty($peluqueria->slug)) {
                continue;
            }

            $baseSlug = Str::slug($peluqueria->nombre ?: 'clinica-' . $peluqueria->id);
            $slug = $baseSlug;
            $counter = 1;

            while (DB::table('clinicas')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            DB::table('clinicas')->where('id', $peluqueria->id)->update([
                'slug' => $slug,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {
            if (Schema::hasColumn('clinicas', 'slug')) {
                $table->dropUnique('clinicas_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};
