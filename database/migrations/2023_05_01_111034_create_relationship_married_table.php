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
        Schema::create('relationship_married', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('first_node_id');
			$table->string('first_node_type');
			$table->unsignedBigInteger('second_node_id');
			$table->string('second_node_type');
			$table->string('properties');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationship_married');
    }
};
