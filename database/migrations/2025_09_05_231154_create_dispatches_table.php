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
            Schema::create('dispatches', function (Blueprint $table) {
                $table->id();
                $table->string('dispatch_type'); // Art des Einsatzes (z.B. "Brand", "Verkehrsunfall")
                $table->string('status')->default('new'); // Status des Einsatzes (z.B. "new", "on_route", "on_scene", "completed")
                $table->float('lat', 10, 6); // Breitengrad
                $table->float('lon', 10, 6); // Längengrad
                $table->integer('reward'); // Belohnung für den Einsatz
                $table->unsignedBigInteger('user_id'); // Zugehörigkeit zum Spieler
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
