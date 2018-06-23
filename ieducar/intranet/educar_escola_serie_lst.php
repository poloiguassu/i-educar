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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escola Eixo');
    $this->processoAp = '585';
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

  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;

  var $ref_cod_curso;
  var $ref_cod_instituicao;
  var $ref_ref_cod_serie;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Escola Eixo - Listagem';

    foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
      $this->$var = ( $val === '' ) ? null: $val;

    $this->addBanner( 'imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet' );

    $lista_busca = array(
      'Eixo',
      'Projeto'
    );

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
    $lista_busca[] = 'Escola';
    $lista_busca[] = 'Institui&ccedil;&atilde;o';
    $lista_busca[] = 'Escola';
    $this->addCabecalhos($lista_busca);

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie'));

    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET["pagina_{$this->nome}"] ) ?
      $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite :
      0;

    $obj_escola_serie = new clsPmieducarEscolaSerie();
    $obj_escola_serie->setOrderby('nm_serie ASC');
    $obj_escola_serie->setLimite($this->limite, $this->offset);

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_escola_serie->codUsuario = $this->pessoa_logada;
        }

    $lista = $obj_escola_serie->lista(
      $this->ref_cod_escola,
      $this->ref_ref_cod_serie,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      NULL,
      NULL,
      NULL,
      NULL,
      $this->ref_cod_instituicao,
      $this->ref_cod_curso
    );

    $total = $obj_escola_serie->_total;

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
        $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
        $nm_serie = $det_ref_cod_serie['nm_serie'];

        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $lista_busca = array(
          "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_serie}</a>",
          "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        $lista_busca[] = "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_escola}</a>";
        $lista_busca[] = "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$registro["ref_cod_instituicao"]}</a>";
        $lista_busca[] = "<a href=\"educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_escola}</a>";

        $this->addLinhas($lista_busca);
      }
    }
    $this->addPaginador2("educar_escola_serie_lst.php", $total, $_GET,
      $this->nome, $this->limite);

    if ($obj_permissao->permissao_cadastra(585, $this->pessoa_logada, 7)) {
      $this->acao = "go(\"educar_escola_serie_cad.php\")";
      $this->nome_acao = "Novo";
    }

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "Listagem de v&iacute;nculos entre escolas e s&eacute;ries"
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
?>
