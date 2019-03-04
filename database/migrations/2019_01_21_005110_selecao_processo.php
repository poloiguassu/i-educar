<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SelecaoProcesso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'pmieducar.selecao_processo',
            function (Blueprint $table) {        
                $table->increments('cod_selecao_processo');
                $table->integer('ref_cod_escola');
                $table->integer('ref_ano');
                $table->integer('ref_cod_curso');
                $table->smallInteger('numero_selecionados');
                $table->smallInteger('total_etapas');
                $table->boolean('finalizado')->default(0);
                $table->integer('ref_usuario_exc')->nullable();
                $table->integer('ref_usuario_cad');
                $table->date('data_cadastro')->nullable();
                $table->date('data_exclusao')->nullable();
                $table->boolean('ativo')->default(1);

                $table->foreign('ref_cod_escola')
                    ->references('cod_escola')->on('pmieducar.escola')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign(['ref_cod_escola', 'ref_ano'])
                    ->references(['ref_cod_escola', 'ano'])
                    ->on('pmieducar.escola_ano_letivo')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign('ref_cod_curso')
                    ->references('cod_curso')->on('pmieducar.curso')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign('ref_usuario_exc')
                    ->references('cod_usuario')->on('pmieducar.usuario')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign('ref_usuario_cad')
                    ->references('cod_usuario')->on('pmieducar.usuario')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');
            }
        );

        DB::unprepared(
            'CREATE TRIGGER fcn_aft_update
                AFTER INSERT OR UPDATE ON
                    pmieducar.selecao_processo
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
        Schema::dropIfExists('pmieducar.selecao_processo');
    }
}
