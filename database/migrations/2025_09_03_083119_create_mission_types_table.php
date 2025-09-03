<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mission_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('required_vehicle_types'); // Speichert, welche Fahrzeugtypen benötigt werden (z.B. LF20, RTW)
            $table->integer('min_vehicles'); // Mindestanzahl an Fahrzeugen
            $table->integer('max_vehicles'); // Höchstanzahl an Fahrzeugen
            $table->integer('reward'); // Belohnung für den Einsatz
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_types');
    }
};
