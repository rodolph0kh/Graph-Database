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
        Schema::create('relationship_follows', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('source_id');
			$table->string('source_type');
			$table->unsignedBigInteger('destination_id');
			$table->string('destination_type');
			$table->string('properties');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationship_follows');
    }
};
