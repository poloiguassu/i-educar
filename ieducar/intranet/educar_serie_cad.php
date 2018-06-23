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
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

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
class indice extends clsCadastro
{
  var $pessoa_logada;

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

  var $ref_cod_instituicao;

  var $disciplina_serie;
  var $ref_cod_disciplina;
  var $incluir_disciplina;
  var $excluir_disciplina;

  var $idade_inicial;
  var $idade_ideal;
  var $idade_final;

  var $regra_avaliacao_id;
  var $regra_avaliacao_diferenciada_id;

  var $alerta_faixa_etaria;
  var $bloquear_matricula_faixa_etaria;
  var $exigir_inep;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_serie=$_GET['cod_serie'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3,
      'educar_serie_lst.php');

    if (is_numeric($this->cod_serie)) {
      $obj = new clsPmieducarSerie($this->cod_serie);
      $registro  = $obj->detalhe();

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $obj_curso_det = $obj_curso->detalhe();
        $this->ref_cod_instituicao = $obj_curso_det['ref_cod_instituicao'];
        $this->fexcluir = $obj_permissoes->permissao_excluir(583,
          $this->pessoa_logada,3);

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == "Editar") ?
      "educar_serie_det.php?cod_serie={$registro["cod_serie"]}" :
      "educar_serie_lst.php";

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "{$nomeMenu} s&eacute;rie"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = "Cancelar";

    $this->alerta_faixa_etaria  = dbBool($this->alerta_faixa_etaria);
    $this->bloquear_matricula_faixa_etaria  = dbBool($this->bloquear_matricula_faixa_etaria);
    $this->exigir_inep  = dbBool($this->exigir_inep);

    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach($_POST as $campo => $val) {
        $this->$campo = ($this->$campo) ? $this->$campo : $val;
      }
    }

    // primary keys
    $this->campoOculto("cod_serie", $this->cod_serie);

    $obrigatorio = TRUE;
    $get_curso = TRUE;
    include('include/pmieducar/educar_campo_lista.php');

    $this->campoTexto("nm_serie", "Eixo", $this->nm_serie, 30, 255, TRUE);

    $opcoes = array("" => "Selecione");

    if ($this->ref_cod_curso) {
      $objTemp = new clsPmieducarCurso();
      $lista = $objTemp->lista($this->ref_cod_curso, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes_["{$registro['cod_curso']}"] = "{$registro['qtd_etapas']}";
        }
      }

      for ($i=1; $i <= $opcoes_["{$registro['cod_curso']}"]; $i++) {
        $opcoes[$i] = "Etapa {$i}";
      }
    }

    $this->campoLista('etapa_curso', 'Etapa Projeto', $opcoes, $this->etapa_curso);

    // Regra de avalia��o
    $mapper = new RegraAvaliacao_Model_RegraDataMapper();
    $regras = array();
    if (!is_null($this->ref_cod_instituicao)) {
      $regras = $mapper->findAll(array(),
        array('instituicao' => $this->ref_cod_instituicao)
      );
      $regras = CoreExt_Entity::entityFilterAttr($regras, 'id', 'nome');
    }

    $this->campoLista('regra_avaliacao_id', 'Regra Avalia��o', $regras, $this->regra_avaliacao_id);

    $opcoes = array('' => 'Selecione', 1 => 'n&atilde;o', 2 => 'sim');

    $this->campoLista('concluinte', 'Concluinte', $opcoes, $this->concluinte);

    $this->campoMonetario('carga_horaria', 'Carga Hor&aacute;ria', $this->carga_horaria, 7, 7, TRUE);

    $this->campoNumero('dias_letivos', 'Dias letivos', $this->dias_letivos, 3, 3, TRUE);

    $this->campoNumero('idade_ideal', 'Idade padrão', $this->idade_ideal, 2, 2, false);

    $this->campoNumero('idade_inicial', 'Faixa et&aacute;ria', $this->idade_inicial,
      2, 2, FALSE, '', '', FALSE, FALSE, TRUE);

    $this->campoNumero('idade_final', '&nbsp;at�', $this->idade_final, 2, 2, FALSE);

		$this->campoMemo( "observacao_historico", "Observa&ccedil;&atilde;o hist�rico", $this->observacao_historico, 60, 5, false );
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->carga_horaria = str_replace(".", "", $this->carga_horaria);
    $this->carga_horaria = str_replace(",", ".", $this->carga_horaria);

    $obj = new clsPmieducarSerie(NULL, NULL, $this->pessoa_logada, $this->ref_cod_curso,
      $this->nm_serie, $this->etapa_curso, $this->concluinte, $this->carga_horaria,
      NULL, NULL, 1, $this->idade_inicial, $this->idade_final,
      $this->regra_avaliacao_id, $this->observacao_historico, $this->dias_letivos,
      $this->regra_avaliacao_diferenciada_id, !is_null($this->alerta_faixa_etaria), !is_null($this->bloquear_matricula_faixa_etaria), $this->idade_ideal, !is_null($this->exigir_inep));

    $this->cod_serie = $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $serie = new clsPmieducarSerie($this->cod_serie);
      $serie = $serie->detalhe();

      $auditoria = new clsModulesAuditoriaGeral("serie", $this->pessoa_logada, $this->cod_serie);
      $auditoria->inclusao($serie);

      $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
      header("Location: educar_serie_lst.php");
      die();
    }

    $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
    echo "<!--\nErro ao cadastrar clsPmieducarSerie\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_curso ) && is_string( $this->nm_serie ) && is_numeric( $this->etapa_curso ) && is_numeric( $this->concluinte ) && is_numeric( $this->carga_horaria ) \n-->";
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->carga_horaria = str_replace(".", "", $this->carga_horaria);
    $this->carga_horaria = str_replace(",", ".", $this->carga_horaria);

    $obj = new clsPmieducarSerie($this->cod_serie, $this->pessoa_logada, NULL,
      $this->ref_cod_curso, $this->nm_serie, $this->etapa_curso, $this->concluinte,
      $this->carga_horaria, NULL, NULL, 1, $this->idade_inicial,
      $this->idade_final, $this->regra_avaliacao_id, $this->observacao_historico, $this->dias_letivos,
      $this->regra_avaliacao_diferenciada_id, !is_null($this->alerta_faixa_etaria), !is_null($this->bloquear_matricula_faixa_etaria),$this->idade_ideal, !is_null($this->exigir_inep));

    $detalheAntigo = $obj->detalhe();
    $editou = $obj->edita();
    if ($editou) {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("serie", $this->pessoa_logada, $this->cod_serie);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);

      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      header("Location: educar_serie_lst.php");
      die();
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao editar clsPmieducarSerie\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_serie ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPmieducarSerie($this->cod_serie, $this->pessoa_logada, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

    $serie = $obj->detalhe();
    $excluiu = $obj->excluir();

    if ($excluiu) {
      $auditoria = new clsModulesAuditoriaGeral("serie", $this->pessoa_logada, $this->cod_serie);
      $auditoria->exclusao($serie);

      $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
      header( "Location: educar_serie_lst.php" );
      die();
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao excluir clsPmieducarSerie\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_serie ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
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
<script type="text/javascript">
function getRegra()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  var campoRegras = document.getElementById('regra_avaliacao_id');
  campoRegras.length = 1;
  campoRegras.disabled = true;
  campoRegras.options[0].text = 'Carregando regras';

  var campoRegrasDiferenciadas = document.getElementById('regra_avaliacao_diferenciada_id');
  campoRegrasDiferenciadas.length = 1;
  campoRegrasDiferenciadas.disabled = true;
  campoRegrasDiferenciadas.options[0].text = 'Carregando regras';

  var xml_qtd_etapas = new ajax(RegrasInstituicao);
  xml_qtd_etapas.envia("educar_serie_regra_xml.php?ins=" + campoInstituicao);
}

function EtapasCurso(xml_qtd_etapas)
{
  var campoEtapas = document.getElementById('etapa_curso');
  var DOM_array = xml_qtd_etapas.getElementsByTagName('curso');

  if (DOM_array.length) {
    campoEtapas.length = 1;
    campoEtapas.options[0].text = 'Selecione uma etapa';
    campoEtapas.disabled = false;

    var etapas;
    etapas = DOM_array[0].getAttribute("qtd_etapas");

    for (var i = 1; i<=etapas;i++) {
      campoEtapas.options[i] = new Option("Etapa "+i , i, false, false);
    }
  }
  else {
    campoEtapas.options[0].text = 'O curso n�o possui nenhuma etapa';
  }
}

function RegrasInstituicao(xml_qtd_regras)
{
  var campoRegras = document.getElementById('regra_avaliacao_id');
  var campoRegrasDiferenciadas = document.getElementById('regra_avaliacao_diferenciada_id');
  var DOM_array = xml_qtd_regras.getElementsByTagName('regra');

  if (DOM_array.length) {
    campoRegras.length = 1;
    campoRegras.options[0].text = 'Selecione uma regra';
    campoRegras.disabled = false;

    campoRegrasDiferenciadas.length = 1;
    campoRegrasDiferenciadas.options[0].text = 'Selecione uma regra';
    campoRegrasDiferenciadas.disabled = false;

    var loop = DOM_array.length;

    for (var i = 0; i < loop;i++) {
      campoRegras.options[i] = new Option(DOM_array[i].firstChild.data, DOM_array[i].id, false, false);
      campoRegrasDiferenciadas.options[i] = new Option(DOM_array[i].firstChild.data, DOM_array[i].id, false, false);
    }
  }
  else {
	  campoRegras.options[0].text = 'A institui��o n�o possui uma Regra de Avalia��o';
  }
}

document.getElementById('ref_cod_curso').onchange = function()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;

  var campoEtapas = document.getElementById('etapa_curso');
  campoEtapas.length = 1;
  campoEtapas.disabled = true;
  campoEtapas.options[0].text = 'Carregando etapas';

  var xml_qtd_etapas = new ajax(EtapasCurso);
  xml_qtd_etapas.envia("educar_curso_xml2.php?cur=" + campoCurso);
}

/**
 * Dispara eventos durante onchange da select ref_cod_instituicao.
 */
document.getElementById('ref_cod_instituicao').onchange = function()
{
  // Essa a��o � a padr�o do item, via include
  getCurso();

  // Requisi��o Ajax para as Regras de Avalia��o
  getRegra();
}
</script>
