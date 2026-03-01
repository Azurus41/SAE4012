<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('online_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('online_partie_id');
            $table->unsignedBigInteger('joueur_id');
            $table->text('contenu');
            $table->timestamps();

            $table->foreign('online_partie_id')->references('id')->on('online_parties')->onDelete('cascade');
            $table->foreign('joueur_id')->references('id')->on('joueurs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_messages');
    }
};
