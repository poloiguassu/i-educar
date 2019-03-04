<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Arquivo disponível desde a versão 1.0.0
 *
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'App/Model/IedFinder.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Servidores - Cadastro de Horários');
        $this->processoAp = '641';
        $this->addEstilo('localizacaoSistema');
    }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 *
 * @category  i-Educar
 *
 * @license   @@license@@
 *
 * @package   iEd_Pmieducar
 *
 * @since     Classe disponível desde a versão 1.0.0
 *
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_curso_;
    public $ref_ref_cod_serie;
    public $ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_disciplina;
    public $dia_semana;
    public $quadro_horario;
    public $ref_cod_quadro_horario;
    public $hora_inicial;
    public $hora_final;
    public $ref_cod_instituicao_servidor;
    public $ref_cod_servidor;
    public $incluir_horario;
    public $excluir_horario;
    public $lst_matriculas;
    public $identificador;
    public $ano_alocacao;

    public $min_mat = 0;
    public $min_ves = 0;
    public $min_not = 0;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_turma          = $_GET['ref_cod_turma'];
        $this->ref_ref_cod_serie      = $_GET['ref_cod_serie'];
        $this->ref_cod_curso          = $_GET['ref_cod_curso'];
        $this->ref_cod_escola         = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao    = $_GET['ref_cod_instituicao'];
        $this->ref_cod_disciplina     = $_GET['ref_cod_disciplina'];
        $this->ref_ref_cod_serie_     = $_GET['ref_ref_cod_serie_'];
        $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
        $this->dia_semana             = $_GET['dia_semana'];
        $this->identificador          = $_GET['identificador'];
        $this->ano_alocacao           = $_GET['ano'];

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}$ano={$this->ano_alocacao}"
        );

        if (!$_POST) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);
        }

        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_quadro_horario)) {
            echo '<script>
              var quadro_horario = 0;
            </script>';

            $obj = new clsPmieducarQuadroHorarioHorarios();
            $lista = $obj->lista(
                $this->ref_cod_quadro_horario,
                $this->ref_ref_cod_serie,
                $this->ref_cod_escola,
                null,
                $this->ref_cod_turma,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->dia_semana
            );

            if ($lista) {
                $qtd_horario = 1;
                foreach ($lista as $campo) {
                    $this->quadro_horario[$qtd_horario]['ref_cod_quadro_horario_']       = $campo['ref_cod_quadro_horario'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_serie_']            = $campo['ref_cod_serie'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_escola_']           = $campo['ref_cod_escola'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_disciplina_']       = $campo['ref_cod_disciplina'];
                    $this->quadro_horario[$qtd_horario]['sequencial_']                   = $campo['sequencial'];
                    $this->quadro_horario[$qtd_horario]['ref_cod_instituicao_servidor_'] = $campo['ref_cod_instituicao_servidor'];
                    $this->quadro_horario[$qtd_horario]['ref_servidor_']                 = $campo['ref_servidor'];
                    $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_']      = $campo['ref_servidor_substituto'];
                    $this->quadro_horario[$qtd_horario]['data_aula_']                    = $campo['data_aula'];
                    $this->quadro_horario[$qtd_horario]['hora_inicial_']                 = substr($campo['hora_inicial'], 0, 5);
                    $this->quadro_horario[$qtd_horario]['hora_final_']                   = substr($campo['hora_final'], 0, 5);
                    $this->quadro_horario[$qtd_horario]['ativo_']                        = $campo['ativo'];
                    $this->quadro_horario[$qtd_horario]['dia_semana_']                   = $campo['dia_semana'];
                    $this->quadro_horario[$qtd_horario]['qtd_horario_']                  = $qtd_horario;
                    $qtd_horario++;

                    /**
                     * salva os dados em uma tabela temporaria
                     * para realizar consulta na listagem
                     */
                    if (!$_POST['identificador']) {
                        $obj_quadro_horario
                            = new clsPmieducarQuadroHorarioHorariosAux(
                                $campo['ref_cod_quadro_horario'],
                                null,
                                $campo['ref_cod_disciplina'],
                                $campo['ref_cod_escola'],
                                $campo['ref_cod_serie'],
                                $campo['ref_cod_instituicao_servidor'],
                                $campo['ref_servidor'],
                                $campo['dia_semana'],
                                substr($campo['hora_inicial'], 0, 5),
                                substr($campo['hora_final'], 0, 5),
                                $this->identificador
                            );

                        $obj_quadro_horario->cadastra();
                    }
                }
            }

            if ($lista) {
                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7)) {
                    if ($this->descricao) {
                        $this->fexcluir = true;
                    }
                }

                $retorno = 'Editar';
            }
        } else {
            header('Location: educar_quadro_horario_lst.php');
            die;
        }

        $this->url_cancelar = "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            [
                $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
                'educar_servidores_index.php'       => 'Servidores',
                ''                                  => "{$nomeMenu} horário"
            ]
        );
        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $desabilitado = 'disabled';

        $this->inputsHelper()->dynamic('instituicao', ['value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('escola', ['value' => $this->ref_cod_escola, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('curso', ['value' => $this->ref_cod_curso, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('serie', ['value' => $this->ref_ref_cod_serie, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('anoLetivo', ['value' => $this->ano_alocacao, 'disabled' => $desabilitado]);

        $this->campoQuebra();

        /**
         * Campos a serem preenchidos com os dados necessários para a inclusão de horários
         */

        // foreign keys
        $opcoes_disc = ['' => 'Selecione uma disciplina'];

        // Componentes curriculares da série
        $componentesTurma = [];
        try {
            $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                $this->ref_ref_cod_serie,
                $this->ref_cod_escola,
                $this->ref_cod_turma
            );
        } catch (Exception $e) {
        }

        if (0 == count($componentesTurma)) {
            $opcoes_disc = ['NULL' => 'A série dessa escola não possui componentes cadastrados'];
        } else {
            $opcoes_disc['todas_disciplinas'] = 'Todas as disciplinas';
            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente;
            }
        }

        $this->campoLista(
            'ref_cod_disciplina',
            'Componente curricular',
            $opcoes_disc,
            $this->ref_cod_disciplina,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $this->campoOculto('identificador', $this->identificador);

        $opcoesDias = [
            '' => 'Selecione um dia da semana',
            1  => 'Domingo',
            2  => 'Segunda-Feira',
            3  => 'Terça-Feira',
            4  => 'Quarta-Feira',
            5  => 'Quinta-Feira',
            6  => 'Sexta-Feira',
            7  => 'Sábado'
        ];

        $this->inputsHelper()->date(
            'data_aula',
            [
                'label' => 'Data da Aula',
                'value' => $this->data_aula,
                'placeholder' => '',
                'required' => true
            ]
        );

        $this->campoOculto('dia_semana', $this->dia_semana);
        $this->campoLista(
            'dia_semana_',
            'Dia da Semana',
            $opcoesDias,
            $this->dia_semana,
            '',
            false,
            '',
            '',
            true,
            false
        );

        $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, false);
        $this->campoHora('hora_final', 'Hora Final', $this->hora_final, false);

        $this->campoListaPesq(
            'ref_cod_servidor',
            'Servidor',
            ['' => 'Selecione um servidor'],
            $this->ref_cod_servidor,
            '',
            '',
            false,
            '',
            '',
            null,
            null,
            '',
            true,
            false,
            false
        );

        $this->campoRotulo(
            'bt_incluir_horario',
            'Horário',
            '<a href=\'#\' id=\'btn_incluir_horario\' >
                <img src=\'imagens/nvp_bot_adiciona.gif\' title=\'Incluir\' border=0>
            </a>'
        );

        $this->campoOculto('incluir_horario', '');

        /**
         * Inclui horários
         */
        if ($_POST['quadro_horario']) {
            $this->quadro_horario = unserialize(urldecode($_POST['quadro_horario']));
        }

        $qtd_horario = count($this->quadro_horario) == 0 ? 1 : count($this->quadro_horario) + 1;

        // primary keys
        if ($this->incluir_horario) {
            if (is_numeric($_POST['ref_cod_servidor'])
                && is_string($_POST['hora_inicial']) && is_string($_POST['hora_final'])
                && is_numeric($_POST['dia_semana']) && is_numeric($_POST['ref_cod_disciplina'])
            ) {
                $this->quadro_horario[$qtd_horario]['ref_cod_quadro_horario_']       = $this->ref_cod_quadro_horario;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_serie_']            = $this->ref_ref_cod_serie;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_escola_']           = $this->ref_cod_escola;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_disciplina_']       = $_POST['ref_cod_disciplina'];
                $this->quadro_horario[$qtd_horario]['ref_cod_instituicao_servidor_'] = $this->ref_cod_instituicao;
                $this->quadro_horario[$qtd_horario]['ref_servidor_']                 = $_POST['ref_cod_servidor'];
                $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_']      = $_POST['ref_servidor_substituto'];
                $this->quadro_horario[$qtd_horario]['data_aula_']                    = $_POST['data_aula'];
                $this->quadro_horario[$qtd_horario]['hora_inicial_']                 = $_POST['hora_inicial'];
                $this->quadro_horario[$qtd_horario]['hora_final_']                   = $_POST['hora_final'];
                $this->quadro_horario[$qtd_horario]['ativo_']                        = 1;
                $this->quadro_horario[$qtd_horario]['dia_semana_']                   = $_POST['dia_semana'];
                $this->quadro_horario[$qtd_horario]['qtd_horario_']                  = $qtd_horario;

                $qtd_horario++;

                /**
                 * salva os dados em uma tabela temporaria
                 * para realizar consulta na listagem
                 */
                $obj_quadro_horario = new clsPmieducarQuadroHorarioHorariosAux(
                    $this->ref_cod_quadro_horario,
                    null,
                    $this->ref_cod_disciplina,
                    $this->ref_cod_escola,
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_instituicao,
                    $this->ref_cod_servidor,
                    $this->dia_semana,
                    $this->hora_inicial,
                    $this->hora_final,
                    $this->identificador
                );

                $obj_quadro_horario->cadastra();

                unset($this->ref_cod_servidor);
                unset($this->ref_cod_disciplina);
                unset($this->hora_inicial);
                unset($this->hora_final);

                echo '
                <script>
                    window.onload = function() {
                        document.getElementById(\'ref_cod_servidor\').value   = \'\';
                        document.getElementById(\'ref_cod_disciplina\').value = \'\';
                        document.getElementById(\'data_aula\').value          = \'\';
                        document.getElementById(\'hora_inicial\').value       = \'\';
                        document.getElementById(\'hora_final\').value         = \'\';
                    }
                </script>';
            }
        }

        echo '<script>
                quadro_horario = ' . count($this->quadro_horario) . ';
            </script>';

        $this->campoOculto('excluir_horario', '');
        $qtd_horario = 1;

        $this->lst_matriculas = urldecode($this->lst_matriculas);

        $this->min_mat = $this->min_ves = $this->min_not = 0;

        if ($this->quadro_horario) {
            foreach ($this->quadro_horario as $campo) {
                if ($this->excluir_horario == $campo['qtd_horario_']) {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios();
                    $lst_horario = $obj_horario->lista(
                        $campo['ref_cod_quadro_horario_'],
                        $campo['ref_ref_cod_serie_'],
                        $campo['ref_ref_cod_escola_'],
                        $campo['ref_ref_cod_disciplina_'],
                        null,
                        null,
                        null,
                        $campo['ref_cod_instituicao_servidor_'],
                        null,
                        $campo['ref_servidor_'],
                        $campo['hora_inicial_'],
                        null,
                        $campo['hora_final_'],
                        null,
                        null,
                        null,
                        null,
                        null,
                        1,
                        $campo['dia_semana_']
                    );

                    if (is_array($lst_horario)) {
                        $campo['ativo_'] = 0;

                        if (isset($this->lst_matriculas)) {
                            $this->lst_matriculas .= '' . $campo['ref_servidor_'] . '';
                        } else {
                            $this->lst_matriculas .= ', ' . $campo['ref_servidor_'] . '';
                        }
                    } else {
                        $campo['ativo_'] = 2;

                        if (isset($this->lst_matriculas)) {
                            $this->lst_matriculas .= '' . $campo['ref_servidor_'] . '';
                        } else {
                            $this->lst_matriculas .= ', ' . $campo['ref_servidor_'] . '';
                        }
                    }

                    $this->excluir_horario = null;

                    $obj_horario = new clsPmieducarQuadroHorarioHorariosAux();
                    $lst_horario = $obj_horario->excluiRegistro(
                        $campo['ref_cod_quadro_horario_'],
                        $campo['ref_ref_cod_serie_'],
                        $campo['ref_ref_cod_escola_'],
                        $campo['ref_ref_cod_disciplina_'],
                        $campo['ref_cod_instituicao_servidor_'],
                        $campo['ref_servidor_'],
                        $this->identificador
                    );
                } else {
                    switch ($campo['dia_semana_']) {
                    case 1:
                        $campo['nm_dia_semana_'] = 'Domingo';
                        break;

                    case 2:
                        $campo['nm_dia_semana_'] = 'Segunda-Feira';
                        break;

                    case 3:
                        $campo['nm_dia_semana_'] = 'Terça-Feira';
                        break;

                    case 4:
                        $campo['nm_dia_semana_'] = 'Quarta-Feira';
                        break;

                    case 5:
                        $campo['nm_dia_semana_'] = 'Quinta-Feira';
                        break;

                    case 6:
                        $campo['nm_dia_semana_'] = 'Sexta-Feira';
                        break;

                    case 7:
                        $campo['nm_dia_semana_'] = 'S&aacute;bado';
                        break;
                    }
                }

                if ($campo['ativo_'] == 1) {
                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_nm_dia_semana',
                        '',
                        $campo['nm_dia_semana_'],
                        13,
                        255,
                        false,
                        false,
                        true
                    );

                    $this->campoOculto(
                        $campo['qtd_horario_'] . '_dia_semana',
                        $campo['dia_semana_']
                    );

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_data_aula',
                        '',
                        $campo['data_aula_'],
                        8,
                        255,
                        false,
                        false,
                        true
                    );

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_hora_inicial',
                        '',
                        $campo['hora_inicial_'],
                        5,
                        255,
                        false,
                        false,
                        true
                    );

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_hora_final',
                        '',
                        $campo['hora_final_'],
                        5,
                        255,
                        false,
                        false,
                        true
                    );

                    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
                    $componente = $componenteMapper->find($campo['ref_ref_cod_disciplina_']);

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_ref_cod_disciplina',
                        '',
                        $componente->nome,
                        30,
                        255,
                        false,
                        false,
                        true
                    );

                    $obj_pes = new clsPessoa_($campo['ref_servidor_']);
                    $det_pes = $obj_pes->detalhe();

                    if (is_numeric($campo['ref_servidor_substituto_'])) {
                        $this->campoTextoInv(
                            $campo['qtd_horario_'] . '_ref_cod_servidor',
                            '',
                            $det_pes['nome'],
                            30,
                            255,
                            false,
                            false,
                            false,
                            '',
                            ''
                        );
                    } else {
                        $this->campoTextoInv(
                            $campo['qtd_horario_'] . '_ref_cod_servidor',
                            '',
                            $det_pes['nome'],
                            30,
                            255,
                            false,
                            false,
                            false,
                            '',
                            "<a href='#' onclick=\"getElementById('excluir_horario').value = '{$campo['qtd_horario_']}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>"
                        );
                    }
                }

                if ($campo['ativo_'] != 2) {
                    $horarios_incluidos[$qtd_horario]['ref_cod_quadro_horario_']       = $campo['ref_cod_quadro_horario_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_serie_']            = $campo['ref_ref_cod_serie_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_escola_']           = $campo['ref_ref_cod_escola_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_disciplina_']       = $campo['ref_ref_cod_disciplina_'];
                    $horarios_incluidos[$qtd_horario]['sequencial_']                   = $campo['sequencial_'];
                    $horarios_incluidos[$qtd_horario]['ref_cod_instituicao_servidor_'] = $campo['ref_cod_instituicao_servidor_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_']                 = $campo['ref_servidor_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_substituto_']      = $campo['ref_servidor_substituto_'];
                    $horarios_incluidos[$qtd_horario]['data_aula_']                    = $campo['data_aula_'];
                    $horarios_incluidos[$qtd_horario]['hora_inicial_']                 = $campo['hora_inicial_'];
                    $horarios_incluidos[$qtd_horario]['hora_final_']                   = $campo['hora_final_'];
                    $horarios_incluidos[$qtd_horario]['ativo_']                        = $campo['ativo_'];
                    $horarios_incluidos[$qtd_horario]['dia_semana_']                   = $campo['dia_semana_'];
                    $horarios_incluidos[$qtd_horario]['qtd_horario_']                  = $qtd_horario;
                    $qtd_horario++;
                }
            }

            unset($this->quadro_horario);
            $this->quadro_horario = $horarios_incluidos;
        }

        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);
        $this->campoOculto('quadro_horario', serialize($this->quadro_horario));
        $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
        $this->campoOculto('ano_alocacao', $this->ano_alocacao);
        $this->campoOculto('lst_matriculas', urlencode($this->lst_matriculas));
        $this->campoOculto('min_mat', $this->min_mat);
        $this->campoOculto('min_ves', $this->min_ves);
        $this->campoOculto('min_not', $this->min_not);

        $this->campoQuebra();
    }

    public function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            'educar_quadro_horario_lst.php'
        );

        $this->quadro_horario = unserialize(urldecode($this->quadro_horario));

        $verifica = true;
        $parametros = '';
        if ($this->ref_cod_disciplina == 'todas_disciplinas') {
            $this->ref_cod_turma          = $_GET['ref_cod_turma'];
            $this->ref_ref_cod_serie      = $_GET['ref_cod_serie'];
            $this->ref_cod_curso          = $_GET['ref_cod_curso'];
            $this->ref_cod_escola         = $_GET['ref_cod_escola'];
            $this->ref_cod_instituicao    = $_GET['ref_cod_instituicao'];
            $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
            $this->dia_semana             = $_GET['dia_semana'];
            $this->identificador          = $_GET['identificador'];
            $this->ref_servidor           = $_POST['ref_cod_servidor'];
            $this->data_aula              = $_POST['data_aula'];
            $this->hora_inicial           = $_POST['hora_inicial'];
            $this->hora_final             = $_POST['hora_final'];

            $componentesTurma = [];
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $this->ref_cod_turma
                );
            } catch (Exception $e) {
            }

            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente->id;
            }

            foreach ($opcoes_disc as $displina) {
                $parametros = "?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $this->ref_cod_quadro_horario,
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $displina,
                    null,
                    null,
                    $this->ref_cod_instituicao,
                    null,
                    $this->ref_servidor,
                    $this->hora_inicial,
                    $this->hora_final,
                    null,
                    null,
                    1,
                    $this->dia_semana,
                    null,
                    $this->data_aula
                );

                $cadastrou = $obj_horario->cadastra();

                if ($cadastrou) {
                    if ($verifica) {
                        $verifica = true;
                    }
                } else {
                    $verifica = false;
                }
            }
        } else {
            foreach ($this->quadro_horario as $registro) {
                $parametros = "?ref_cod_instituicao={$registro['ref_cod_instituicao_servidor_']}&ref_cod_escola={$registro['ref_ref_cod_escola_']}&ref_cod_curso={$this->ref_cod_curso_}&ref_cod_serie={$registro['ref_ref_cod_serie_']}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $registro['ref_cod_quadro_horario_'],
                    $registro['ref_ref_cod_serie_'],
                    $registro['ref_ref_cod_escola_'],
                    $registro['ref_ref_cod_disciplina_'],
                    null,
                    null,
                    $registro['ref_cod_instituicao_servidor_'],
                    null,
                    $registro['ref_servidor_'],
                    $registro['hora_inicial_'],
                    $registro['hora_final_'],
                    null,
                    null,
                    1,
                    $registro['dia_semana_'],
                    null,
                    $registro['data_aula_']
                );

                $cadastrou = $obj_horario->cadastra();

                if ($cadastrou) {
                    if ($verifica) {
                        $verifica = true;
                    }
                } else {
                    $verifica = false;
                }
            }
        }

        if ($verifica) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header("Location: educar_quadro_horario_lst.php{$parametros}");
            die();
        }

        $this->mensagem = 'Cadastro não realizado. 1<br>';

        return false;
    }

    public function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            'educar_quadro_horario_lst.php'
        );

        $this->quadro_horario = unserialize(urldecode($this->quadro_horario));

        $verifica = true;
        $parametros = '';

        if ($this->ref_cod_disciplina == 'todas_disciplinas') {
            $this->ref_cod_turma          = $_GET['ref_cod_turma'];
            $this->ref_ref_cod_serie      = $_GET['ref_cod_serie'];
            $this->ref_cod_curso          = $_GET['ref_cod_curso'];
            $this->ref_cod_escola         = $_GET['ref_cod_escola'];
            $this->ref_cod_instituicao    = $_GET['ref_cod_instituicao'];
            $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
            $this->dia_semana             = $_GET['dia_semana'];
            $this->identificador          = $_GET['identificador'];
            $this->ref_servidor           = $_POST['ref_cod_servidor'];
            $this->data_aula              = $_POST['data_aula'];
            $this->hora_inicial           = $_POST['hora_inicial'];
            $this->hora_final             = $_POST['hora_final'];

            $componentesTurma = [];
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $this->ref_cod_turma
                );
            } catch (Exception $e) {
            }

            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente->id;
            }
            foreach ($opcoes_disc as $displina) {
                $parametros = "?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $this->ref_cod_quadro_horario,
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $displina,
                    null,
                    null,
                    $this->ref_cod_instituicao,
                    null,
                    $this->ref_servidor,
                    $this->hora_inicial,
                    $this->hora_final,
                    null,
                    null,
                    1,
                    $this->dia_semana,
                    null,
                    $this->data_aula
                );

                $cadastrou = $obj_horario->cadastra();

                if ($cadastrou) {
                    if ($verifica) {
                        $verifica = true;
                    }
                } else {
                    $verifica = false;
                }
            }
        } elseif (is_array($this->quadro_horario)) {
            foreach ($this->quadro_horario as $registro) {
                $parametros  = "?ref_cod_instituicao={$registro['ref_cod_instituicao_servidor_']}&ref_cod_escola={$registro['ref_ref_cod_escola_']}&ref_cod_curso={$this->ref_cod_curso_}&ref_cod_serie={$registro['ref_ref_cod_serie_']}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";
                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $registro['ref_cod_quadro_horario_'],
                    $registro['ref_ref_cod_serie_'],
                    $registro['ref_ref_cod_escola_'],
                    $registro['ref_ref_cod_disciplina_'],
                    $registro['sequencial_'],
                    null,
                    $registro['ref_cod_instituicao_servidor_'],
                    null,
                    $registro['ref_servidor_'],
                    null,
                    null,
                    null,
                    null,
                    $registro['ativo_'],
                    null,
                    null,
                    $registro['data_aula_']
                );

                if ($obj_horario->detalhe()) {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                        $registro['ref_cod_quadro_horario_'],
                        $registro['ref_ref_cod_serie_'],
                        $registro['ref_ref_cod_escola_'],
                        $registro['ref_ref_cod_disciplina_'],
                        $registro['sequencial_'],
                        null,
                        $registro['ref_cod_instituicao_servidor_'],
                        null,
                        $registro['ref_servidor_'],
                        $registro['hora_inicial_'],
                        $registro['hora_final_'],
                        null,
                        null,
                        $registro['ativo_'],
                        $registro['dia_semana_'],
                        null,
                        $registro['data_aula_']
                    );

                    $editou = $obj_horario->edita();

                    if ($editou) {
                        if ($verifica) {
                            $verifica = true;
                        }
                    } else {
                        $verifica = false;
                    }
                } else {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                        $registro['ref_cod_quadro_horario_'],
                        $registro['ref_ref_cod_serie_'],
                        $registro['ref_ref_cod_escola_'],
                        $registro['ref_ref_cod_disciplina_'],
                        null,
                        null,
                        $registro['ref_cod_instituicao_servidor_'],
                        null,
                        $registro['ref_servidor_'],
                        $registro['hora_inicial_'],
                        $registro['hora_final_'],
                        null,
                        null,
                        $registro['ativo_'],
                        $registro['dia_semana_'],
                        null,
                        $registro['data_aula_']
                    );

                    $cadastrou = $obj_horario->cadastra();

                    if ($cadastrou) {
                        if ($verifica) {
                            $verifica = true;
                        }
                    } else {
                        $verifica = false;
                    }
                }
            }
        }

        if ($verifica) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);

            $this->mensagem .= 'Cadastro editado com sucesso.<br>';
            header("Location: educar_quadro_horario_lst.php{$parametros}");
            die();
        }

        $this->mensagem = 'Cadastro não editado.<br>';

        return false;
    }

    public function Excluir()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            641,
            $this->pessoa_logada,
            7,
            'educar_calendario_dia_lst.php'
        );

        $obj = new clsPmieducarCalendarioDia(
            $this->ref_cod_calendario_ano_letivo,
            $this->mes,
            $this->dia,
            $this->pessoa_logada,
            $this->pessoa_logada,
            'NULL',
            'NULL',
            $this->data_cadastro,
            $this->data_exclusao,
            1
        );

        $excluiu = $obj->edita();

        if ($excluiu) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);

            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            header("Location: educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}");
            die();
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_servidor_lupa').onclick = function() {
  validaCampoServidor();
}

document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}

document.getElementById('ref_cod_serie').onchange = function() {
  getTurma();
}

document.getElementById('btn_enviar').onclick = function() {
  verificaHorario();
}

document.getElementById('ref_cod_disciplina').onchange = function() {
  document.getElementById('ref_cod_servidor').length = 1;
}

function validaCampoServidor()
{
  document.getElementById('ref_cod_servidor').length = 1;

  if (document.getElementById('dia_semana').value == '') {
    alert('Você deve escolher o dia da semana!');
    return;
  }
  else if (document.getElementById('hora_inicial').value == '')
  {
    alert('Você deve preencher o campo Hora Inicial!');
    return;
  }
  else if (document.getElementById('hora_final').value == '')
  {
    alert('Você deve preencher o campo Hora Final!');
    return;
  }
  else
  {
    var ref_cod_instituicao;
    var ref_cod_escola;
    var dia_semana;
    var hora_inicial;
    var hora_final;
    var lst_matriculas;
    var min_mat;
    var min_ves;
    var min_not;
    var identificador;
    var ref_cod_disciplina;
    var ref_cod_curso;
    var ano_alocacao;

    if (document.getElementById('ref_cod_instituicao').value) {
      ref_cod_instituicao = document.getElementById('ref_cod_instituicao').value;
    }

    if (document.getElementById('ref_cod_escola').value) {
      ref_cod_escola = document.getElementById('ref_cod_escola').value;
    }

    if (document.getElementById('dia_semana').value) {
      dia_semana = document.getElementById('dia_semana').value;
    }

    if (document.getElementById('hora_inicial').value) {
      hora_inicial = document.getElementById('hora_inicial').value;
    }

    if (document.getElementById('hora_final').value) {
      hora_final = document.getElementById('hora_final').value;
    }

    if (document.getElementById('lst_matriculas').value) {
      lst_matriculas = document.getElementById('lst_matriculas').value;
    }

    if (document.getElementById('min_mat').value) {
      min_mat = parseInt(document.getElementById('min_mat').value, 10);
    }

    if (document.getElementById('min_ves').value) {
      min_ves = parseInt(document.getElementById('min_ves').value, 10);
    }

    if (document.getElementById('min_not').value) {
      min_not = parseInt(document.getElementById('min_not').value, 10);
    }

    if (document.getElementById('identificador').value) {
      identificador = document.getElementById('identificador').value;
    }

    if (document.getElementById('ref_cod_disciplina').value) {
      ref_cod_disciplina = document.getElementById('ref_cod_disciplina').value;
    }

    if (document.getElementById('ref_cod_curso').value) {
      ref_cod_curso = document.getElementById('ref_cod_curso').value;
    }

    if (document.getElementById('ano_alocacao').value) {
      ano_alocacao = document.getElementById('ano_alocacao').value;
    }

    if (document.getElementById('hora_inicial').value && document.getElementById('hora_final').value) {
      var hr_ini;
      var hr_fim;

      hr_ini  = hora_inicial.split(':');
      hr_fim  = hora_final.split(':');

      hr_ini[0] = parseInt(hr_ini[0], 10);
      hr_ini[1] = parseInt(hr_ini[1], 10);
      hr_fim[0] = parseInt(hr_fim[0], 10);
      hr_fim[1] = parseInt(hr_fim[1], 10);

      min_ini = (hr_ini[0] * 60) + hr_ini[1];
      min_fim = (hr_fim[0] * 60) + hr_fim[1];

      if ( min_ini >= 480 && min_ini <= 720) {
        if (min_fim <= 720) {
          min_mat += min_fim - min_ini;
        }
        else if (min_fim >= 721 && min_fim <= 1080) {
          min_mat += 720 - min_ini;
          min_ves += min_fim - 720;
        }
        else if ((min_fim >= 1081 && min_fim <= 1439) || min_fim == 0) {
          min_mat += 720 - min_ini;
          min_ves += 360;

          if (min_fim >= 1081 && min_fim <= 1439) {
            min_not += min_fim - 1080;
          }
          else if (min_fim = 0) {
            min_not += 360;
          }
        }
      }
      else if (min_ini >= 721 && min_ini <= 1080) {
        if (min_fim <= 1080) {
          min_ves += min_fim - min_ini;
        }
        else if ((min_fim >= 1081 && min_fim <= 1439) || min_fim == 0) {
          min_ves += 1080 - min_ini;

          if (min_fim >= 1081 && min_fim <= 1439) {
            min_not += min_fim - 1080;
          }
          else if (min_fim = 0) {
            min_not += 360;
          }
        }
      }
      else if ((min_ini >= 1081 && min_ini <= 1439) || min_ini == 0) {
        if (min_fim <= 1439) {
          min_not += min_fim - min_ini;
        }
        else if (min_fim == 0) {
          min_not += 1440 - min_ini;
        }
      }
    }

    if (verificaQuadroHorario()) {
      if (document.getElementById('lst_matriculas').value) {
        pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor&matricula=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola + '&dia_semana=' + dia_semana + '&hora_inicial=' + hora_inicial + '&hora_final=' + hora_final + '&horario=S' + '&lst_matriculas=' + lst_matriculas + '&min_mat=' + min_mat + '&min_ves=' + min_ves + '&min_not=' + min_not + '&identificador=' + identificador + '&ref_cod_disciplina=' + ref_cod_disciplina + '&ref_cod_curso=' + ref_cod_curso + '&ano_alocacao=' + ano_alocacao, 'ref_cod_servidor');
      }
      else {
        pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor&matricula=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola + '&dia_semana=' + dia_semana + '&hora_inicial=' + hora_inicial + '&hora_final=' + hora_final + '&horario=S' + '&min_mat=' + min_mat + '&min_ves=' + min_ves + '&min_not=' + min_not + '&identificador=' + identificador + '&ref_cod_disciplina=' + ref_cod_disciplina + '&ref_cod_curso=' + ref_cod_curso+ '&ano_alocacao=' + ano_alocacao, 'ref_cod_servidor');
      }
    }
  }
}

function verificaQuadroHorario()
{
  var aux      = '';
  var cont     = 1;
  var hora_ini = document.getElementById('hora_inicial').value.substring(0, 2);
  var min_ini  = document.getElementById('hora_inicial').value.substring(3);
  var hora_fim = document.getElementById('hora_final').value.substring(0, 2);
  var min_fim  = document.getElementById('hora_final').value.substring(3);

  hora_ini = parseInt(hora_ini, 10) + (parseFloat(min_ini)  / 60);
  hora_fim = parseInt(hora_fim, 10) + (parseFloat(min_fim) / 60);

  if (hora_ini >= hora_fim) {
    alert('O horário de início deve ser menor que o horário final');
    return false;
  }

  do {
    if (document.getElementById( cont + '_dia_semana')) {
      if (document.getElementById(cont + '_data_aula').value == document.getElementById('data_aula').value) {
        if ((document.getElementById('hora_inicial').value < document.getElementById(cont + '_hora_inicial').value
          && document.getElementById('hora_final').value < document.getElementById(cont + '_hora_inicial').value)
          || (document.getElementById('hora_inicial').value >= document.getElementById(cont + '_hora_final').value
          && document.getElementById('hora_final').value > document.getElementById(cont + '_hora_final').value))
        {
        }
        else {
          alert( 'O horário escolhido coincide com um horário já existente!' );
          return false;
        }
      }

      cont++;
    }
    else {
      aux = 'sair';
      return true;
    }
  } while (aux == '');
}

function verificaHorario()
{
  if (parseInt(quadro_horario, 10) == 0 && !($j('#ref_cod_disciplina').val() == 'todas_disciplinas')) {
    alert('Você deve incluir pelo menos um horário');
    return false;
  }else if ($j('#ref_cod_disciplina').val() == 'todas_disciplinas'){
    if (document.getElementById('ref_cod_disciplina').value == '') {
     alert('Você deve escolher a disciplina!');
     return;
   }
   else if (document.getElementById('hora_inicial').value == '') {
     alert('Você deve preencher o campo Hora Inicial!');
     return;
   }
   else if (document.getElementById('hora_final').value == '') {
     alert('Você deve preencher o campo Hora Final!');
     return;
   }
   else if (document.getElementById('ref_cod_servidor').value == '') {
     alert('Você deve selecionar um servidor no campo Servidor');
     return;
   }
  }
  acao();
  return true;
}
$j('#ref_cod_disciplina').change(todas_disciplinas);

function todas_disciplinas(){
  if($j('#ref_cod_disciplina').val() == 'todas_disciplinas'){
    $j("#btn_incluir_horario").closest('tr').hide();
  }else{
    $j("#btn_incluir_horario").closest('tr').show();
  }
}

$j('#btn_incluir_horario').click(addHorario);

function addHorario(){
  if (document.getElementById('ref_cod_disciplina').value == '') {
     alert('Você deve escolher a disciplina!');
     return;
   }
   else if (document.getElementById('hora_inicial').value == '') {
     alert('Você deve preencher o campo Hora Inicial!');
     return;
   }
   else if (document.getElementById('hora_final').value == '') {
     alert('Você deve preencher o campo Hora Final!');
     return;
   }
   else if (document.getElementById('ref_cod_servidor').value == '') {
     alert('Você deve selecionar um servidor no campo Servidor');
     return;
   }
   else {
     if (verificaQuadroHorario()) {
       $j('#incluir_horario').val('S');
       $j('#tipoacao').val('');
       formcadastro.submit();
     }
   }
}
</script>
