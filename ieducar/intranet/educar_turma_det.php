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
 * @author    Adriano Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'App/Model/IedFinder.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/CustomLabel.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Trilha Jovem - Turma');
    $this->processoAp = 586;
  }
}

/**
 * indice class.
 *
 * @author    Adriano Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_ref_cod_serie;
  var $ref_ref_cod_escola;
  var $ref_cod_infra_predio_comodo;
  var $nm_turma;
  var $sgl_turma;
  var $max_aluno;
  var $multiseriada;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_turma_tipo;
  var $hora_inicial;
  var $hora_final;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;

  var $ref_cod_instituicao;
  var $ref_cod_curso;

  var $ref_cod_instituicao_regente;
  var $ref_cod_regente;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Turma - Detalhe';
    $this->addBanner(
      'imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet'
    );

    $this->cod_turma = $_GET['cod_turma'];

    $dias_da_semana = array(
      '' => 'Selecione',
      1  => 'Domingo',
      2  => 'Segunda',
      3  => 'Terça',
      4  => 'Quarta',
      5  => 'Quinta',
      6  => 'Sexta',
      7  => 'Sábado'
    );

    $tmp_obj = new clsPmieducarTurma();
    $lst_obj = $tmp_obj->lista($this->cod_turma, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, array('true', 'false'));

    $registro = array_shift($lst_obj);

    foreach ($registro as $key => $value) {
      $this->$key = $value;
    }

    if (! $registro) {
      header('Location: educar_turma_lst.php');
      die();
    }

    if (class_exists('clsPmieducarTurmaTipo'))
    {
      $obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo(
        $registro['ref_cod_turma_tipo']
      );

      $det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
      $registro['ref_cod_turma_tipo'] = $det_ref_cod_turma_tipo['nm_tipo'];
    }
    else {
      $registro['ref_cod_turma_tipo'] = 'Erro na geração';
    }

    if (class_exists('clsPmieducarInfraPredioComodo')) {
      $obj_ref_cod_infra_predio_comodo = new clsPmieducarInfraPredioComodo(
        $registro['ref_cod_infra_predio_comodo']
      );

      $det_ref_cod_infra_predio_comodo = $obj_ref_cod_infra_predio_comodo->detalhe();
      $registro['ref_cod_infra_predio_comodo'] = $det_ref_cod_infra_predio_comodo['nm_comodo'];
    }
    else {
      $registro['ref_cod_infra_predio_comodo'] = 'Erro na geração';
    }

    if (class_exists('clsPmieducarInstituicao')) {
      $obj_cod_instituicao = new clsPmieducarInstituicao(
        $registro['ref_cod_instituicao']
      );

      $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
      $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];
    }
    else {
      $registro['ref_cod_instituicao'] = 'Erro na geração';
    }

    if (class_exists('clsPmieducarEscola')) {
      $this->ref_ref_cod_escola = $registro['ref_ref_cod_escola'];
      $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
      $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
      $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];
    }
    else {
      $registro['ref_cod_escola'] = 'Erro na geração';
    }

    if (class_exists('clsPmieducarCurso')) {
      $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
      $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
      $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
      $padrao_ano_escolar = $det_ref_cod_curso['padrao_ano_escolar'];
    }
    else {
      $registro['ref_cod_curso'] = 'Erro na geração';
    }

    if (class_exists('clsPmieducarSerie')) {
      $this->ref_ref_cod_serie = $registro['ref_ref_cod_serie'];
      $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
      $det_ser = $obj_ser->detalhe();
      $registro['ref_ref_cod_serie'] = $det_ser['nm_serie'];
    }
    else {
      $registro['ref_ref_cod_serie'] = 'Erro na geração';
    }

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      if ($registro['ref_cod_instituicao']) {
        $this->addDetalhe(array('Institui��o Executora', $registro['ref_cod_instituicao']));
      }
    }

    if ($nivel_usuario == 1 || $nivel_usuario == 2) {
      if ($registro['ref_ref_cod_escola']) {
        $this->addDetalhe(array('Institui��o', $registro['ref_ref_cod_escola']));
      }
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Projeto', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('Eixo', $registro['ref_ref_cod_serie']));
    }

    if ($registro['ref_cod_regente']) {
      $obj_pessoa = new clsPessoa_($registro['ref_cod_regente']);
      $det = $obj_pessoa->detalhe();

      $this->addDetalhe(array('Educador', $det['nome']));
    }

    if ($registro['ref_cod_infra_predio_comodo']) {
      $this->addDetalhe(array('Sala', $registro['ref_cod_infra_predio_comodo']));
    }

    if ($registro['ref_cod_turma_tipo']) {
      $this->addDetalhe(array('Tipo de Turma', $registro['ref_cod_turma_tipo']));
    }

    if ($registro['nm_turma']) {
      $this->addDetalhe(array('Turma', $registro['nm_turma']));
    }

    if ($registro['sgl_turma']) {
      $this->addDetalhe(array(_cl('turma.detalhe.sigla'), $registro['sgl_turma']));
    }

    if ($registro['max_aluno']) {
      $this->addDetalhe(array('Máximo de Alunos', $registro['max_aluno']));
    }

    $this->addDetalhe(array('Situação', dbBool($registro['visivel']) ? 'Ativo' : 'Desativo'));

    if ($registro['multiseriada'] == 1) {
      if ($registro['multiseriada'] == 1) {
        $registro['multiseriada'] = 'sim';
      }
      else {
        $registro['multiseriada'] = 'não';
      }

      $this->addDetalhe(array('Multi-Seriada', $registro['multiseriada']));

      $obj_serie_mult = new clsPmieducarSerie($registro['ref_ref_cod_serie_mult']);
      $det_serie_mult = $obj_serie_mult->detalhe();

      $this->addDetalhe(array('Eixo Multi-Seriada', $det_serie_mult['nm_serie']));
    }

    if ($padrao_ano_escolar == 1) {
      if ($registro['hora_inicial']) {
        $registro['hora_inicial'] = date('H:i', strtotime($registro['hora_inicial']));
        $this->addDetalhe(array('Hora Inicial', $registro['hora_inicial']));
      }

      if ($registro['hora_final']) {
        $registro['hora_final'] = date('H:i', strtotime($registro['hora_final']));
        $this->addDetalhe(array('Hora Final', $registro['hora_final']));
      }

      if ($registro['hora_inicio_intervalo']) {
        $registro['hora_inicio_intervalo'] = date('H:i', strtotime($registro['hora_inicio_intervalo']));
        $this->addDetalhe(array('Hora Início Intervalo', $registro['hora_inicio_intervalo']));
      }

      if ($registro['hora_fim_intervalo']) {
        $registro['hora_fim_intervalo'] = date('H:i', strtotime($registro['hora_fim_intervalo']));
        $this->addDetalhe(array('Hora Fim Intervalo', $registro['hora_fim_intervalo']));
      }
      if (is_string($registro['dias_semana']) && !empty($registro['dias_semana'])) {
        $registro['dias_semana'] = explode(',',str_replace(array('{', "}"), '', $registro['dias_semana']));
        foreach ($registro['dias_semana'] as $key => $dia) {
          $diasSemana .= $dias_da_semana[$dia] . '<br>';
        }
        $this->addDetalhe(array('Dia da Semana', $diasSemana));
      }
    }
    elseif ($padrao_ano_escolar == 0) {
      $obj = new clsPmieducarTurmaModulo();
      $obj->setOrderby('sequencial ASC');
      $lst = $obj->lista($this->cod_turma);

      if ($lst) {
        $tabela = '
          <table>
            <tr align="center">
              <td bgcolor="#f5f9fd "><b>Nome</b></td>
              <td bgcolor="#f5f9fd "><b>Data Início</b></td>
              <td bgcolor="#f5f9fd "><b>Data Fim</b></td>
              <td bgcolor="#f5f9fd "><b>Dias Letivos</b></td>
            </tr>';

        $cont = 0;

        foreach ($lst as $valor) {
          if (($cont % 2) == 0) {
            $color = ' bgcolor="#f5f9fd " ';
          }
          else {
            $color = ' bgcolor="#FFFFFF" ';
          }

          $obj_modulo = new clsPmieducarModulo($valor['ref_cod_modulo']);
          $det_modulo = $obj_modulo->detalhe();
          $nm_modulo = $det_modulo['nm_tipo'];

          $valor['data_inicio'] = dataFromPgToBr($valor['data_inicio']);
          $valor['data_fim']    = dataFromPgToBr($valor['data_fim']);

          $tabela .= sprintf('
            <tr>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=left>%s</td>
              <td %s align=center>%s</td>
            </tr>',
            $color, $nm_modulo, $color, $valor['data_inicio'], $color, $valor['data_fim'],$color, $valor['dias_letivos']
          );

          $cont++;
        }

        $tabela .= '</table>';
      }

      if ($tabela) {
        $this->addDetalhe(array('Módulo', $tabela));
      }

      if (is_string($registro['dias_semana']) && !empty($registro['dias_semana'])) {
        $registro['dias_semana'] = explode(',',str_replace(array('{', "}"), '', $registro['dias_semana']));
        foreach ($registro['dias_semana'] as $key => $dia) {
          $diasSemana .= $dias_da_semana[$dia] . '<br>';
        }
        $this->addDetalhe(array('Dia da Semana', $diasSemana));
      }
    }

    // Recupera os componentes curriculares da turma
    $componentes = array();

    try {
      $componentes = App_Model_IedFinder::getComponentesTurma(
        $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_turma
      );
    }
    catch (Exception $e) {
    }

    $tabela3 = '
      <table>
        <tr align="center">
          <td bgcolor="#f5f9fd "><b>Nome</b></td>
          <td bgcolor="#f5f9fd "><b>Carga horária</b></td>
        </tr>';

    $cont = 0;
    foreach ($componentes as $componente) {
      $color = ($cont++ % 2 == 0) ? ' bgcolor="#f5f9fd " ' : ' bgcolor="#FFFFFF" ';

      $tabela3 .= sprintf('
        <tr>
          <td %s align="left">%s</td>
          <td %s align="center">%.0f h</td>
        </tr>',
        $color, $componente, $color, $componente->cargaHoraria
      );
    }

    $tabela3 .= '</table>';
    $this->addDetalhe(array('Componentes curriculares', $tabela3));

    if ($obj_permissoes->permissao_cadastra(586, $this->pessoa_logada, 7)) {
      $this->url_novo   = 'educar_turma_cad.php';
      $this->url_editar = 'educar_turma_cad.php?cod_turma=' . $registro['cod_turma'];

      $this->array_botao[]            = 'Reclassificar alunos alfabeticamente';
      $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente reclassificar os alunos alfabeticamente?\\nAo utilizar esta opção para esta turma, a ordenação dos alunos no diário e em relatórios que é controlada por ordem de chegada após a data de fechamento da turma (campo Data de fechamento), passará a ter o controle novamente alfabético, desconsiderando a data de fechamento.\"))reclassifica_matriculas({$registro['cod_turma']})";

      $this->array_botao[]            = 'Editar sequência de alunos na turma';
      $this->array_botao_url_script[] = sprintf('go("educar_ordenar_alunos_turma.php?cod_turma=%d");', $registro['cod_turma']);

      $this->array_botao[]            = 'Lançar pareceres da turma';
      $this->array_botao_url_script[] = sprintf('go("educar_parecer_turma_cad.php?cod_turma=%d");', $registro['cod_turma']);
    }

    $this->url_cancelar = 'educar_turma_lst.php';
    $this->largura      = '100%';
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
