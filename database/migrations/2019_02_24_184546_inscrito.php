<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Inscrito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'pmieducar.inscrito',
            function (Blueprint $table) {
                $table->increments('cod_inscrito')->unique();
                $table->integer('ref_cod_selecao_processo');
                $table->integer('ref_cod_aluno');
                $table->integer('estudando_escola')->nullable();
                $table->smallInteger('estudando_serie')->nullable();
                $table->smallInteger('estudando_turno')->nullable();
                $table->integer('egresso')->nullable();
                $table->smallInteger('guarda_mirim')->default(0);
                $table->integer('area_interesse')->nullable();
                $table->smallInteger('copia_rg')->default(0);
                $table->smallInteger('copia_residencia')->default(0);
                $table->smallInteger('copia_historico')->default(0);
                $table->smallInteger('copia_renda')->default(0);
                $table->smallInteger('encaminhamento')->default(0);
                $table->integer('ref_usuario_exc')->nullable();
                $table->integer('ref_usuario_cad');
                $table->date('data_cadastro')->nullable();
                $table->date('data_exclusao')->nullable();
                $table->smallInteger('ativo')->default(1);

                $table->dropPrimary('cod_inscrito');

                $table->index(['ref_cod_selecao_processo', 'ref_cod_aluno']);


                $table->foreign('ref_cod_aluno')
                    ->references('cod_aluno')->on('pmieducar.aluno')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign('estudando_escola')
                    ->references('idescola')->on('public.escola_municipio')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');

                $table->foreign('ref_cod_selecao_processo')
                    ->references('cod_selecao_processo')
                    ->on('pmieducar.selecao_processo')
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
                    pmieducar.inscrito
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
    Schema::dropIfExists('pmieducar.inscrito');
    }
}
