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
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  public function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Reserva Vaga');
    $this->processoAp = '639';
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

  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_ref_cod_serie;
  var $ref_cod_curso;
  var $ref_cod_instituicao;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = "Reserva Vaga - Listagem";

    foreach ($_GET as $var => $val) // passa todos os valores obtidos no GET para atributos do objeto
      $this->$var = ($val === '') ? NULL : $val;

    

    $lista_busca = array(
      "Eixo",
      "Projeto"
    );

    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
    if ($nivel_usuario == 1) {
      $lista_busca[] = 'Escola';
      $lista_busca[] = 'Institui&ccedil;&atilde;o';
    }
    elseif ($nivel_usuario == 2) {
      $lista_busca[] = "Escola";
    }
    $this->addCabecalhos($lista_busca);

    $get_escola = TRUE;
    $get_curso  = TRUE;
    $get_escola_curso_serie = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite :
      0;

    $obj_escola_serie = new clsPmieducarEscolaSerie();
    $obj_escola_serie->setLimite($this->limite, $this->offset);

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
        if (class_exists('clsPmieducarSerie')) {
          $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
          $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
          $nm_serie = $det_ref_cod_serie['nm_serie'];
        }
        else {
          $registro['ref_cod_serie'] = "Erro na gera&ccedil;&atilde;o";
          echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
        }

        if (class_exists('clsPmieducarCurso')) {
          $obj_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
          $det_curso = $obj_curso->detalhe();
          $registro["ref_cod_curso"] = $det_curso["nm_curso"];
        }
        else {
          $registro["ref_cod_serie"] = "Erro na gera&ccedil;&atilde;o";
          echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarSerie\n-->";
        }

        if (class_exists('clsPmieducarEscola')) {
          $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
          $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
          $nm_escola = $det_ref_cod_escola["nome"];
        }
        else {
          $registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
          echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
        }

        if (class_exists('clsPmieducarInstituicao')) {
          $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
          $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
          $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
        }
        else {
          $registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
          echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
        }

        $lista_busca = array(
          "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_serie}</a>",
          "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_escola}</a>";
          $lista_busca[] = "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }
        else if ($nivel_usuario == 2) {
          $lista_busca[] = "<a href=\"educar_reserva_vaga_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}\">{$nm_escola}</a>";
        }
        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2('educar_reserva_vaga_lst.php', $total, $_GET, $this->nome, $this->limite);
    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Listagem de reservas de vaga"
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

<script type='text/javascript'>
document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}
</script>