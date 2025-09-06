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
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('vehicle_type');
                $table->unsignedBigInteger('station_id');
                $table->string('name');
                $table->integer('price');
                $table->string('image');
                $table->json('stats');
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
