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
        Schema::create('vente_medicament', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('vente_id');
    $table->char('medicament_id', 10);
    $table->integer('quantite');
    $table->decimal('prix_unitaire', 10, 2);
    $table->decimal('sous_total', 10, 2);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vente_medicament');
    }
};
