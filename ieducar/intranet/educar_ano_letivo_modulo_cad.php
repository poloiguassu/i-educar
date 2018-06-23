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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/Date/Utils.php';

require_once 'App/Date/Utils.php';

require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja�
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Ano Letivo M�dulo');
    $this->processoAp = 561;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja�
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_ano;
  var $ref_ref_cod_escola;
  var $sequencial;
  var $ref_cod_modulo;
  var $data_inicio;
  var $data_fim;

  var $ano_letivo_modulo;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_modulo     = $_GET['ref_cod_modulo'];
    $this->ref_ref_cod_escola = $_GET['ref_cod_escola'];
    $this->ref_ano            = $_GET['ano'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    if (is_numeric($this->ref_ano) && is_numeric($this->ref_ref_cod_escola)) {
      $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola, $this->ref_ano);
      $registro  = $obj->detalhe();

      if ($registro) {
        if ($obj_permissoes->permissao_excluir(561, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = $_GET['referrer'] ?
      $_GET['referrer'] . '?cod_escola=' . $this->ref_ref_cod_escola:
      'educar_escola_lst.php';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "{$nomeMenu} m&oacute;dulos do ano letivo"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = $this->$campo ? $this->$campo : $val;
      }
    }

    // Primary keys
    $this->campoOculto('ref_ano', $this->ref_ano);
    $this->campoOculto('ref_ref_cod_escola', $this->ref_ref_cod_escola);

    $obj_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
    $det_escola = $obj_escola->detalhe();
    $ref_cod_instituicao = $det_escola['ref_cod_instituicao'];

    $obj = new clsPmieducarAnoLetivoModulo();
    $obj->setOrderBy('sequencial ASC');
    $registros = $obj->lista($this->ref_ano - 1, $this->ref_ref_cod_escola);
    $cont = 0;
    $modulosAnoAnterior = "";
    if ($registros) {

      $tabela = "<table border=0 style='' cellpadding=2 width='100%'>";
      $tabela .= "<tr bgcolor=$cor><td colspan='2'>M&oacute;dulos do ano anterior (".($this->ref_ano - 1).")</td></tr><tr><td>";
      $tabela .= "<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" align=\"left\" width='300px'>";
      $tabela .= "<tr bgcolor='#A1B3BD'><th width='100px'>Etapa<a name='ano_letivo'/></th><th width='200px'>Per�odo</th></tr>";

     foreach ($registros as $campo) {
      $cor = "#E3E8EF"; #$cor == "#FFFFFF" ? "#E3E8EF" : "#FFFFFF";
      $cont++;
      $tabela .= "<tr bgcolor='$cor'><td align='center'>{$cont}</td><td align='center'>".dataFromPgToBr($campo['data_inicio'])." � ".dataFromPgToBr($campo['data_fim'])."</td></tr>";
      //$modulosAnoAnterior .= ++$cont."� Etapa: De ".dataFromPgToBr($campo['data_inicio'])." � ".dataFromPgToBr($campo['data_fim']);
     }

     $tabela .="</table>";
     $tabela .= "<tr><td colspan='2'><b> Adicione os m�dulos abaixo para {$this->ref_ano} semelhante ao exemplo do ano anterior: </b></td></tr><tr><td>";
     $tabela .= "</table>";
    }

      $existeBissexto = false;

      foreach ($registros as $campo) {
        $cor = "#f5f9fd";
        $cont++;
        $tabela .= "<tr bgcolor='$cor'><td align='center'>{$cont}</td><td align='center'>".dataFromPgToBr($campo['data_inicio'])." à ".dataFromPgToBr($campo['data_fim'])."</td></tr>";

    // M�dulos do ano letivo
    if ($_POST['ano_letivo_modulo']) {
      $this->ano_letivo_modulo = unserialize(urldecode($_POST['ano_letivo_modulo']));
    }

        $novaDataInicio = str_replace($ano, $this->ref_ano, $campo['data_inicio']);
        $novaDataFim    = str_replace($ano, $this->ref_ano, $campo['data_fim']);

        if (Portabilis_Date_Utils::checkDateBissexto($novaDataInicio)
            || Portabilis_Date_Utils::checkDateBissexto($novaDataFim)) {
          $existeBissexto = true;
        }
      }

      if ($existeBissexto) {
        $tabela .= "<tr bgcolor='#FCF8E3' style='color: #8A6D3B; font-weight:normal;'>
                      <td align='center'><b>Observação:</b></td>
                      <td align='center'>A data 29/02/$this->ref_ano não poderá ser migrada pois $this->ref_ano não é um ano bissexto, portanto será substituída por 28/02/$this->ref_ano.</td>
                    </tr>";
      }

      $tabela .="</table>";
      $tabela .= "<tr><td colspan='2'><b> Adicione os módulos abaixo para {$this->ref_ano} semelhante ao exemplo do ano anterior: </b></td></tr><tr><td>";
      $tabela .= "</table>";
    }



    $ref_ano_ = $this->ref_ano;
    $this->campoTexto('ref_ano_', 'Ano', $ref_ano_, 4, 4, FALSE, FALSE, FALSE,
      '', '', '', '', TRUE);

    $this->campoQuebra();
    if ($tabela)
      $this->campoRotulo('modulosAnoAnterior', '-', $tabela);

    $this->campoQuebra();

    // Novo módulos do ano letivo

    $opcoesCampoModulo = array('' => 'Selecione');
    if (class_exists("clsPmieducarModulo")) {
      $objTemp = new clsPmieducarModulo();
      $objTemp->setOrderby('nm_tipo ASC');

      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, 1, $ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoesCampoModulo[$registro['cod_modulo']] = $registro['nm_tipo'];
        }
      }
    }
    else {
      $opcoes = array('' => 'Erro na gera��o');
    }

    // data
    if ($qtd_modulo > 1) {
      $this->campoLista('ref_cod_modulo', 'M�dulo', $opcoes,
        $this->ref_cod_modulo, NULL, NULL, NULL, NULL, NULL, FALSE);

      $this->campoData('data_inicio', 'Data In�cio', $this->data_inicio);

      $this->campoData('data_fim', 'Data Fim', $this->data_fim);
    }
    else {
      $this->campoLista('ref_cod_modulo', 'M�dulo', $opcoes, $this->ref_cod_modulo);
      $this->campoData('data_inicio', 'Data In�cio', $this->data_inicio, TRUE);
      $this->campoData('data_fim', 'Data Fim', $this->data_fim, TRUE);
    }

    $this->campoOculto('incluir_modulo', '');
    $this->campoRotulo('bt_incluir_modulo', 'M�dulo',
     '<a href="#" id="add_module"><img src="imagens/nvp_bot_adiciona.gif" title="Incluir" border="0" /></a>'
    );

  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {

      $this->copiarTurmasUltimoAno($this->ref_ref_cod_escola, $this->ref_ano);

      $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola,
        $this->ref_ano, $this->pessoa_logada, NULL, 0, NULL, NULL, 1, 1
      );

      $cadastrou = $obj->cadastra();

      if ($cadastrou) {

        foreach ($this->ref_cod_modulo as $key => $campo) {
          $this->data_inicio[$key]  = dataToBanco($this->data_inicio[$key]);
          $this->data_fim[$key]     = dataToBanco($this->data_fim[$key]);
          if ($this->dias_letivos[$key] == '')
            $this->dias_letivos[$key] = '0';
          $obj = new clsPmieducarAnoLetivoModulo($this->ref_ano,
            $this->ref_ref_cod_escola, $key+1,
            $this->ref_cod_modulo[$key], $this->data_inicio[$key],
            $this->data_fim[$key], $this->dias_letivos[$key]
          );

          $cadastrou1 = $obj->cadastra();

          if (! $cadastrou1) {
            $this->mensagem = 'Cadastro n�o realizado.<br />';
            return FALSE;
          }
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        header('Location: educar_escola_det.php?cod_escola='.$this->ref_ref_cod_escola.'#ano_letivo');

        die();
      }

      $this->mensagem = 'Cadastro n�o realizado. <br />';
      return FALSE;
    }

    echo '<script>alert("� necess�rio adicionar pelo menos um m�dulo!")</script>';
    $this->mensagem = 'Cadastro n�o realizado.<br />';
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
      $obj  = new clsPmieducarAnoLetivoModulo($this->ref_ano, $this->ref_ref_cod_escola);
      $excluiu = $obj->excluirTodos();

      if ($excluiu) {

        foreach ($this->ref_cod_modulo as $key => $campo) {
          $this->data_inicio[$key] = dataToBanco($this->data_inicio[$key]);
          $this->data_fim[$key]    = dataToBanco($this->data_fim[$key]);
          if ($this->dias_letivos[$key] == '')
            $this->dias_letivos[$key] = '0';
          $obj = new clsPmieducarAnoLetivoModulo($this->ref_ano,
            $this->ref_ref_cod_escola, $key+1,
            $this->ref_cod_modulo[$key], $this->data_inicio[$key],
            $this->data_fim[$key], $this->dias_letivos[$key]
          );

          $cadastrou1 = $obj->cadastra();

          if (! $cadastrou) {
            $this->mensagem = 'Edi��o n�o realizada.<br />';
            return FALSE;
          }
        }

        $this->mensagem .= 'Edi��o efetuada com sucesso.<br />';
        header('Location: educar_escola_lst.php');
        die();
      }
    }

    echo "<script>alert('� necess�rio adicionar pelo menos um m�dulo!')</script>";
    $this->mensagem = 'Edi��o n�o realizada.<br />';
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    $obj = new clsPmieducarEscolaAnoLetivo($this->ref_ref_cod_escola,
      $this->ref_ano, NULL, $this->pessoa_logada, NULL, NULL, NULL, 0);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $obj  = new clsPmieducarAnoLetivoModulo($this->ref_ano, $this->ref_ref_cod_escola);
      $excluiu1 = $obj->excluirTodos();

      if ($excluiu1) {
        $this->mensagem .= 'Exclus�o efetuada com sucesso.<br />';
        header('Location: educar_escola_lst.php');
        die();
      }

      $this->mensagem = 'Exclus�o n�o realizada.<br />';
      return FALSE;
    }

    $this->mensagem = 'Exclus�o n�o realizada.<br />';
    return FALSE;
  }

  /**
   * Verifica se ao menos uma das datas as datas de in�cio dos m�dulos � do
   * mesmo ano letivo da escola. Em caso de erro, configura a mensagem de
   * erro que � retornado pelo formul�rio.
   *
   * @access private
   * @param  array $modulos O array associativo recebido via POST pelo formul�rio.
   * @return bool  FALSE caso nenhuma das datas esteja no mesmo ano letivo da escola.
   */
  function _verificaModuloDatas(array $modulos)
  {
    $dates = array();
    foreach ($modulos as $modulo) {
      $dates[] = $modulo['data_inicio_'];
    }

    try {
      App_Date_Utils::datesYearAtLeast($dates, $this->ref_ano, 1);
    }
    catch (App_Date_Exception $e) {
      $this->mensagem = $e->getMessage();
      return FALSE;
    }

    return TRUE;
  }

  function copiarTurmasUltimoAno($escolaId, $anoDestino) {
    $sql       = 'select ano, turmas_por_ano from pmieducar.escola_ano_letivo where ref_cod_escola = $1 ' .
                 'and ativo = 1 and ano in (select max(ano) from pmieducar.escola_ano_letivo where ' .
                 'ref_cod_escola = $1 and ativo = 1)';

    $ultimoAnoLetivo = Portabilis_Utils_Database::selectRow($sql, $escolaId);

    $anoTurmasPorAno = $ultimoAnoLetivo['turmas_por_ano'] == 1 ? $ultimoAnoLetivo['ano'] : null;

    $turmasEscola    = new clsPmieducarTurma();
    $turmasEscola    = $turmasEscola->lista(null, null, null, null, $escolaId, null, null, null,
                                            null, null, null, null, null, null, 1, null, null,
                                            null, null, null, null, null, null, null, null, null,
                                            null, null, null, null, null, false, null, true, null,
                                            null, $anoTurmasPorAno);

    foreach ($turmasEscola as $turma)
      $this->copiarTurma($turma, $ultimoAnoLetivo['ano'], $anoDestino);
  }

  function copiarTurma($turmaOrigem, $anoOrigem, $anoDestino) {
    $sql = "select 1 from turma where ativo = 1 and visivel = true
            and ref_ref_cod_escola = $1 and nm_turma = $2 and ref_ref_cod_serie = $3 and ano = $4 limit 1";

    $params = array(
      $turmaOrigem['ref_ref_cod_escola'],
      $turmaOrigem['nm_turma'],
      $turmaOrigem['ref_ref_cod_serie'],
      $anoDestino
    );

    $existe = Portabilis_Utils_Database::selectField($sql, $params);

    if ($existe != 1) {
      $fields = array('ref_usuario_exc', 'ref_usuario_cad', 'ref_ref_cod_serie', 'ref_ref_cod_escola',
                      'ref_cod_infra_predio_comodo', 'nm_turma', 'sgl_turma', 'max_aluno', 'multiseriada',
                      'data_cadastro', 'data_exclusao', 'ativo', 'ref_cod_turma_tipo', 'hora_inicial', 'hora_final',
                      'hora_inicio_intervalo', 'hora_fim_intervalo', 'ref_cod_regente', 'ref_cod_instituicao_regente',
                      'ref_cod_instituicao', 'ref_cod_curso', 'ref_ref_cod_serie_mult', 'ref_ref_cod_escola_mult',
                      'visivel', 'turma_turno_id', 'tipo_boletim', 'ano', 'dias_semana', 'atividades_complementares',
                      'atividades_aee', 'turma_unificada', 'tipo_atendimento', 'etapa_educacenso',
                      'cod_curso_profissional', 'tipo_mediacao_didatico_pedagogico',
                      'nao_informar_educacenso', 'turma_mais_educacao');

      $turmaDestino = new clsPmieducarTurma();

      foreach ($fields as $fieldName)
        $turmaDestino->$fieldName = $turmaOrigem[$fieldName];

      $turmaDestino->ano = $anoDestino;
      $turmaDestinoId    = $turmaDestino->cadastra();

      $this->copiarComponenteCurricularTurma($turmaOrigem['cod_turma'], $turmaDestinoId);
      $this->copiarModulosTurma($turmaOrigem['cod_turma'], $turmaDestinoId, $anoOrigem, $anoDestino);
    }
  }

  function copiarComponenteCurricularTurma($turmaOrigemId, $turmaDestinoId) {
    $dataMapper             = new ComponenteCurricular_Model_TurmaDataMapper();
    $componentesTurmaOrigem = $dataMapper->findAll(array(), array('turma' => $turmaOrigemId));

    foreach ($componentesTurmaOrigem as $componenteTurmaOrigem) {
      $data = array(
        'componenteCurricular' => $componenteTurmaOrigem->get('componenteCurricular'),
        'escola'               => $componenteTurmaOrigem->get('escola'),
        'cargaHoraria'         => $componenteTurmaOrigem->get('cargaHoraria'),
        'turma'                => $turmaDestinoId,

        // est� sendo mantido o mesmo ano_escolar_id, uma vez que n�o foi
        // foi encontrado de onde o valor deste campo � obtido.
        'anoEscolar'           => $componenteTurmaOrigem->get('anoEscolar')
      );

      $componenteTurmaDestino = $dataMapper->createNewEntityInstance($data);
      $dataMapper->save($componenteTurmaDestino);
    }
  }

  function copiarModulosTurma($turmaOrigemId, $turmaDestinoId, $anoOrigem, $anoDestino) {
    $modulosTurmaOrigem = new clsPmieducarTurmaModulo();
    $modulosTurmaOrigem = $modulosTurmaOrigem->lista($turmaOrigemId);

    foreach ($modulosTurmaOrigem as $moduloOrigem) {
      $moduloDestino = new clsPmieducarTurmaModulo();

      $moduloDestino->ref_cod_modulo = $moduloOrigem['ref_cod_modulo'];
      $moduloDestino->sequencial     = $moduloOrigem['sequencial'];
      $moduloDestino->ref_cod_turma  = $turmaDestinoId;

      $moduloDestino->data_inicio    = str_replace(
        $anoOrigem, $anoDestino, $moduloOrigem['data_inicio']
      );

      $moduloDestino->data_fim       = str_replace(
        $anoOrigem, $anoDestino, $moduloOrigem['data_fim']
      );

      if (Portabilis_Date_Utils::checkDateBissexto($moduloDestino->data_inicio)) {
        $moduloDestino->data_inicio = str_replace(29, 28, $moduloDestino->data_inicio);
      }

      if (Portabilis_Date_Utils::checkDateBissexto($moduloDestino->data_fim)) {
        $moduloDestino->data_fim = str_replace(29, 28, $moduloDestino->data_fim);
      }

      $moduloDestino->cadastra();
    }
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
 * Realiza valida��o client-side do forComponenteCurricular_Model_TurmaDataMappermul�rio.
 */
function incluir()
{
  var phpjs     = ied_phpjs.getInstance();
  var startDate = null;
  var endDate   = null;

  startDate = document.getElementById('data_inicio').value.split('/');
  endDate   = document.getElementById('data_fim').value.split('/');

  if ('' === document.getElementById('ref_cod_modulo').value) {
    alert('� necess�rio selecionar um "m�dulo".');
    return false;
  }

  if (!phpjs.checkdate(startDate[1], startDate[0], startDate[2])) {
    document.getElementById('data_inicio').className = 'formdestaque';
    alert('Preencha o campo "Data In�cio" corretamente!');
    document.getElementById('data_inicio').focus();
    return false;
  }

  if (!phpjs.checkdate(endDate[1], endDate[0], endDate[2])) {
    document.getElementById('data_fim').className = 'formdestaque';
    alert('Preencha o campo "Data Fim" corretamente!');
    document.getElementById('data_fim').focus();
    return false;
  }

  startDate = new Date(parseInt(startDate[2], 10), parseInt(startDate[1], 10) - 1, parseInt(startDate[0], 10));
  endDate   = new Date(parseInt(endDate[2], 10), parseInt(endDate[1], 10) - 1, parseInt(endDate[0], 10));

  if (endDate < startDate) {
    alert('"Data In�cio" n�o pode ser posterior a "Data Fim".');
    return false;
  }

  document.getElementById('incluir_modulo').value = 'S';
  document.getElementById('tipoacao').value = '';
  acao();
}

</script>
