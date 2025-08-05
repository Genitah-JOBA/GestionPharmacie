<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('message')->after('title');
            $table->enum('type', ['danger', 'warning', 'info'])->default('info')->after('message');
            $table->string('link')->nullable()->after('type');
            $table->boolean('read')->default(false)->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'message', 'type', 'link', 'read']);
        });
    }
};
