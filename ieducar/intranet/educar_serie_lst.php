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
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Eixo');
    $this->processoAp = '583';
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
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_curso;
  var $nm_serie;
  var $etapa_curso;
  var $concluinte;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $intervalo;

  var $ref_cod_instituicao;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = "Eixo - Listagem";

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $lista_busca = array(
      'Eixo',
      'Projeto'
    );

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      $lista_busca[] = 'Institui&ccedil;&atilde;o';
    }

    $this->addCabecalhos($lista_busca);

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso'));

    // outros Filtros
    $this->campoTexto('nm_serie', 'Eixo', $this->nm_serie, 30, 255, FALSE);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET["pagina_{$this->nome}"] ?
      $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite : 0;

    $obj_serie = new clsPmieducarSerie();
    $obj_serie->setOrderby("nm_serie ASC");
    $obj_serie->setLimite($this->limite, $this->offset);

    $lista = $obj_serie->lista(
      NULL,
      NULL,
      NULL,
      $this->ref_cod_curso,
      $this->nm_serie,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $this->ref_cod_instituicao
    );

    $total = $obj_serie->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        // Pega detalhes de foreign_keys
        $obj_ref_cod_curso = new clsPmieducarCurso($registro["ref_cod_curso"]);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];

        $obj_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
        $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

        $lista_busca = array(
          "<a href=\"educar_serie_det.php?cod_serie={$registro["cod_serie"]}\">{$registro["nm_serie"]}</a>",
          "<a href=\"educar_serie_det.php?cod_serie={$registro["cod_serie"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_serie_det.php?cod_serie={$registro["cod_serie"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }

        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2("educar_serie_lst.php", $total, $_GET, $this->nome, $this->limite);

    if ($obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3)) {
      $this->acao = "go(\"educar_serie_cad.php\")";
      $this->nome_acao = "Novo";
    }

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "Listagem de s&eacute;ries"
    ));
    $this->enviaLocalizacao($localizacao->montar());  

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