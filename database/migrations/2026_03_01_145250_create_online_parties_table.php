<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('online_parties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique(); // room code
            // statuts : waiting, playing, voting, mister_white_guess, finished
            $table->enum('statut', ['waiting', 'playing', 'voting', 'mister_white_guess', 'finished'])->default('waiting');
            $table->unsignedBigInteger('mot_id')->nullable();
            $table->string('mot_civil')->nullable();       // the civilians' word
            $table->string('mot_undercover')->nullable();  // the undercover's word
            $table->integer('joueur_actuel_index')->default(0); // whose turn it is
            $table->integer('phase_vote_id')->nullable();  // which player is being voted (online_partie_joueurs.id)
            $table->timestamp('timer_expiry')->nullable(); // when the current timer expires
            $table->integer('tour_numero')->default(1);
            $table->timestamps();
            $table->foreign('mot_id')->references('id')->on('mots')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_parties');
    }
};
