<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaForeignEscolaMunicipio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.inscrito', function (Blueprint $table) {
            $table->foreign('estudando_escola')
                ->references('idescola')->on('public.escola_municipio')
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
        Schema::table('pmieducar.inscrito', function (Blueprint $table) {
            $table->dropForeign(['estudando_escola']);
        });
    }
}
