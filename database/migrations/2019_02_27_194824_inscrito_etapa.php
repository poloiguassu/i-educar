<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InscritoEtapa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'pmieducar.inscrito_etapa',
            function (Blueprint $table) {
                $table->integer('ref_cod_inscrito');
                $table->integer('ref_cod_selecao_processo');
                $table->smallInteger('etapa');
                $table->smallInteger('situacao')->default(0);

                $table->index(
                    [
                        'ref_cod_inscrito',
                        'ref_cod_selecao_processo',
                        'etapa'
                    ]
                );

                $table->foreign('ref_cod_inscrito')
                    ->references('cod_inscrito')
                    ->on('pmieducar.inscrito')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign('ref_cod_selecao_processo')
                    ->references('cod_selecao_processo')
                    ->on('pmieducar.selecao_processo')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');
            }
        );

        DB::unprepared(
            'CREATE TRIGGER fcn_aft_update
                AFTER INSERT OR UPDATE ON
                    pmieducar.inscrito_etapa
                FOR EACH ROW
                EXECUTE PROCEDURE pmieducar.fcn_aft_update();'
        );
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
