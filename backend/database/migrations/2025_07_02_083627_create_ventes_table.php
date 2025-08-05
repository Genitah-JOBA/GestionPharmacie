<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentesTable extends Migration
{
    public function up()
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id(); // bigint unsigned par défaut
            $table->string('code');
            $table->string('mode_paiement');
            $table->decimal('montant_total', 10, 2);
            $table->integer('user_id'); // 👈 Doit être INT, pas unsignedBigInteger
            $table->unsignedBigInteger('client_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ventes');
    }
}
