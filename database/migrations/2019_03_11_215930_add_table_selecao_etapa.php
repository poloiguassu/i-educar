<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableSelecaoEtapa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pmieducar.selecao_etapa', function (Blueprint $table) {
            $table->integer('ref_cod_selecao_processo');
            $table->smallInteger('etapa');
            $table->string('nome');
            $table->smallInteger('ativo')->default(1);

            $table->primary(['ref_cod_selecao_processo', 'etapa']);

            $table->foreign('ref_cod_selecao_processo')
                ->references('cod_selecao_processo')
                ->on('pmieducar.selecao_processo')
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
        Schema::dropIfExists('pmieducar.selecao_etapa');
    }
}
