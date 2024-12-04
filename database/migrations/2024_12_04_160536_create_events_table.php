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
        Schema::create('events', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); // Event name
            $table->timestamp('start_time')->default(DB::raw('CURRENT_TIMESTAMP'));; 
            $table->timestamp('end_time')->default(DB::raw('CURRENT_TIMESTAMP'));; 
            $table->text('description'); // Event description
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
