<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('ad_categories', function (Blueprint $table) {
            $table->foreignId('place_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->primary(['place_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_categories');
    }
}