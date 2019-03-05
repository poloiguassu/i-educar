<?php

use Illuminate\Database\Seeder;

class MenuVPSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO portal.menu_menu VALUES (72, 'VPS', null, null, '/intranet/selecao_vps_index.php', 10, true, 'fa-user-plus');
            INSERT INTO pmicontrolesis.tutormenu VALUES (24, 'VPS');

            -- Submenu
            INSERT INTO portal.menu_submenu VALUES (21455, 72, 2, 'VPS', 'educar_vps_index.php', null, 2);
            INSERT INTO portal.menu_submenu VALUES (21456, 72, 2, 'Encaminhados', 'educar_vps_relatorio_encaminhados.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21457, 72, 2, 'Visitas', 'educar_vps_visita_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21458, 72, 2, 'Tipo Contração', 'educar_vps_tipo_contratacao_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21459, 72, 2, 'Responsável Entrevista', 'educar_vps_responsavel_entrevista_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21460, 72, 2, 'Jornada Trabalho', 'educar_vps_jornada_trabalho_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21461, 72, 2, 'Idiomas', 'educar_vps_idioma_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21462, 72, 2, 'Função', 'educar_vps_funcao_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21463, 72, 2, 'Atribuir aluno', 'educar_vps_atribuir_aluno_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21464, 72, 2, 'Alunos Aptos', 'educar_vps_aluno_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21465, 72, 2, 'Alunos Encaminhados', 'educar_vps_aluno_encaminhado_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21466, 72, 2, 'Entrevistas', 'educar_entrevista_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21467, 72, 2, 'Empresas', 'educar_empresa_entrevista_lst.php', null, 3);
            INSERT INTO portal.menu_submenu VALUES (21468, 72, 2, 'Estatísticas Gerais', 'educar_vps_index.php', null, 3);


            -- Menu Suspenso
            INSERT INTO pmicontrolesis.menu VALUES (21254, null, null, 'Cadastros', 1, '', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21255, 21462, 21254, 'Função', 2, 'educar_vps_funcao_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21256, 21461, 21254, 'Idiomas', 2, 'educar_vps_idioma_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21257, 21458, 21254, 'Tipo Contratação', 2, 'educar_vps_tipo_contratacao_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21258, 21460, 21254, 'Jornada Trabalho', 2, 'educar_vps_jornada_trabalho_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21259, null, null, 'Entrevistas', 1, '', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21260, 21466, 21259, 'Entrevistas', 1, 'educar_entrevista_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21261, 21467, 21259, 'Empresas', 1, 'educar_empresa_entrevista_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21262, 21457, 21259, 'Visitas', 2, 'educar_vps_visita_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21263, 21459, 21259, 'Responsável Entrevista', 2, 'educar_vps_responsavel_entrevista_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21264, 21464, 21259, 'Alunos Aptos', 2, 'educar_vps_aluno_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21265, 21465, 21259, 'Encaminhar', 2, 'educar_vps_aluno_encaminhado_lst.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21266, null, null, 'Relatórios', 1, '', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21267, 21465, 21266, 'Alunos Encaminhados', 2, 'educar_vps_relatorio_encaminhados.php', '_self', 1, 24);
            INSERT INTO pmicontrolesis.menu VALUES (21268, 21468, 21266, 'Estatísticas Gerais', 2, 'educar_vps_index.php', '_self', 1, 24);"
        );
    }
}
