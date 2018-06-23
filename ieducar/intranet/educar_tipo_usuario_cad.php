
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
 * @subpackage  pmieducar
 * @subpackage  Administrativo
 * @subpackage  TipoUsuario
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Tipo Usu�rio');
    $this->processoAp = '554';
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
 /**
  * Refer�ncia a usu�rio da sess�o.
  * @var int
  */
  var $pessoa_logada;

  var $cod_tipo_usuario;
  var $ref_funcionario_cad;
  var $ref_funcionario_exc;
  var $nm_tipo;
  var $descricao;
  var $nivel;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $permissoes;

  function Inicializar()
  {
    $retorno = 'Novo';

    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    // Verifica se o usu�rio tem permiss�o para realizar o cadastro
    $obj_permissao = new clsPermissoes();
    $obj_permissao->permissao_cadastra(554, $this->pessoa_logada, 7,
      'educar_tipo_usuario_lst.php', TRUE);

    $this->cod_tipo_usuario = $_GET['cod_tipo_usuario'];

    if (is_numeric($this->cod_tipo_usuario)) {
      $obj = new clsPmieducarTipoUsuario($this->cod_tipo_usuario);

      if (! $registro = $obj->detalhe()){
        header('Location: educar_tipo_usuario_lst.php');
      }

      if ($registro) {
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $this->fexcluir = $obj_permissao->permissao_excluir(554,$this->pessoa_logada,7,null,true);

        $retorno = "Editar";
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'educar_tipo_usuario_det.php?cod_tipo_usuario=' . $registro['cod_tipo_usuario'] :
      'educar_tipo_usuario_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "{$nomeMenu} tipo de usu&aacute;rio"             
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $retorno;
  }

  function Gerar()
  {
    // Primary key
    $this->campoOculto('cod_tipo_usuario', $this->cod_tipo_usuario);

    $this->campoTexto('nm_tipo', 'Tipo de Usu�rio', $this->nm_tipo, 40, 255, TRUE);

    $array_nivel = array(
      '8' => 'Biblioteca',
      '4' => 'Escola',
      '2' => 'Institucional',
      '1' => 'Poli-institucional'
    );

    $this->campoLista('nivel', 'N&iacute;vel', $array_nivel, $this->nivel);

    $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 37, 5, FALSE);
    $this->campoRotulo('listagem_menu', '<b>Permiss&otilde;es de acesso aos menus</b>', '');
    $objTemp = new clsBanco();

    // cod menu 55 = ieducar, 57 = biblioteca (ambos sistema = ieducar (2) )

    $objTemp->Consulta('
      SELECT
        sub.cod_menu_submenu,
        sub.nm_submenu,
        m.nm_menu
      FROM
        menu_submenu sub,
        menu_menu m
      WHERE
        sub.ref_cod_menu_menu = m.cod_menu_menu
        AND ((m.cod_menu_menu = 55 OR m.ref_cod_menu_pai = 55) OR
            (m.cod_menu_menu = 69 OR m.ref_cod_menu_pai = 69) OR
            (m.cod_menu_menu = 68 OR m.ref_cod_menu_pai = 68) OR
            (m.cod_menu_menu = 7 OR m.ref_cod_menu_pai = 7) OR
            (m.cod_menu_menu = 23 OR m.ref_cod_menu_pai = 23) OR
            (m.cod_menu_menu = 5 OR m.ref_cod_menu_pai = 5) OR
            (m.cod_menu_menu = 25 OR m.ref_cod_menu_pai = 25) OR
            (m.cod_menu_menu = 38 OR m.ref_cod_menu_pai = 38) OR
            (m.cod_menu_menu = 57 OR m.ref_cod_menu_pai = 57) OR
            (m.cod_menu_menu = 56 OR m.ref_cod_menu_pai = 56) OR
            (m.cod_menu_menu = 70 OR m.ref_cod_menu_pai = 70) OR
            (m.cod_menu_menu = 71 OR m.ref_cod_menu_pai = 71))
      ORDER BY
        cod_menu_menu, upper(sub.nm_submenu)
    ');

    while ($objTemp->ProximoRegistro()) {
      list($codigo, $nome,$menu_pai) = $objTemp->Tupla();
      $opcoes[$menu_pai][$codigo] = $nome;
    }

    $array_opcoes  = array(
      ''  => 'Selecione',
      'M' => 'Marcar',
      'U' => 'Desmarcar'
    );

    $array_opcoes_ = array(
      ''  => 'Selecione',
      'M' => 'Marcar Todos',
      'U' => 'Desmarcar Todos'
    );

    $this->campoLista('todos', 'Op&ccedil;&otilde;es', $array_opcoes_, '',
      "selAction('-', '-', this)", FALSE, '', '', FALSE, FALSE);
    $script = "menu = [];\n";

    foreach ($opcoes as $id_pai => $menu) {
      $this->campoQuebra();
      $this->campoRotulo($id_pai,'<b>' . $id_pai . '-</b>', '');

      $this->campoLista($id_pai . ' 1', 'Op&ccedil;&otilde;es', $array_opcoes,
        '', "selAction('$id_pai', 'visualiza', this)", TRUE, '', '', FALSE, FALSE);

      $this->campoLista($id_pai . ' 2', 'Op&ccedil;&otilde;es', $array_opcoes,
        '', "selAction('$id_pai', 'cadastra', this)", TRUE, '', '', FALSE, FALSE);

      $this->campoLista($id_pai . ' 3', 'Op&ccedil;&otilde;es', $array_opcoes,
        '', "selAction('$id_pai', 'exclui', this)", FALSE, '', '', FALSE, FALSE);

      $script .= "menu['$id_pai'] = [];\n";

      foreach ($menu as $id => $submenu) {
        $obj_menu_tipo_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario, $id);
        $obj_menu_tipo_usuario->setCamposLista('cadastra', 'visualiza', 'exclui');
        $obj_det = $obj_menu_tipo_usuario->detalhe();

        if($this->tipoacao == 'Novo') {
          $obj_det['visualiza'] = $obj_det['cadastra'] = $obj_det['exclui'] = 1;
        }

        $script .= "menu['$id_pai'][menu['$id_pai'].length] = $id; \n";

        $this->campoOculto("permissoes[{$id}][id]", $id);

        /* alterado para campos n�o usar inline, pois por algum motivo os dois primeiros checkboxes
           n�o estavam funcionando devidamente */

        // visualiza

        $options = array(
          'label'      => $submenu,
          'value'      => $obj_det['visualiza'],
          'label_hint' => 'Visualizar',
          'inline'     => true
          );

        $this->inputsHelper()->checkbox("permissoes[{$id}][visualiza]", $options);


        // cadastra

        $options = array(
          'label'      => $submenu,
          'value'      => $obj_det['cadastra'],
          'label_hint' => 'Cadastrar',
          'inline'     => true
          );

        $this->inputsHelper()->checkbox("permissoes[{$id}][cadastra]", $options);


        // excluir

        $options = array(
          'label'      => $submenu,
          'value'      => $obj_det['exclui'],
          'label_hint' => 'Excluir'
          );

        $this->inputsHelper()->checkbox("permissoes[{$id}][exclui]", $options);
      }

    }

    echo '<script type="text/javascript">'. $script . '</script>';
  }

  function Novo() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $tipoUsuario = new clsPmieducarTipoUsuario($this->cod_tipo_usuario, $this->pessoa_logada, NULL,
                                               $this->nm_tipo, $this->descricao, $this->nivel, NULL, NULL, 1);
    $this->cod_tipo_usuario = $tipoUsuario->cadastra();

    if ($this->cod_tipo_usuario){
      $tipo_usuario = new clsPmieducarTipoUsuario($this->cod_tipo_usuario);
      $tipo_usuario = $tipo_usuario->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("tipo_usuario", $this->pessoa_logada, $this->cod_tipo_usuario);
      $auditoria->inclusao($tipo_usuario);

      $this->createMenuTipoUsuario();
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
    return FALSE;
  }

  function Editar() {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $tipoUsuario = new clsPmieducarTipoUsuario($this->cod_tipo_usuario, NULL, $this->pessoa_logada,
                                               $this->nm_tipo, $this->descricao, $this->nivel, NULL, NULL, 1);

    $detalheAntigo = $tipoUsuario->detalhe();
    if ($tipoUsuario->edita()){
      $detalheAtual = $tipoUsuario->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("tipo_usuario", $this->pessoa_logada, $this->cod_tipo_usuario);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
      $this->createMenuTipoUsuario();
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }

  protected function createMenuTipoUsuario() {
    if ($this->permissoes) {

      // remove todos menus vinculados ao tipo de usu�rio.
      $menuTipoUsuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario);
      $menuTipoUsuario->excluirTudo();

      // vinvula ao tipo de usu�rio, menus com alguma permiss�o marcada
      foreach ($this->permissoes as $menuSubmenuId => $permissao) {
        if ($permissao['cadastra'] || $permissao['visualiza'] || $permissao['exclui']) {

          // recebe c�digo falso em algum momento?
          if ($this->cod_tipo_usuario == FALSE)
             $this->cod_tipo_usuario = '0';

          $menuTipoUsuario = new clsPmieducarMenuTipoUsuario(
            $this->cod_tipo_usuario,
            $menuSubmenuId,
            $permissao['cadastra']  ? 1 : 0,
            $permissao['visualiza'] ? 1 : 0,
            $permissao['exclui']    ? 1 : 0
          );

          if (! $menuTipoUsuario->cadastra()) {
            $this->mensagem .= "Erro ao cadastrar acessos aos menus.<br>";
            return FALSE;
          }

        }
      } //for
    }

    $this->mensagem .= 'Altera&ccedil;&atilde;o efetuada com sucesso.<br>';
    header('Location: educar_tipo_usuario_lst.php');
    die();
  }

  function Excluir()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $tipoUsuario = new clsPmieducarTipoUsuario($this->cod_tipo_usuario, NULL, $this->pessoa_logada);
    $detalhe = $tipoUsuario->detalhe();

    if ($tipoUsuario->excluir()) {
      $auditoria = new clsModulesAuditoriaGeral("tipo_usuario", $this->pessoa_logada, $this->cod_tipo_usuario);
      $auditoria->exclusao($detalhe);
      $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';

      $menuTipoUsuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario);
      $menuTipoUsuario->excluirTudo();

      header('Location: educar_tipo_usuario_lst.php');
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

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
/**
 * Marca/desmarca todas as op��es de submenu (opera��es de sistema) de um dados
 * menu pai.
 *
 * @param  int     menu_pai
 * @param  string  tipo
 * @param  string  acao
 */
function selAction(menu_pai, tipo, acao)
{
  var element = document.getElementsByTagName('input');
  var state;

  switch (acao.value) {
    case 'M':
      state = true;
    break;
    case 'U':
      state = false;
    break
    default:
      return false;
  }

  acao.selectedIndex = 0;

  if(menu_pai == '-' && tipo == '-') {
    for (var ct = 0; ct < element.length; ct++) {
      if(element[ct].getAttribute('type') == 'checkbox') {
        element[ct].checked = state;
        element[ct].value = ( state ? 'on' : '');
      }
    }

    return;
  }

  for (var ct=0; ct < menu[menu_pai].length; ct++){
    document.getElementsByName('permissoes[' + menu[menu_pai][ct]  + '][' + tipo + ']')[0].checked = state;
    document.getElementsByName('permissoes[' + menu[menu_pai][ct]  + '][' + tipo + ']')[0].value = ( state ? 'on' : '');
  }
}
</script>
