<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSelectedAreaSelecion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pmieducar.inscrito', function (Blueprint $table) {
            $table->smallInteger('area_selecionado')->nullable();
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
            $table->dropColumn('area_selecionado');
        });
    }
}
