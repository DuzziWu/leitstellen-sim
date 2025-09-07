<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // In database/migrations/xxxx_xx_xx_create_vehicles_table.php

    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); // Eindeutige ID für jedes Fahrzeug
            $table->string('vehicle_type'); // Speichert den Typ (z.B. 'lf', 'tlf')
            $table->foreignId('station_id')->nullable()->constrained()->onDelete('set null'); // FK zur Wache
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // FK zum Benutzer
            $table->string('status')->default('available');
            $table->unsignedBigInteger('dispatch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
