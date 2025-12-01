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
    Schema::table('anggotas', function (Blueprint $table) {

        // referral: siapa yang mengundang
        $table->unsignedBigInteger('referred_by')->nullable()->after('sponsor');

        // poin dan koin
        $table->integer('poin')->default(0)->after('referred_by');
        $table->integer('koin')->default(0)->after('poin');

        // foreign key (pastikan table-nya 'anggotas')
        $table->foreign('referred_by')
              ->references('id')
              ->on('anggotas')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('anggotas', function (Blueprint $table) {

        // hapus FK dulu
        $table->dropForeign(['referred_by']);

        // lalu hapus kolom
        $table->dropColumn(['referred_by', 'poin', 'koin']);
    });
}

};
