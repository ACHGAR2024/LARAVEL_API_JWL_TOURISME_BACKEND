<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesReservationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('places_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_place_tiket')->nullable();
            $table->string('address_place')->nullable();
            $table->date('reservation_start_date')->nullable();
            $table->date('reservation_end_date')->nullable();
            $table->unsignedInteger('id_events');
            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
            $table->foreign('id_events')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('places_reservations');
    }
}