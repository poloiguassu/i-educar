<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'Portabilis/Utils/CustomLabel.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Customiza&ccedil;&atilde;o de labels');
    $this->processoAp = 9998869;
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;
  var $ref_cod_instituicao;
  var $custom_labels;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998869, $this->pessoa_logada, 7, 'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_configuracoes_index.php" => "Configurações",
         "" => "Customiza&ccedil;&atilde;o de labels"
    ));

    $this->enviaLocalizacao($localizacao->montar());

    return 'Editar';
  }

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao);
    $configuracoes = $configuracoes->detalhe();

    $this->custom_labels = $configuracoes['custom_labels'];

    $customLabel = new CustomLabel(PROJECT_ROOT . DS . 'configuration' . DS . 'custom_labels.json');
    $defaults = $customLabel->getDefaults();

    foreach($defaults as $k => $v) {
        $this->inputsHelper()->text('custom_labels[' . $k . ']', array(
            'label' => $k,
            'size' => 100,
            'required' => false,
            'placeholder' => $v,
            'value' => (!empty($this->custom_labels[$k])) ? $this->custom_labels[$k] : ''
        ));
    }
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao, array(
        'custom_labels' => $this->custom_labels
    ));

    $detalheAntigo = $configuracoes->detalhe();
    $editou = $configuracoes->edita();

    if ($editou) {
      $detalheAtual = $configuracoes->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("configuracoes_gerais", $this->pessoa_logada, $ref_cod_instituicao ? $ref_cod_instituicao : 'null');
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      header("Location: index.php");
      die();
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

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
