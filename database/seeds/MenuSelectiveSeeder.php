<?php

use Illuminate\Database\Seeder;

class MenuSelectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO portal.menu_menu VALUES (73, 'Processo Seletivo', null, null, '/intranet/selecao_inscritos_lst.php', 1, true, 'fa-briefcase');
            INSERT INTO pmicontrolesis.tutormenu VALUES (23, 'Processo Seletivo');

            -- Submenu
            INSERT INTO portal.menu_submenu VALUES (21469, 73, 2, 'Candidatos', 'selecao_inscritos_lst.php', null, 2);
            INSERT INTO portal.menu_submenu VALUES (21470, 73, 2, 'Estatísticas Gerais', 'selecao_estatistica_lst.php', null, 2);
            INSERT INTO portal.menu_submenu VALUES (21472, 73, 2, 'Processo Seletivo', 'selecao_processo_lst.php', null, 2);

            -- Menu Suspenso
            INSERT INTO pmicontrolesis.menu VALUES (21269, null, null, 'Cadastros', 1, '', '_self', 1, 23);
            INSERT INTO pmicontrolesis.menu VALUES (21274, 21472, 21269, 'Processo Seletivo', 0, 'selecao_processo_lst.php', '_self', 1, 23);
            INSERT INTO pmicontrolesis.menu VALUES (21270, 21469, 21269, 'Candidatos', 1, 'selecao_inscritos_lst.php', '_self', 1, 23);
            INSERT INTO pmicontrolesis.menu VALUES (21271, null, null, 'Relatório', 1, '', '_self', 1, 23);
            INSERT INTO pmicontrolesis.menu VALUES (21272, 21470, 21271, 'Estatísticas Gerais', 1, 'selecao_estatistica_lst.php', '_self', 1, 23);
        );
    }
}
