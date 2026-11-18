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
    Schema::create('phones', function (Blueprint $table) {
            $table->id();
            
            // Link each phone to its owner and remove it if the owner is deleted.
            $table->foreignId('user_phone_id')->constrained('user_phones')->onDelete('cascade');
            
            $table->string('phone_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
