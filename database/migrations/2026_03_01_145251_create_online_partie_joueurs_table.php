<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('online_partie_joueurs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('online_partie_id');
            $table->unsignedBigInteger('joueur_id');
            $table->enum('role', ['civil', 'undercover', 'mister_white'])->nullable();
            $table->string('mot')->nullable(); // the word given to this player
            $table->boolean('est_elimine')->default(false);
            $table->boolean('a_parle')->default(false); // has spoken this round
            $table->integer('ordre')->default(0); // speaking order
            $table->timestamps();

            $table->foreign('online_partie_id')->references('id')->on('online_parties')->onDelete('cascade');
            $table->foreign('joueur_id')->references('id')->on('joueurs')->onDelete('cascade');
            $table->unique(['online_partie_id', 'joueur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_partie_joueurs');
    }
};
