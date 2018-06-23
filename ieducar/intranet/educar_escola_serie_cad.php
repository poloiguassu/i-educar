<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'Avaliacao/Fixups/CleanComponentesCurriculares.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escola Eixo');
    $this->processoAp = 585;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @todo      Ver a quest�o de formul�rios que tem campos dinamicamente
 *   desabilitados de acordo com a requisi��o (GET, POST ou erro de valida��o).
 *   A forma atual de usar valores em campos hidden leva a diversos problemas
 *   como aumento da l�gica de pr�-valida��o nos m�todos Novo() e Editar().
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
    var $pessoa_logada;
    var $ref_cod_escola;
    var $ref_cod_escola_;
    var $ref_cod_serie;
    var $ref_cod_serie_;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $hora_inicial;
    var $hora_final;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $hora_inicio_intervalo;
    var $hora_fim_intervalo;
    var $hora_fim_intervalo_;
    var $ref_cod_instituicao;
    var $ref_cod_curso;
    var $escola_serie_disciplina;
    var $ref_cod_disciplina;
    var $incluir_disciplina;
    var $excluir_disciplina;
    var $disciplinas;
    var $carga_horaria;
    var $etapas_especificas;
    var $etapas_utilizadas;
    var $definirComponentePorEtapa;

    function Inicializar()
    {
        $retorno = 'Novo';

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

    $this->url_cancelar = ($retorno == 'Editar') ?
      sprintf('educar_escola_serie_det.php?ref_cod_escola=%d&ref_cod_serie=%d',
        $registro['ref_cod_escola'], $registro['ref_cod_serie']) :
      'educar_escola_serie_lst.php';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "{$nomeMenu} v&iacute;nculo entre escola e s&eacute;rie"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach($_POST as $campo => $val) {
        $this->$campo = ($this->$campo) ? $this->$campo : $val;
      }
    }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(585, $this->pessoa_logada, 7, 'educar_escola_serie_lst.php');

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $tmp_obj = new clsPmieducarEscolaSerie();
            $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
            $registro = array_shift($lst_obj);

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(585, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }

    $this->campoLista('ref_cod_serie', 'S�rie', $opcoes_serie, $this->ref_cod_serie,
      '', FALSE, '', '', $this->ref_cod_serie ? TRUE : FALSE);

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME'] . "/intranet" => "In&iacute;cio",
                "educar_index.php" => "Escola",
                "" => "{$nomeMenu} v&iacute;nculo entre escola e s&eacute;rie"
            )
        );

        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = 'Cancelar';
        return $retorno;
    }

		$this->campoCheck("bloquear_enturmacao_sem_vagas", "Bloquear enturma��o ap�s atingir limite de vagas", $this->bloquear_enturmacao_sem_vagas);

        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $curso_desabilitado = true;
            $serie_desabilitado = true;
            $escola_serie_desabilitado = true;

            $this->campoOculto('ref_cod_instituicao_', $this->ref_cod_instituicao);
            $this->campoOculto('ref_cod_escola_', $this->ref_cod_escola);
            $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
            $this->campoOculto('ref_cod_serie_', $this->ref_cod_serie);
        }

        $obrigatorio = true;
        $get_escola = true;
        $get_curso = true;
        $get_serie = false;
        $get_escola_serie = true;

        include 'include/pmieducar/educar_campo_lista.php';

        if ($this->ref_cod_escola_) {
            $this->ref_cod_escola = $this->ref_cod_escola_;
        }

    $opcoes = array('' => 'Selecione');

    // Editar
    $disciplinas = 'Nenhuma s�rie selecionada';

    if ($this->ref_cod_serie) {
      $disciplinas = '';
      $conteudo = '';

      // Instancia o mapper de ano escolar
      $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
      $lista = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

      // Instancia o mapper de componente curricular
      $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

      if (is_array($lista) && count($lista)) {
        $conteudo .= '<div style="margin-bottom: 10px; float: left">';
        $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
        $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga hor�ria</span>';
        $conteudo .= '  <span style="display: block; float: left">Usar padr�o do componente?</span>';
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';
        $conteudo .= '<div style="margin-bottom: 10px; float: left">';
        $conteudo .= "  <label style='display: block; float: left; width: 350px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck(".'"disciplinas[]"'.");'/>Marcar Todos</label>";
        $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='CheckTodos2' onClick='marcarCheck(".'"usar_componente[]"'.");';/>Marcar Todos</label>";
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';         

        foreach ($lista as $registro) {
          $checked = '';
          $usarComponente = FALSE;

          if ($this->escola_serie_disciplina[$registro->id] == $registro->id) {
            $checked = 'checked="checked"';
          }

          if (is_null($this->escola_serie_disciplina_carga[$registro->id]) ||
            0 == $this->escola_serie_disciplina_carga[$registro->id]) {
            $usarComponente = TRUE;
          }
          else {
            $cargaHoraria = $this->escola_serie_disciplina_carga[$registro->id];
          }

          $cargaComponente = $registro->cargaHoraria;

          $conteudo .= '<div style="margin-bottom: 10px; float: left">';
          $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
          $conteudo .= "  <label style='display: block; float: left'><input type='checkbox' id='usar_componente[]' name='usar_componente[$registro->id]' value='1' ". ($usarComponente == TRUE ? $checked : '') .">($cargaComponente h)</label>";

          $conteudo .= '</div>';
          $conteudo .= '<br style="clear: left" />';

          $cargaHoraria = '';
        }

        $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
        $disciplinas .= '</table>';
      }
      else {
        $disciplinas = 'A s�rie/ano escolar n�o possui componentes curriculares cadastrados.';
      }
    }

    $this->campoRotulo("disciplinas_", "Componentes curriculares",
      "<div id='disciplinas'>$disciplinas</div>");

    $this->campoQuebra();
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    /*
     * Se houve erro na primeira tentativa de cadastro, ir� considerar apenas
     * os valores enviados de forma oculta.
     */
    if (isset($this->ref_cod_instituicao_)) {
      $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
      $this->ref_cod_escola      = $this->ref_cod_escola_;
      $this->ref_cod_curso       = $this->ref_cod_curso_;
      $this->ref_cod_serie       = $this->ref_cod_serie_;
    }

    $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
    $componenteAno = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

    /*
     * Se $disciplinas n�o for informado e o ano escolar tem componentes
     * curriculares cadastrados, retorna erro.
     */
    if (!is_array($this->disciplinas) &&
        (is_array($componenteAno) && 0 < count($componenteAno))
    ) {
      echo "<script> alert('� necess�rio adicionar pelo menos um componente curricular.') </script>";
      $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
      return FALSE;
    }

        $this->campoLista(
            'ref_cod_serie',
            'Série',
            $opcoes_serie,
            $this->ref_cod_serie,
            '',
            false,
            '',
            '',
            $this->ref_cod_serie ? true : false
        );

        $this->hora_inicial = substr($this->hora_inicial, 0, 5);
        $this->hora_final = substr($this->hora_final, 0, 5);
        $this->hora_inicio_intervalo = substr($this->hora_inicio_intervalo, 0, 5);
        $this->hora_fim_intervalo = substr($this->hora_fim_intervalo, 0, 5);

        // hora
        $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, false);
        $this->campoHora('hora_final', 'Hora Final', $this->hora_final, false);
        $this->campoHora('hora_inicio_intervalo', 'Hora In&iacute;cio Intervalo', $this->hora_inicio_intervalo, false);
        $this->campoHora('hora_fim_intervalo', 'Hora Fim Intervalo', $this->hora_fim_intervalo, false);
        $this->campoCheck("bloquear_enturmacao_sem_vagas", "Bloquear enturmação após atingir limite de vagas", $this->bloquear_enturmacao_sem_vagas);
        $this->campoCheck("bloquear_cadastro_turma_para_serie_com_vagas", "Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)", $this->bloquear_cadastro_turma_para_serie_com_vagas);
        $this->campoQuebra();

        // Inclui disciplinas
        if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
            $obj = new clsPmieducarEscolaSerieDisciplina();
            $registros = $obj->lista($this->ref_cod_serie, $this->ref_cod_escola, null, 1);

            if ($registros) {
                foreach ($registros as $campo) {
                    $this->escola_serie_disciplina[$campo['ref_cod_disciplina']] = $campo['ref_cod_disciplina'];
                    $this->escola_serie_disciplina_carga[$campo['ref_cod_disciplina']] = floatval($campo['carga_horaria']);

                    if ($this->definirComponentePorEtapa) {
                        $this->escola_serie_disciplina_etapa_especifica[$campo['ref_cod_disciplina']] = intval($campo['etapas_especificas']);
                        $this->escola_serie_disciplina_etapa_utilizada[$campo['ref_cod_disciplina']] = $campo['etapas_utilizadas'];
                    }
                }
            }
        }

        $opcoes = array('' => 'Selecione');

        // Editar
        $disciplinas = 'Nenhuma série selecionada';

        if ($this->ref_cod_serie) {
            $disciplinas = '';
            $conteudo = '';

            // Instancia o mapper de ano escolar
            $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
            $lista = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

            if (is_array($lista) && count($lista)) {
                $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Nome abreviado</span>';
                $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga horária</span>';
                $conteudo .= '  <span style="display: block; float: left">Usar padrão do componente?</span>';

                if ($this->definirComponentePorEtapa) {
                    $conteudo .= '  <span style="display: block; float: left; margin-left: 30px;">Usado em etapas específicas?(Exemplo: 1,2 / 1,3)</span>';
                }

                $conteudo .= '</div>';
                $conteudo .= '<br style="clear: left" />';
                $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                $conteudo .= "  <label style='display: block; float: left; width: 350px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck(" . '"disciplinas[]"' . ");'/>Marcar Todos</label>";
                $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='CheckTodos2' onClick='marcarCheck(" . '"usar_componente[]"' . ");';/>Marcar Todos</label>";

                if ($this->definirComponentePorEtapa) {
                    $conteudo .= "  <label style='display: block; float: left; width: 100px; margin-left: 84px;'><input type='checkbox' name='CheckTodos3' onClick='marcarCheck(" . '"etapas_especificas[]"' . ");';/>Marcar Todos</label>";
                }

                $conteudo .= '</div>';
                $conteudo .= '<br style="clear: left" />';

                foreach ($lista as $registro) {
                    $checked = '';
                    $checkedEtapaEspecifica = '';
                    $usarComponente = false;

                    if ($this->escola_serie_disciplina[$registro->id] == $registro->id) {
                        $checked = 'checked="checked"';

                        if ($this->escola_serie_disciplina_etapa_especifica[$registro->id] == "1") {
                            $checkedEtapaEspecifica = 'checked="checked"';
                        }
                    }

                    if (is_null($this->escola_serie_disciplina_carga[$registro->id]) || 0 == $this->escola_serie_disciplina_carga[$registro->id]) {
                        $usarComponente = true;
                    } else {
                        $cargaHoraria = $this->escola_serie_disciplina_carga[$registro->id];
                    }

                    $cargaComponente = $registro->cargaHoraria;
                    $etapas_utilizadas = $this->escola_serie_disciplina_etapa_utilizada[$registro->id];

                    $conteudo .= '<div style="margin-bottom: 10px; float: left">';
                    $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
                    $conteudo .= "  <span style='display: block; float: left; width: 100px'>{$registro->abreviatura}</span>";
                    $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
                    $conteudo .= "  <label style='display: block; float: left'><input type='checkbox' id='usar_componente[]' name='usar_componente[$registro->id]' value='1' " . ($usarComponente == true ? $checked : '') . ">($cargaComponente h)</label>";

                    if ($this->definirComponentePorEtapa) {
                        $conteudo .= "  <input style='margin-left:140px; float:left;' type='checkbox' id='etapas_especificas[]' name='etapas_especificas[$registro->id]' value='1' " . ($usarComponente == true ? $checkedEtapaEspecifica : '') . "></label>";
                        $conteudo .= "  <label style='display: block; float: left; width: 100px;'>Etapas utilizadas: <input type='text' class='etapas_utilizadas' name='etapas_utilizadas[$registro->id]' value='{$etapas_utilizadas}' size='5' maxlength='7'></label>";
                    }

                    $conteudo .= '</div>';
                    $conteudo .= '<br style="clear: left" />';

                    $cargaHoraria = '';
                }

                $disciplinas = '<table cellspacing="0" cellpadding="0" border="0">';
                $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
                $disciplinas .= '</table>';
            } else {
                $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
            }
        }

        $this->campoRotulo("disciplinas_", "Componentes curriculares", "<div id='disciplinas'>$disciplinas</div>");
        $this->campoQuebra();
    }

    function Novo()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        /*
         * Se houve erro na primeira tentativa de cadastro, irá considerar apenas
         * os valores enviados de forma oculta.
         */
        if (isset($this->ref_cod_instituicao_)) {
            $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
            $this->ref_cod_escola = $this->ref_cod_escola_;
            $this->ref_cod_curso = $this->ref_cod_curso_;
            $this->ref_cod_serie = $this->ref_cod_serie_;
        }

      $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
      header('Location: educar_escola_serie_lst.php');
      die();
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
    echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerie\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->pessoa_logada ) && ( $this->hora_inicial ) && ( $this->hora_final ) && ( $this->hora_inicio_intervalo ) && ( $this->hora_fim_intervalo )\n-->";
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    /*
     * Atribui valor para atributos usados em Gerar(), sen�o o formul�rio volta
     * a liberar os campos Institui��o, Escola e Projeto que devem ser read-only
     * quando em modo de edi��o
     */
    $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
    $this->ref_cod_escola      = $this->ref_cod_escola_;
    $this->ref_cod_curso       = $this->ref_cod_curso_;
    $this->ref_cod_serie       = $this->ref_cod_serie_;

    $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
    $componenteAno = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

    /**
     * @see indice#Novo();
     */
    if (!is_array($this->disciplinas) &&
        (is_array($componenteAno) && 0 < count($componenteAno))
    ) {
      echo "<script>alert('� necess�rio adicionar pelo menos um componente curricular.');</script>";
      $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
      return FALSE;
    }

            $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
            $auditoria->inclusao($obj->detalhe());
        }

        if ($cadastrou) {
            if ($this->disciplinas) {
                foreach ($this->disciplinas as $key => $campo) {
                    $obj = new clsPmieducarEscolaSerieDisciplina(
                        $this->ref_cod_serie,
                        $this->ref_cod_escola,
                        $campo,
                        1,
                        $this->carga_horaria[$key],
                        $this->etapas_especificas[$key],
                        $this->etapas_utilizadas[$key]
                    );

                    if ($obj->existe()) {
                        $cadastrou1 = $obj->edita();
                    } else {
                        $cadastrou1 = $obj->cadastra();
                    }

                    if (!$cadastrou1) {
                        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
                        echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
                        return false;
                    }
                }
            }

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            header('Location: educar_escola_serie_lst.php');
            die();
        }

        $this->mensagem = 'Cadastro n&atilde;o rrealizado.<br>';
        echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerie\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->pessoa_logada ) && ( $this->hora_inicial ) && ( $this->hora_final ) && ( $this->hora_inicio_intervalo ) && ( $this->hora_fim_intervalo )\n-->";
        return false;
    }

    function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        /*
         * Atribui valor para atributos usados em Gerar(), senão o formulário volta
         * a liberar os campos Instituição, Escola e Curso que devem ser read-only
         * quando em modo de edição
         */
        $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
        $this->ref_cod_escola = $this->ref_cod_escola_;
        $this->ref_cod_curso = $this->ref_cod_curso_;
        $this->ref_cod_serie = $this->ref_cod_serie_;

        $this->bloquear_enturmacao_sem_vagas = is_null($this->bloquear_enturmacao_sem_vagas) ? 0 : 1;
        $this->bloquear_cadastro_turma_para_serie_com_vagas = is_null($this->bloquear_cadastro_turma_para_serie_com_vagas) ? 0 : 1;

        $obj = new clsPmieducarEscolaSerie(
            $this->ref_cod_escola,
            $this->ref_cod_serie,
            $this->pessoa_logada,
            null,
            $this->hora_inicial,
            $this->hora_final,
            null,
            null,
            1,
            $this->hora_inicio_intervalo,
            $this->hora_fim_intervalo,
            $this->bloquear_enturmacao_sem_vagas,
            $this->bloquear_cadastro_turma_para_serie_com_vagas
        );

        $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();

        $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
        $auditoria->alteracao($detalheAntigo, $obj->detalhe());

        $obj = new clsPmieducarEscolaSerieDisciplina(
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $campo,
            1
        );

        $obj->excluirNaoSelecionados($this->disciplinas);


        if ($editou) {
            if ($this->disciplinas) {
                foreach ($this->disciplinas as $key => $campo) {
                    if (isset($this->usar_componente[$key])) {
                        $carga_horaria = null;
                    } else {
                        $carga_horaria = $this->carga_horaria[$key];
                    }

                    $etapas_especificas = $this->etapas_especificas[$key];
                    $etapas_utilizadas = $this->etapas_utilizadas[$key];

                    $obj = new clsPmieducarEscolaSerieDisciplina(
                        $this->ref_cod_serie,
                        $this->ref_cod_escola,
                        $campo,
                        1,
                        $carga_horaria,
                        $etapas_especificas,
                        $etapas_utilizadas
                    );

                    $existe = $obj->existe();

                    if ($existe) {
                        $editou1 = $obj->edita();

                        if (!$editou1) {
                            $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
                            echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
                            return false;
                        }
                    } else {
                        $cadastrou = $obj->cadastra();

                        if (!$cadastrou) {
                            $this->mensagem = 'Cadastro n&atilde;o realizada.<br>';
                            echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
                            return false;
                        }
                    }
                }

                //Verifica/limpa disciplinas não alteradas quando a escola/série for editada e tiver disciplinas marcadas
                //não padrão do ano letivo.
                $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
                $existe_ano_andamento = $obj_ano_letivo->lista($this->ref_cod_escola, null, null, null, 1, null, null, null, null, 1);

                foreach ($existe_ano_andamento as $reg) {
                    CleanComponentesCurriculares::destroyOldResources($reg['ano']);
                }
            }

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            header('Location: educar_escola_serie_lst.php');
            die();
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
        return false;
    }

    function Excluir()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj = new clsPmieducarEscolaSerie(
            $this->ref_cod_escola_,
            $this->ref_cod_serie_,
            $this->pessoa_logada,
            null,
            null,
            null,
            null,
            null,
            0
        );

        $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        $auditoria = new clsModulesAuditoriaGeral("escola_serie", $this->pessoa_logada);
        $auditoria->exclusao($detalhe);

        if ($excluiu) {
            $obj = new clsPmieducarEscolaSerieDisciplina($this->ref_cod_serie_, $this->ref_cod_escola_, null, 0);
            $excluiu1 = $obj->excluirTodos();

            if ($excluiu1) {
                $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
                header("Location: educar_escola_serie_lst.php");
                die();
            }
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarEscolaSerie\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_escola_ ) && is_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    private function _checkPermitirDefinirComponentesEtapa()
    {
        if (isset($this->ref_cod_serie)) {
            $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
            $det_serie = $obj_serie->detalhe();
            $regra_avaliacao_id = $det_serie["regra_avaliacao_id"];

            if (isset($regra_avaliacao_id)) {
                $regra_avaliacao_mapper = new RegraAvaliacao_Model_RegraDataMapper();
                $regra_avaliacao = $regra_avaliacao_mapper->find($regra_avaliacao_id);
            }
        }

        return ($regra_avaliacao->definirComponentePorEtapa == 1);
    }

    public function __construct()
    {
        parent::__construct();
        $this->loadAssets();
    }

    public function loadAssets()
    {
        $scripts = array(
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/EscolaSerie.js'
        );

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �� p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_instituicao').onchange = function()
{
  getDuploEscolaCurso();
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getSerie();
  var campoDisciplinas = document.getElementById('disciplinas');
  campoDisciplinas.innerHTML = "Nenhuma s�rie selecionada";
}

function getDisciplina(xml_disciplina)
{
  var campoDisciplinas = document.getElementById('disciplinas');
  var DOM_array = xml_disciplina.getElementsByTagName( "disciplina" );
  var conteudo = '';

  if (DOM_array.length) {
    conteudo += '<div style="margin-bottom: 10px; float: left">';
    conteudo += '  <span style="display: block; float: left; width: 250px;">Nome</span>';
    conteudo += '  <label span="display: block; float: left; width: 100px">Carga hor�ria</span>';
    conteudo += '  <label span="display: block; float: left">Usar padr�o do componente?</span>';
    conteudo += '</div>';
    conteudo += '<br style="clear: left" />';
    conteudo += '<div style="margin-bottom: 10px; float: left">';
    conteudo += "  <label style='display: block; float: left; width: 350px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck("+'"disciplinas[]"'+");'/>Marcar Todos</label>";
    conteudo += "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='CheckTodos2' onClick='marcarCheck("+'"usar_componente[]"'+");';/>Marcar Todos</label>";
    conteudo += '</div>';
    conteudo += '<br style="clear: left" />';    

    for (var i = 0; i < DOM_array.length; i++) {
      id = DOM_array[i].getAttribute("cod_disciplina");

      conteudo += '<div style="margin-bottom: 10px; float: left">';
      conteudo += '  <label style="display: block; float: left; width: 250px;"><input type="checkbox" name="disciplinas['+ id +']" id="disciplinas[]" value="'+ id +'">'+ DOM_array[i].firstChild.data +'</label>';
      conteudo += '  <label style="display: block; float: left; width: 100px;"><input type="text" name="carga_horaria['+ id +']" value="" size="5" maxlength="7"></label>';
      conteudo += '  <label style="display: block; float: left"><input type="checkbox" id="usar_componente[]" name="usar_componente['+ id +']" value="1">('+ DOM_array[i].getAttribute("carga_horaria") +' h)</label>';    
      conteudo += '</div>';
      conteudo += '<br style="clear: left" />';
    }
  }
  else {
    campoDisciplinas.innerHTML = 'A s�rie/ano escolar n�o possui componentes '
                               + 'curriculares cadastrados.';
  }

  if (conteudo) {
    campoDisciplinas.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
    campoDisciplinas.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
    campoDisciplinas.innerHTML += '</table>';
  }
}

document.getElementById('ref_cod_serie').onchange = function()
{
  var campoSerie = document.getElementById('ref_cod_serie').value;

  var campoDisciplinas = document.getElementById('disciplinas');
  campoDisciplinas.innerHTML = "Carregando disciplina";

  var xml_disciplina = new ajax( getDisciplina );
  xml_disciplina.envia("educar_disciplina_xml.php?ser=" + campoSerie);
}

after_getEscola = function()
{
  getEscolaCurso();
  getSerie();

  var campoDisciplinas = document.getElementById('disciplinas');
  campoDisciplinas.innerHTML = "Nenhuma s�rie selecionada";
};

function getSerie()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;
  if (document.getElementById('ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_cod_escola').value;
  }
  else if (document.getElementById('ref_ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_ref_cod_escola').value;
  }

  var campoSerie  = document.getElementById('ref_cod_serie');

  campoSerie.length = 1;

  limpaCampos(4);
  if (campoEscola && campoCurso) {
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando s�ries';

    var xml = new ajax(atualizaLstSerie);
    xml.envia("educar_serie_not_escola_xml.php?esc="+campoEscola+"&cur="+campoCurso);
  }
  else {
    campoSerie.options[0].text = 'Selecione';
  }
}

function atualizaLstSerie(xml)
{
  var campoSerie = document.getElementById('ref_cod_serie');
  campoSerie.length = 1;
  campoSerie.options[0].text = 'Selecione uma s�rie';
  campoSerie.disabled = false;

  series = xml.getElementsByTagName('serie');
  if (series.length) {
    for(var i = 0; i < series.length; i++) {
      campoSerie.options[campoSerie.options.length] = new Option(series[i].firstChild.data,
        series[i].getAttribute('cod_serie'), false, false);
    }
  }
  else {
    campoSerie.options[0].text = 'O curso n�o possui nenhuma s�rie ou todas as s�ries j� est� associadas a essa escola';
    campoSerie.disabled = true;
  }
}

function marcarCheck(idValue) {
    // testar com formcadastro
    var contaForm = document.formcadastro.elements.length;
    var campo = document.formcadastro;
    var i;
    if (idValue == 'disciplinas[]'){
      for (i=0; i<contaForm; i++) {
          if (campo.elements[i].id == idValue) {

              campo.elements[i].checked = campo.CheckTodos.checked;
          }
      }
    }else {
      for (i=0; i<contaForm; i++) {
          if (campo.elements[i].id == idValue) {

              campo.elements[i].checked = campo.CheckTodos2.checked;
           }
       }

    }
     
} 
</script>
