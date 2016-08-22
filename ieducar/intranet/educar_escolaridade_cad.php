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
 * @author      Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Escolaridade
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escolaridade');
    $this->processoAp = '632';
    $this->addEstilo("localizacaoSistema");
  }
}

class indice extends clsCadastro
{
  /**
   * Refer�ncia a usu�rio da sess�o
   * @var int
   */
  var $pessoa_logada = NULL;

  var $idesco;
  var $descricao;

  function Inicializar()
  {
    $retorno = 'Novo';

    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->idesco = $_GET['idesco'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 3, 'educar_escolaridade_lst.php');

    if (is_numeric($this->idesco)) {
      $obj = new clsCadastroEscolaridade($this->idesco);
      $registro = $obj->detalhe();

      if ($registro) {
        // Passa todos os valores obtidos no registro para atributos do objeto
        foreach($registro as $campo => $val) {
          $this->$campo = $val;
        }

        if ($obj_permissoes->permissao_excluir(632, $this->pessoa_logada, 3)) {
          $this->fexcluir = true;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'educar_escolaridade_det.php?idesco=' . $registro['idesco'] :
      'educar_escolaridade_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "{$nomeMenu} escolaridade"             
    ));
    $this->enviaLocalizacao($localizacao->montar());    

    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoOculto('idesco', $this->idesco);

    // Outros campos
    $this->campoTexto('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 30, 255, TRUE);
  }

  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsCadastroEscolaridade(NULL, $this->descricao);
    $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
      header('Location: educar_escolaridade_lst.php');
      die();
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
    return FALSE;
  }

  function Editar()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsCadastroEscolaridade($this->idesco, $this->descricao);
    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      header("Location: educar_escolaridade_lst.php");
      die();
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }

  function Excluir()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsCadastroEscolaridade($this->idesco, $this->descricao);
    $excluiu = $obj->excluir();
    if ($excluiu) {
      $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
      header('Location: educar_escolaridade_lst.php');
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