<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('Nom');
            $table->string('Prenom');
            $table->char('Phone', 10);
            $table->string('mot_de_passe');
            $table->enum('role', ['Administrateur', 'Pharmacien']);
            $table->tinyInteger('actif')->default(0);
            $table->tinyInteger('doit_changer_mdp')->default(1);
            $table->dateTime('derniere_connexion')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
