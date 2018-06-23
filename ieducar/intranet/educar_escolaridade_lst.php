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
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escolaridade do servidor');
    $this->processoAp = '632';
    $this->addEstilo("localizacaoSistema");
  }
}

class indice extends clsListagem
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

  /**
   * Limite de registros por p�gina
   * @var int
   */
  var $limite = 0;

  /**
   * In�cio dos registros a serem exibidos (limit)
   * @var int
   */
  var $offset = 0;

  var $idesco;
  var $descricao;

  function Gerar()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Escolaridade - Listagem';

    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET AS $var => $val){
      $this->$var = ($val === '') ? NULL : $val;
    }

    

    $this->addCabecalhos(array(
      'Descri&ccedil;&atilde;o'
    ));

    // Outros Filtros
    $this->campoTexto('descricao', 'Descri��o', $this->descricao, 30, 255, FALSE);

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_' . $this->nome]) ?
      $_GET['pagina_' . $this->nome] * $this->limite-$this->limite : 0;

    $obj_escolaridade = new clsCadastroEscolaridade();
    $obj_escolaridade->setOrderby('descricao ASC');
    $obj_escolaridade->setLimite($this->limite, $this->offset);
    $lista = $obj_escolaridade->lista(NULL,
      $this->descricao
    );

    $total = $obj_escolaridade->_total;

    // Monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $this->addLinhas(array(
          "<a href=\"educar_escolaridade_det.php?idesco={$registro["idesco"]}\">{$registro["descricao"]}</a>"
        ));
      }
    }

    $this->addPaginador2('educar_escolaridade_lst.php', $total, $_GET, $this->nome, $this->limite);
    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 3)) {
      $this->acao = 'go("educar_escolaridade_cad.php")';
      $this->nome_acao = 'Novo';
    }

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Listagem de escolaridades"
    ));
    $this->enviaLocalizacao($localizacao->montar());    

    $this->largura = '100%';
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