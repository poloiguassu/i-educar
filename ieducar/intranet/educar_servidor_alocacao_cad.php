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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @author    Haissam Yebahi <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once "lib/Portabilis/String/Utils.php";
require_once 'lib/Portabilis/Date/Utils.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @author    Haissam Yebahi <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Aloca��o');
    $this->processoAp = 635;
    $this->addEstilo('localizacaoSistema');
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @author    Haissam Yebahi <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;
  var $cod_servidor_alocacao;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_escola;
  var $ref_cod_servidor;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $carga_horaria_alocada;
  var $carga_horaria_disponivel;
  var $periodo;
  var $ref_cod_funcionario_vinculo;
  var $ano;
  var $data_admissao;
  var $alocacao_array          = array();
  var $alocacao_excluida_array = array();

  static $escolasPeriodos = array();
  static $periodos = array();

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $cod_servidor_alocacao   = $_GET['cod_servidor_alocacao'];

    if (is_numeric($cod_servidor_alocacao)) {
      $this->cod_servidor_alocacao = $cod_servidor_alocacao;

      $servidorAlocacao = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao);
      $servidorAlocacao = $servidorAlocacao->detalhe();

      $this->ref_ref_cod_instituicao     = $servidorAlocacao['ref_ref_cod_instituicao'];
      $this->ref_cod_servidor            = $servidorAlocacao['ref_cod_servidor'];
      $this->ref_cod_escola              = $servidorAlocacao['ref_cod_escola'];
      $this->periodo                     = $servidorAlocacao['periodo'];
      $this->carga_horaria_alocada       = $servidorAlocacao['carga_horaria'];
      $this->cod_servidor_funcao         = $servidorAlocacao['ref_cod_servidor_funcao'];
      $this->ref_cod_funcionario_vinculo = $servidorAlocacao['ref_cod_funcionario_vinculo'];
      $this->ativo                       = $servidorAlocacao['ativo'];
      $this->ano                         = $servidorAlocacao['ano'];
      $this->data_admissao               = $servidorAlocacao['data_admissao'];

    } else if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
      $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
      $this->ref_cod_servidor        = $ref_cod_servidor;
      $this->ref_cod_instituicao = $ref_ref_cod_instituicao;
    } else {
      header('Location: educar_servidor_lst.php');
      die();
    }

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
      $this->fexcluir = TRUE;
    }

    $this->url_cancelar      = sprintf(
      'educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
    );
    $this->nome_url_cancelar = 'Cancelar';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "Alocar servidor"             
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $retorno;
  }

  function Gerar()
  {

    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo('nm_instituicao', 'Institui��o', $inst_det['nm_instituicao']);
    $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);

    // Dados do servidor
    $objTemp = new clsPmieducarServidor($this->ref_cod_servidor, NULL,
        NULL, NULL, NULL, NULL, 1, $this->ref_ref_cod_instituicao);
    $det = $objTemp->detalhe();

    if ($det) {
      $this->carga_horaria_disponivel = $det['carga_horaria'];
    }

    if ($this->ref_cod_servidor) {
      $objTemp = new clsPessoaFisica($this->ref_cod_servidor);
      $detalhe = $objTemp->detalhe();
      //$detalhe = $detalhe['idpes']->detalhe();
      $nm_servidor = $detalhe['nome'];
    }

    $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

    if ($_POST['alocacao_array']) {
      $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
    }

    if ($_POST['alocacao_excluida_array']) {
      $this->alocacao_excluida_array = unserialize(urldecode($_POST['alocacao_excluida_array']));
    }

    if ($_POST['carga_horaria_alocada'] && $_POST['periodo']) {
      $aux = array();
      $aux['carga_horaria_alocada'] = $_POST['carga_horaria_alocada'];
      $aux['periodo']               = $_POST['periodo'];
      $aux['ref_cod_escola']        = $_POST['ref_cod_escola'];
      $aux['novo']                  = 1;

      $this->alocacao_array[] = $aux;

      unset($this->periodo);
      unset($this->carga_horaria_alocada);
      unset($this->ref_cod_escola);
    }

    // Exclus�o
    if ($this->alocacao_array) {
      foreach ($this->alocacao_array as $key => $alocacao) {
        if (is_numeric($_POST['excluir_periodo'])) {
          if ($_POST['excluir_periodo'] == $key) {
            $this->alocacao_excluida_array[] = $alocacao;
            unset($this->alocacao_array[$key]);
            unset($this->excluir_periodo);
          }
        }
      }
    }

    // Carga hor�ria
    $carga = $this->carga_horaria_disponivel;
    $this->campoRotulo('carga_horaria_disponivel', 'Carga Hor�ria', $carga . ':00');

    foreach ($this->alocacao_array as $alocacao) {
      $carga_horaria_ = explode(':', $alocacao['carga_horaria_alocada']);

      $horas   += (int) $carga_horaria_[0];
      $minutos += (int) $carga_horaria_[1];
    }

    $total = ($horas * 60) + $minutos;
    $rest  = ($carga * 60) - $total;

    $total = sprintf('%02d:%02d', ($total / 60), ($total % 60));
    $rest  = sprintf('%02d:%02d', ($rest / 60), ($rest % 60));

    $this->campoRotulo('horas_utilizadas', 'Horas Utilizadas', $total);
    $this->campoRotulo('horas_restantes', 'Horas Restantes', $rest);
    $this->campoOculto('horas_restantes_', $rest);

    $this->campoQuebra();

    $this->campoOculto('excluir_periodo', '');
    unset($aux);

    // Escolas
    $obj_escola = new clsPmieducarEscola();
    $permissao  = new clsPermissoes();

    // Exibe apenas a escola ao qual o usu�rio de n�vel escola est� alocado
    if (4 == $permissao->nivel_acesso($this->pessoa_logada)) {
      $lista_escola = $obj_escola->lista($permissao->getEscola($this->pessoa_logada),
        NULL, NULL, $this->ref_ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL,
        NULL, 1);

      $nome_escola = $lista_escola[0]['nome'];
      $cod_escola  = $lista_escola[0]['cod_escola'];

      $this->campoTextoInv('ref_cod_escola_label', 'Escola', $nome_escola, 100, 255, FALSE);
      $this->campoOculto('ref_cod_escola', $cod_escola);
    }
    // Usu�rio administrador visualiza todas as escolas dispon�veis
    else {
      $lista_escola = $obj_escola->lista(NULL, NULL, NULL,
        $this->ref_ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      $opcoes = array('' => 'Selecione');

      if ($lista_escola) {
        foreach ($lista_escola as $escola) {
          $opcoes[$escola['cod_escola']] = $escola['nome'];
        }
      }

      $this->campoLista('ref_cod_escola', 'Escola', $opcoes, $this->ref_cod_escola,
        '', FALSE, '', '', FALSE, FALSE);
    }

    // Períodos
    $periodo = array(
      1  => 'Matutino',
      2  => 'Vespertino',
      3  => 'Noturno'
    );
    self::$periodos = $periodo;

    $this->campoLista('periodo', 'Per�odo', $periodo, $this->periodo, NULL, FALSE,
      '', '', FALSE, FALSE);

    $this->campoHora('carga_horaria_alocada', 'Carga Hor�ria',
      $this->carga_horaria_alocada, FALSE);

    // Altera a string de descri��o original do campo hora
    $this->campos['carga_horaria_alocada'][6] = sprintf('Formato hh:mm (m�ximo de %d horas por per�odo)', clsPmieducarServidorAlocacao::$cargaHorariaMax);

    // Carga horária
    $this->campoHoraServidor('carga_horaria_alocada', 'Carga horária', $this->carga_horaria_alocada, TRUE);

    $options = array(
        'label' => 'Data de admissão',
        'placeholder' => 'dd/mm/yyyy',
        'hint' => 'A data deve estar em branco ou dentro do período de datas da exportação para o Educacenso, para o servidor ser exportado.',
        'value' => $this->data_admissao,
        'required' => FALSE,
    );
    $this->inputsHelper()->date('data_admissao', $options);

    $this->campoRotulo('bt_incluir_periodo', 'Per�odo', "<a href='#' onclick=\"if(validaHora()) { document.getElementById('incluir_periodo').value = 'S'; document.getElementById('tipoacao').value = ''; document.{$this->__nome}.submit();}\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");

    $lista_funcoes = $obj_funcoes->funcoesDoServidor($this->ref_ref_cod_instituicao, $this->ref_cod_servidor);

    $opcoes = array('' => 'Selecione');

      foreach ($this->alocacao_array as $key => $alocacao) {
        $obj_permissoes = new clsPermissoes();
        $link_excluir   = '';

        $obj_escola = new clsPmieducarEscola($alocacao['ref_cod_escola']);
        $det_escola = $obj_escola->detalhe();
        $det_escola = $det_escola['nome'];

        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {

          $show = TRUE;
          if (4 == $permissao->nivel_acesso($this->pessoa_logada)
              && $alocacao['ref_cod_escola'] != $permissao->getEscola($this->pessoa_logada)
          ) {
            $show = FALSE;
          }

          $link_excluir = $show ? "<a href='#' onclick=\"getElementById('excluir_periodo').value = '{$key}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" : "";
        }

        // @todo CoreExt_Enum
        switch ($alocacao['periodo']) {
          case 1:
            $nm_periodo = 'Matutino';
            break;
          case 2:
            $nm_periodo = 'Vespertino';
            break;
          case 3:
            $nm_periodo = 'Noturno';
            break;
        }

        // Per�odos usados na escola
        self::$escolasPeriodos[$alocacao['ref_cod_escola']][$alocacao['periodo']] = $alocacao['periodo'];

        $this->campoTextoInv('periodo_' . $key, '', $nm_periodo, 10, 10, FALSE,
          FALSE, TRUE, '', '', '', '', 'periodo');

        $this->campoTextoInv('carga_horaria_alocada_' . $key, '',
          substr($alocacao['carga_horaria_alocada'], 0, 5), 5, 5, FALSE, FALSE, TRUE, '', '',
          '', '', 'ds_carga_horaria_');

        $this->campoTextoInv('ref_cod_escola_' . $key, '', $det_escola, 70, 255,
          FALSE, FALSE, FALSE, '', $link_excluir, '', '', 'ref_cod_escola_');
      }
    }

    $this->campoLista('cod_servidor_funcao', 'Função', $opcoes, $this->cod_servidor_funcao, '', FALSE, '', '', FALSE, FALSE);

    // Vínculos
    $opcoes = array("" => "Selecione", 5 => "Comissionado", 4 => "Contratado", 3 => "Efetivo", 6 => "Estagi&aacute;rio");

    $this->campoLista("ref_cod_funcionario_vinculo", "V&iacute;nculo", $opcoes, $this->ref_cod_funcionario_vinculo, NULL, FALSE, '', '', FALSE, FALSE);
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
        "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
    $dataAdmissao = $this->data_admissao ? Portabilis_Date_Utils::brToPgSql($this->data_admissao) : NULL;

    $servidorAlocacao = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao,
                                                 $this->ref_ref_cod_instituicao,
                                                 null,
                                                 null,
                                                 null,
                                                 $this->ref_cod_servidor,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 null,
                                                 $this->ano,
                                                 $dataAdmissao);

    $carga_horaria_disponivel = $this->hhmmToMinutes($this->carga_horaria_disponivel);
    $carga_horaria_alocada    = $this->hhmmToMinutes($this->carga_horaria_alocada);
    $carga_horaria_alocada   += $this->hhmmToMinutes($servidorAlocacao->getCargaHorariaAno());

    if ($carga_horaria_disponivel >= $carga_horaria_alocada){

    $obj_novo = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao,
                                                 $this->ref_ref_cod_instituicao,
                                                 null,
                                                 $this->pessoa_logada,
                                                 $this->ref_cod_escola,
                                                 $this->ref_cod_servidor,
                                                 null,
                                                 null,
                                                 $this->ativo,
                                                 $this->carga_horaria_alocada,
                                                 $this->periodo,
                                                 $this->cod_servidor_funcao,
                                                 $this->ref_cod_funcionario_vinculo,
                                                 $this->ano,
                                                 $dataAdmissao);

      if ($obj_novo->periodoAlocado()) {
        $this->mensagem = 'Período informado já foi alocado. Por favor, selecione outro.<br />';
        return FALSE;
      }

      $cadastrou = $obj_novo->cadastra();

      if (!$cadastrou) {
        $this->mensagem = 'Cadastro não realizado.<br />';
        echo "<!--\nErro ao cadastrar clsPmieducarServidorAlocacao\nvalores obrigatorios\nis_numeric($this->ref_ref_cod_instituicao) &&
              is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_servidor) &&
              is_numeric($this->periodo) && ($this->carga_horaria_alocada)\n-->";
        return FALSE;
      }

      // Excluí alocação existente
      if ($this->cod_servidor_alocacao) {
        $obj_tmp = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, null, $this->pessoa_logada);
        $obj_tmp->excluir();
      }

      // Atualiza código da alocação
      $this->cod_servidor_alocacao = $cadastrou;
    }else{
      $this->mensagem = 'Não é possível alocar quantidade superior de horas do que o disponível.<br />';
      $this->alocacao_array = null;

    if ($this->alocacao_array) {
      foreach ($this->alocacao_array as $alocacao) {
        if ($alocacao['novo']) {
          $cargaHoraria = explode(':', $alocacao['carga_horaria_alocada']);

          $hora    = isset($cargaHoraria[0]) ? $cargaHoraria[0] : 0;
          $minuto  = isset($cargaHoraria[1]) ? $cargaHoraria[1] : 0;
          $segundo = isset($cargaHoraria[2]) ? $cargaHoraria[2] : 0;

          $cargaHoraria = sprintf("%'02d:%'02d:%'02d", $hora, $minuto, $segundo);

          $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
            NULL, $this->pessoa_logada, $alocacao['ref_cod_escola'],
            $this->ref_cod_servidor, NULL, NULL, $this->ativo,
            $cargaHoraria, $alocacao['periodo']);

          $cadastrou = FALSE;

          if (FALSE == $obj->lista(NULL, $this->ref_ref_cod_instituicao,
            NULL, NULL, $alocacao['ref_cod_escola'], $this->ref_cod_servidor, NULL, NULL,
            NULL, NULL, NULL, NULL, $alocacao['periodo'])
          ) {
            $cadastrou = $obj->cadastra();
          }

          if (!$cadastrou) {
            $this->mensagem = 'Cadastro n�o realizado.<br />';
            echo "<!--\nErro ao cadastrar clsPmieducarServidorAlocacao\nvalores obrigatorios\nis_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_servidor) && is_numeric($this->periodo) && ($this->carga_horaria_alocada)\n-->";
            return FALSE;
          }
        }
      }
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
    header('Location: ' . sprintf('educar_servidor_alocacao_det.php?cod_servidor_alocacao=%d', $this->cod_servidor_alocacao));
    die();
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {
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
var escolasPeriodos = <?php print json_encode(indice::$escolasPeriodos); ?>;
var periodos = <?php print json_encode(indice::$periodos); ?>;

window.onload = function()
{
  getPeriodos(document.getElementById('ref_cod_escola').value);
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getPeriodos(document.getElementById('ref_cod_escola').value);
}

    if ($this->cod_servidor_alocacao) {
      $obj_tmp = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, null, $this->pessoa_logada);
      $excluiu = $obj_tmp->excluir();

      if ($excluiu) {
        $this->mensagem = "Exclusão efetuada com sucesso.<br>";
        header("Location: ". sprintf(
              'educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d',
              $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
        die();
      }
    }

    $this->mensagem = 'Exclusão não realizada.<br>';
    return false;
  }

  if (!((/[0-9]{2}:[0-9]{2}/).test(document.formcadastro.carga_horaria_alocada.value))) {
    alert('Preencha o campo "Carga Hor�ria" corretamente!');
    return false;
  }

  if (!periodo) {
    alert('Preencha o campo "Per�odo" corretamente!');
    return false;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

  if (hora_ > hora_max_) {
    message = <?php print sprintf('"O n�mero de horas m�ximo por per�odo/escola � de %.0fh."', clsPmieducarServidorAlocacao::$cargaHorariaMax); ?>;
    alert(message);
    return false;
  }

  if (hora_ > hora_restantes_) {
    alert("Aten��o n�mero de horas excedem o n�mero de horas dispon�veis! Por favor, corrija.");
    document.getElementById('ref_cod_escola').value        = '';
    document.getElementById('periodo').value               = '';
    document.getElementById('carga_horaria_alocada').value = '';
    return false;
  }

// Gera o código HTML
$pagina->MakeAll();
?>