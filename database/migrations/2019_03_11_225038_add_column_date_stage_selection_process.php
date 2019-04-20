<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDateStageSelectionProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.inscrito_etapa', function (Blueprint $table) {
            $table->integer('ref_cod_etapa_data')->nullable();

            $table->foreign('ref_cod_etapa_data')
                ->references('cod_etapa_data')
                ->on('pmieducar.selecao_etapa_data')
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
        Schema::table('pmieducar.inscrito_etapa', function (Blueprint $table) {
            $table->dropColumn('ref_cod_etapa_data');
        });
    }
}
