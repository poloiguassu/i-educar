<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriaTabelaEscolaMunicipio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public.escola_municipio', function (Blueprint $table) {
            $table->increments('idescola');
            $table->string('nome');
            $table->integer('ref_idmun');
            $table->smallInteger('ativo')->default(1);

            $table->foreign('ref_idmun')
                ->references('idmun')->on('public.municipio')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public.escola_municipio');
    }
}
