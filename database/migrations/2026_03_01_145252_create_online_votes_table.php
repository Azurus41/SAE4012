<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('online_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('online_partie_id');
            $table->unsignedBigInteger('votant_id');    // the player voting
            $table->unsignedBigInteger('cible_id');     // who they're voting for
            $table->integer('tour_numero');
            $table->timestamps();

            $table->foreign('online_partie_id')->references('id')->on('online_parties')->onDelete('cascade');
            $table->foreign('votant_id')->references('id')->on('joueurs')->onDelete('cascade');
            $table->foreign('cible_id')->references('id')->on('joueurs')->onDelete('cascade');
            $table->unique(['online_partie_id', 'votant_id', 'tour_numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_votes');
    }
};
