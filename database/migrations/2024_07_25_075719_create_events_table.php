<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_event', 255)->nullable();
            $table->text('content_event')->nullable();
            $table->date('event_date')->nullable();
            $table->date('event_end_date')->nullable();
            $table->string('address_event', 255)->nullable();
            $table->decimal('price_event', 10, 2)->nullable();
            $table->string('photo_event', 255)->nullable();
            $table->date('publication_date')->nullable();
            $table->unsignedInteger('user_id');
            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}