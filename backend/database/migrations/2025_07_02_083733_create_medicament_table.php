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
        Schema::create('medicament', function (Blueprint $table) {
            $table->char('reference', 10)->primary();
            $table->string('nom');
            $table->integer('quantite');
            $table->date('date_expiration');
            $table->string('dosage', 20);
            $table->enum('frequence_utilisation', ['Forte', 'Faible'])->default('Faible');
            $table->double('prix_unitaire')->default(0);
            $table->timestamps();

            $table->foreignId('categorie_id')->nullable()->constrained('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicament');
    }
};
