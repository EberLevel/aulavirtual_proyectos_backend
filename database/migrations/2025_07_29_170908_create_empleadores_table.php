<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadoresTable extends Migration
{
    public function up()
    {
        Schema::create('empleadores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('domain_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('domain_id')->references('id')->on('domains');
        });
    }

    public function down()
    {
        Schema::dropIfExists('empleadores');
    }
}