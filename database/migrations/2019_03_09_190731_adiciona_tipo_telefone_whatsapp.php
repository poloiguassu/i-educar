<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdicionaTipoTelefoneWhatsapp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            "CREATE OR REPLACE VIEW cadastro.v_fone_pessoa AS
                SELECT DISTINCT t.idpes,
                    ( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 1::numeric
                        AND t.idpes = t1.idpes) AS ddd_1,
                    ( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 1::numeric
                        AND t.idpes = t1.idpes) AS fone_1,
                    ( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 2::numeric
                        AND t.idpes = t1.idpes) AS ddd_2,
                    ( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 2::numeric
                        AND t.idpes = t1.idpes) AS fone_2,
                    ( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 3::numeric
                        AND t.idpes = t1.idpes) AS ddd_mov,
                    ( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 3::numeric
                        AND t.idpes = t1.idpes) AS fone_mov,
                    ( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 4::numeric
                        AND t.idpes = t1.idpes) AS ddd_fax,
                    ( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 4::numeric
                        AND t.idpes = t1.idpes) AS fone_fax,
                    ( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 5::numeric
                        AND t.idpes = t1.idpes) AS ddd_whatsapp,
                    ( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 5::numeric
                        AND t.idpes = t1.idpes) AS fone_whatsapp
                FROM cadastro.fone_pessoa t
                ORDER BY t.idpes,
                    (( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 1::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 1::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 2::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 2::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 3::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 3::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 4::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 4::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.ddd
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 5::numeric
                        AND t.idpes = t1.idpes)),
                    (( SELECT t1.fone
                        FROM cadastro.fone_pessoa t1
                        WHERE t1.tipo = 5::numeric
                        AND t.idpes = t1.idpes))"
        );

        DB::unprepared(
            "ALTER TABLE
                cadastro.fone_pessoa
            DROP CONSTRAINT ck_fone_pessoa_tipo,
            ADD  CONSTRAINT ck_fone_pessoa_tipo
                CHECK (tipo >= 1::numeric AND tipo <= 5::numeric);"
        );

        DB::unprepared(
            "ALTER TABLE
                historico.fone_pessoa
            DROP CONSTRAINT ck_fone_pessoa_tipo,
            ADD  CONSTRAINT ck_fone_pessoa_tipo
                CHECK (tipo >= 1::numeric AND tipo <= 5::numeric);"
        );

        DB::unprepared(
            "DROP TRIGGER IF EXISTS trg_aft_fone_historico_campo
                ON cadastro.fone_pessoa;

            DROP FUNCTION IF EXISTS consistenciacao.fcn_fone_historico_campo();

            CREATE FUNCTION consistenciacao.fcn_fone_historico_campo() RETURNS trigger
                LANGUAGE plpgsql
                AS $$
            DECLARE
            v_idpes       numeric;

            v_ddd_antigo      numeric;
            v_ddd_novo      numeric;
            v_fone_antigo     numeric;
            v_fone_novo     numeric;
            v_tipo_fone     numeric;

            v_comando     text;
            v_origem_gravacao   text;

            v_credibilidade_maxima    numeric;
            v_credibilidade_alta    numeric;
            v_sem_credibilidade   numeric;

            v_nova_credibilidade    numeric;

            v_registro      record;

            -- ID dos campos
            v_idcam_ddd_fone_residencial  numeric;
            v_idcam_fone_residencial  numeric;
            v_idcam_ddd_fone_comercial  numeric;
            v_idcam_fone_comercial    numeric;
            v_idcam_ddd_fone_celular  numeric;
            v_idcam_fone_celular    numeric;
            v_idcam_ddd_fax     numeric;
            v_idcam_fax     numeric;
            v_idcam_ddd_whatsapp     numeric;
            v_idcam_whatsapp     numeric;

            v_idcam_ddd     numeric;
            v_idcam_fone      numeric;

            BEGIN
                v_idpes   := NEW.idpes;
                v_tipo_fone := NEW.tipo;
                v_ddd_novo  := NEW.ddd;
                v_fone_novo := NEW.fone;

                IF TG_OP <> 'UPDATE' THEN
                v_ddd_antigo  := 0;
                v_fone_antigo := 0;
                ELSE
                v_ddd_antigo  := COALESCE(OLD.ddd, 0);
                v_fone_antigo := COALESCE(OLD.fone, 0);
                END IF;

                v_idcam_ddd_fone_residencial  := 39;
                v_idcam_fone_residencial  := 40;
                v_idcam_ddd_fone_comercial  := 41;
                v_idcam_fone_comercial    := 42;
                v_idcam_ddd_fone_celular  := 43;
                v_idcam_fone_celular    := 44;
                v_idcam_ddd_fax     := 45;
                v_idcam_fax     := 46;
                v_idcam_ddd_whatsapp     := 47;
                v_idcam_whatsapp     := 48;

                v_nova_credibilidade := 0;
                v_credibilidade_maxima := 1;
                v_credibilidade_alta := 2;
                v_sem_credibilidade := 5;
                v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';

                FOR v_registro IN EXECUTE v_comando LOOP
                v_origem_gravacao := v_registro.origem_gravacao;
                END LOOP;

                IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou usuário do Oscar
                v_nova_credibilidade := v_credibilidade_maxima;
                ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
                v_nova_credibilidade := v_credibilidade_alta;
                END IF;

                IF v_tipo_fone = 1 THEN
                v_idcam_ddd := v_idcam_ddd_fone_residencial;
                v_idcam_fone := v_idcam_fone_residencial;
                ELSIF v_tipo_fone = 2 THEN
                v_idcam_ddd := v_idcam_ddd_fone_comercial;
                v_idcam_fone := v_idcam_fone_comercial;
                ELSIF v_tipo_fone = 3 THEN
                v_idcam_ddd := v_idcam_ddd_fone_celular;
                v_idcam_fone := v_idcam_fone_celular;
                ELSIF v_tipo_fone = 4 THEN
                v_idcam_ddd := v_idcam_ddd_fax;
                v_idcam_fone := v_idcam_fax;
                ELSIF v_tipo_fone = 5 THEN
                v_idcam_ddd := v_idcam_ddd_whatsapp;
                v_idcam_fone := v_idcam_whatsapp;
                END IF;

                IF v_nova_credibilidade > 0 THEN
                IF v_ddd_novo <> v_ddd_antigo THEN
                    EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ddd||','||v_nova_credibilidade||');';
                END IF;

                IF v_fone_novo <> v_fone_antigo THEN
                    EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fone||','||v_nova_credibilidade||');';
                END IF;
                END IF;

                -- Verificar os campos Vazios ou Nulos
                IF v_ddd_novo <= 0 OR v_ddd_novo IS NULL THEN
                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ddd||','||v_sem_credibilidade||');';
                END IF;
                IF v_fone_novo <= 0 OR v_fone_novo IS NULL THEN
                EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_fone||','||v_sem_credibilidade||');';
                END IF;

            RETURN NEW;
            END; $$;
            CREATE TRIGGER trg_aft_fone_historico_campo
            AFTER INSERT OR UPDATE
            ON cadastro.fone_pessoa
            FOR EACH ROW
            EXECUTE PROCEDURE consistenciacao.fcn_fone_historico_campo();"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            'DROP VIEW IF EXISTS cadastro.v_fone_pessoa'
        );
    }
}
