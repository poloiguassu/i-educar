<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

/**
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Exporta&ccedil;&atilde;o Educacenso');
    $this->processoAp = ($_REQUEST['fase2'] == 1 ? 9998845 : 846);
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ano;
  var $ref_cod_instituicao;
  var $segunda_fase = false;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->segunda_fase = ($_REQUEST['fase2'] == 1);

    $codigoMenu = ($this->segunda_fase ? 9998845 : 846);

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra($codigoMenu, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $nomeTela = $this->segunda_fase ? '2ª fase - Situação final' : '1ª fase - Matrícula inicial';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_educacenso_index.php"       => "Educacenso",
         ""                                  => $nomeTela
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $exportacao = $_POST["exportacao"];

    if ($exportacao) {
      $converted_to_iso88591 = utf8_decode($exportacao);

      header('Content-type: text/plain');
      header('Content-Length: ' . strlen($converted_to_iso88591));
      header('Content-Disposition: attachment; filename=exportacao.txt');
      echo $converted_to_iso88591;
      die();
    }

    $this->acao_enviar      = "acaoExportar();";

    return 'Nova exportação';
  }

  function Gerar()
  {
    $fase2 = $_REQUEST['fase2'];

    $dicaCampoData = 'dd/mm/aaaa';

    if ($fase2 == 1) {
      $dicaCampoData = 'A data informada neste campo, deverá ser a mesma informada na 1ª fase da exportação (Matrícula inicial).';
      $this->campoOculto("fase2", "true");
    }

    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));

    $this->inputsHelper()->date('data_ini',array('label' => 'Data início',
                                                 'value' => $this->data_ini,
                                                 'dica' => $dicaCampoData));
    $this->inputsHelper()->date('data_fim',array('label' => 'Data fim',
                                                 'value' => $this->data_fim,
                                                 'dica' => $dicaCampoData));
    if (!empty($this->data_ini) && !empty($this->data_fim) && !empty($this->ref_cod_escola)) {
        Portabilis_View_Helper_Application::loadJavascript($this, '/modules/Educacenso/Assets/Javascripts/Educacenso.js');
    }

  }

  function Novo()
  {

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

function acaoExportar() {
    document.formcadastro.target='_blank';
    acao();
    document.getElementById( 'btn_enviar' ).disabled = false;
    document.getElementById( 'btn_enviar' ).value = 'Exportar';
}

function marcarCheck(idValue) {
    // testar com formcadastro
    var contaForm = document.formcadastro.elements.length;
    var campo = document.formcadastro;
    var i;

    for (i=0; i<contaForm; i++) {
        if (campo.elements[i].id == idValue) {

            campo.elements[i].checked = campo.CheckTodos.checked;
        }
    }
}
</script>
