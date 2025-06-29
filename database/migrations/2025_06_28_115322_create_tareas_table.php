<?php

// 1. MIGRACIÓN - database/migrations/xxxx_create_tareas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id(); // Primary key auto increment
            $table->string('name', 500);
            $table->datetime('created_at');
            $table->string('descripcion', 50);

            // Índices
            $table->index('id'); // PRIMARY (id) UNIQUE ya está implícito con id()
        });
    }

    public function down()
    {
        Schema::dropIfExists('tareas');
    }
};
