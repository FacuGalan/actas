<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql';

    public function up(): void
    {
        Schema::connection('mysql')->create('fa_registro_control', function (Blueprint $table) {
            $table->id();
            $table->integer('operativo_id');
            $table->integer('inspector_id');
            $table->date('fecha');
            $table->time('hora');
            $table->string('dni', 20)->nullable();
            $table->string('nombreinf', 255)->nullable();
            $table->string('dominio', 20)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('crea_user', 50)->nullable();
            $table->date('crea_fecha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('fa_registro_control');
    }
};
