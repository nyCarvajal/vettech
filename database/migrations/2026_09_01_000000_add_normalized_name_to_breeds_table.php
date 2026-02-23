<?php

use App\Models\Breed;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->string('normalized_name')->nullable()->after('name');
        });

        Breed::query()->select('id', 'name', 'species_id')->chunkById(100, function ($breeds) {
            foreach ($breeds as $breed) {
                $normalized = Str::lower(trim(preg_replace('/\s+/', ' ', $breed->name)));
                $breed->forceFill(['normalized_name' => $normalized])->save();
            }
        });

        Schema::table('breeds', function (Blueprint $table) {
            $table->unique(['species_id', 'normalized_name'], 'breeds_species_normalized_unique');
            $table->index('normalized_name');
        });
    }

    public function down(): void
    {
        Schema::table('breeds', function (Blueprint $table) {
            $table->dropUnique('breeds_species_normalized_unique');
            $table->dropIndex(['normalized_name']);
            $table->dropColumn('normalized_name');
        });
    }
};
