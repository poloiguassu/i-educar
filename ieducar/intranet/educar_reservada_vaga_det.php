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
 * @author      Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  ReservaVaga
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase {
  public function Formular() {
    $this->SetTitulo($this->_instituicao . 'Trilha Jovem Iguassu - Vagas Reservadas');
    $this->processoAp = '639';
    $this->addEstilo("localizacaoSistema");
  }
}

class indice extends clsDetalhe
{
  /**
   * Refer�ncia a usu�rio da sess�o
   * @var int
   */
  var $pessoa_logada = NULL;

  /**
   * T�tulo no topo da p�gina
   * @var string
   */
  var $titulo = '';

  // Atributos de mapeamento da tabela pmieducar.reserva_vaga
  var
    $cod_reserva_vaga,
    $ref_ref_cod_escola,
    $ref_ref_cod_serie,
    $ref_usuario_exc,
    $ref_usuario_cad,
    $ref_cod_aluno,
    $data_cadastro,
    $data_exclusao,
    $ativo;

  /**
   * Identifica��o para pmieducar.escola.
   * @var int
   */
  var $ref_cod_escola;

  /**
   * Identifica��o para pmieducar.curso.
   * @var int
   */
  var $ref_cod_curso;

  /**
   * Identifica��o para pmieducar.serie.
   * @var int
   */
  var $ref_cod_serie;

  /**
   * Identifica��o para pmieducar.instituicao.
   * @var int
   */
  var $ref_cod_instituicao;

  /**
   * Sobrescreve clsDetalhe::Gerar().
   * @see clsDetalhe::Gerar()
   */
  function Gerar()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Vagas Reservadas - Detalhe';
    

    $this->cod_reserva_vaga = $_GET['cod_reserva_vaga'];

    $obj_reserva_vaga = new clsPmieducarReservaVaga();
    $lst_reserva_vaga = $obj_reserva_vaga->lista($this->cod_reserva_vaga);

    if (is_array($lst_reserva_vaga)) {
      $registro = array_shift($lst_reserva_vaga);
    }

    if (!$registro) {
      header('Location: educar_reservada_vaga_lst.php');
      die();
    }

    // Atribui c�digos a vari�veis de inst�ncia
    $this->ref_cod_escola = $registro['ref_ref_cod_escola'];
    $this->ref_cod_curso  = $registro['ref_cod_curso'];
    $this->ref_cod_serie  = $registro['ref_ref_cod_serie'];
    $this->ref_cod_instituicao = $registro['ref_cod_instituicao'];

    // Desativa o pedido de reserva de vaga
    if ($_GET['desativa'] == true) {
      $this->_desativar();
    }

    // Institui��o
    $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $det_instituicao = $obj_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $det_instituicao['nm_instituicao'];

    // Escola
    $obj_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
    $det_escola = $obj_escola->detalhe();
    $registro['ref_ref_cod_escola'] = $det_escola['nome'];

    // S�rie
    $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_serie = $obj_serie->detalhe();
    $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

    // Projeto
    $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_curso = $obj_curso->detalhe();
    $registro['ref_cod_curso'] = $det_curso['nm_curso'];

    if ($registro['nm_aluno']) {
      $nm_aluno = $registro['nm_aluno'] . ' (aluno externo)';
    }

    if ($registro["ref_cod_aluno"]) {
      $obj_aluno = new clsPmieducarAluno();
      $lst_aluno = $obj_aluno->lista($registro['ref_cod_aluno'], NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lst_aluno)) {
        $det_aluno = array_shift($lst_aluno);
        $nm_aluno = $det_aluno['nome_aluno'];
      }
    }

    if ($nm_aluno) {
      $this->addDetalhe(array('Aluno', $nm_aluno));
    }

    if ($this->cod_reserva_vaga) {
      $this->addDetalhe(array('N&uacute;mero Reserva Vaga', $this->cod_reserva_vaga));
    }

    $this->addDetalhe(array('-', 'Reserva Pretendida'));

    if ($registro['ref_cod_instituicao']) {
      $this->addDetalhe(array('Institui&ccedil;&atilde;o', $registro['ref_cod_instituicao']));
    }

    if ($registro['ref_ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_ref_cod_escola']));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Projeto', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('Eixo', $registro['ref_ref_cod_serie']));
    }

    $obj_permissao = new clsPermissoes();
    if ($obj_permissao->permissao_cadastra(639, $this->pessoa_logada,7)) {
      $this->array_botao = array('Emiss&atilde;o de Documento de Reserva de Vaga', 'Desativar Reserva');
      $this->array_botao_url_script = array("showExpansivelImprimir(400, 200,  \"educar_relatorio_solicitacao_transferencia.php?cod_reserva_vaga={$this->cod_reserva_vaga}\",[], \"Relat�rio de Solicita��o de transfer�ncia\")","go(\"educar_reservada_vaga_det.php?cod_reserva_vaga={$this->cod_reserva_vaga}&desativa=true\")");
    }

    $this->url_cancelar = 'educar_reservada_vaga_lst.php?ref_cod_escola=' .
      $this->ref_cod_escola . '&ref_cod_serie=' . $this->ref_cod_serie;
    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe da vaga reservada"
    ));
    $this->enviaLocalizacao($localizacao->montar());    
  }

  /**
   * Desativa o pedido de reserva de vaga.
   * @return bool Retorna FALSE em caso de erro
   */
  private function _desativar()
  {
    $obj = new clsPmieducarReservaVaga($this->cod_reserva_vaga, NULL, NULL,
        $this->pessoa_logada, NULL, NULL, NULL, NULL, 0);
    $excluiu = $obj->excluir();

    if ($excluiu) {
      $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
      header('Location: educar_reservada_vaga_lst.php?ref_cod_escola=' .
        $this->ref_cod_escola . '&ref_cod_serie=' . $this->ref_cod_serie);
      die();
    }

    $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }

}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();