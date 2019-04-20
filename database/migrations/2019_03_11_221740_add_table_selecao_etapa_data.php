<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableSelecaoEtapaData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.selecao_etapa_data', function (Blueprint $table) {
            $table->increments('cod_etapa_data');
            $table->integer('ref_cod_selecao_processo');
            $table->smallInteger('etapa');
            $table->date('data_etapa');
            $table->string('horario');
            $table->smallInteger('ativo')->default(1);

            $table->foreign(['ref_cod_selecao_processo', 'etapa'])
                ->references(['ref_cod_selecao_processo', 'etapa'])
                ->on('pmieducar.selecao_etapa')
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
        Schema::dropIfExists('pmieducar.selecao_etapa_data');
    }
}
