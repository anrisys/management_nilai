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
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->string('kelas', 10);
            $table->string('mapel', 100);
            $table->integer('nilai')->unsigned()->check('nilai >= 0 AND nilai <= 100');
            $table->timestamps();

            $table->index(['siswa_id']);
            $table->index(['kelas']);
            $table->index(['mapel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
