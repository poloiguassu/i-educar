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

class clsIndexBase extends clsBase {
  public function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Vagas Reservadas');
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

  // Atributos de mapeamento da tabela pmieducar.reserva_vaga
  var
    $cod_reserva_vaga   = NULL,
    $ref_ref_cod_escola = NULL,
    $ref_ref_cod_serie  = NULL,
    $ref_usuario_exc    = NULL,
    $ref_usuario_cad    = NULL,
    $ref_cod_aluno      = NULL,
    $data_cadastro      = NULL,
    $data_exclusao      = NULL,
    $ativo              = NULL;

  /**
   * Atributos para apresenta��o
   * @var mixed
   */
  var
    $ref_cod_escola      = NULL,
    $ref_cod_curso       = NULL,
    $ref_cod_instituicao = NULL,
    $nm_aluno            = NULL;

  /**
   * Sobrescreve clsListagem::Gerar().
   * @see clsListagem::Gerar()
   */
  function Gerar()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Vagas Reservadas - Listagem';

    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    

    $lista_busca = array(
      'Aluno',
      'Eixo',
      'Projeto'
    );

    // Recupera n��vel de acesso do usu�rio logado
    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      $lista_busca[] = 'Escola';
      $lista_busca[] = 'Institui&ccedil;&atilde;o';
    }
    elseif ($nivel_usuario == 2) {
      $lista_busca[] = 'Escola';
    }
    $this->addCabecalhos($lista_busca);

    // Lista de op��ees para o formul�rio de pesquisa r�pida
    $get_escola = TRUE;
    $get_curso  = TRUE;
    $get_escola_curso_serie = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    // Refer�ncia de escola
    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }
    elseif (isset($_GET['ref_cod_escola'])) {
      $this->ref_ref_cod_escola = intval($_GET['ref_cod_escola']);
    }

    // Refer�ncia de s�rie
    if ($this->ref_cod_serie) {
      $this->ref_ref_cod_serie = $this->ref_cod_serie;
    }
    elseif (isset($_GET['ref_cod_serie'])) {
      $this->ref_ref_cod_serie = intval($_GET['ref_cod_serie']);
    }

    // Campos do formul�rio
    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE, FALSE,
      FALSE, '', '<img border="0" onclick="pesquisa_aluno();" id="ref_cod_aluno_lupa" name="ref_cod_aluno_lupa" src="imagens/lupa.png" />');

    // C�digo do aluno (retornado de pop-up de busca da pesquisa de alunos - lupa)
    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET["pagina_{$this->nome}"] ?
      ($_GET["pagina_{$this->nome}"] * $this->limite - $this->limite)
      : 0;

    // Inst�ncia objeto de mapeamento relacional com o tabela pmieducar.reserva_vaga
    $obj_reserva_vaga = new clsPmieducarReservaVaga();
    $obj_reserva_vaga->setOrderby('data_cadastro ASC');
    $obj_reserva_vaga->setLimite($this->limite, $this->offset);

    // Lista os registros usando os valores passados pelos filtros
    $lista = $obj_reserva_vaga->lista(
      $this->cod_reserva_vaga,
      $this->ref_ref_cod_escola,
      $this->ref_ref_cod_serie,
      NULL,
      NULL,
      $this->ref_cod_aluno,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $this->ref_cod_instituicao,
      $this->ref_cod_curso
    );

    // Pega o total de registros encontrados
    $total = $obj_reserva_vaga->_total;

    // Itera sobre resultados montando a lista de apresenta��o
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        // Recupera nome da s�rie da reserva de vaga
        $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $nm_serie  = $det_serie['nm_serie'];

        // Recupera o nome do curso da reserva de vaga
        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        // Recupera o nome da escola da reserva de vaga
        $obj_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
        $det_escola = $obj_escola->detalhe();
        $nm_escola = $det_escola['nome'];

        // Recupera o nome da institui��o da reserva de vaga
        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        /*
         * Se for um aluno previamente cadastrado, procuramos seu nome, primeiro
         * buscando a refer�ncia de Pessoa e depois pesquisando a tabela para
         * carregar o nome
         */
        if ($registro['ref_cod_aluno']) {
          // Pesquisa por aluno para pegar o identificador de Pessoa
          $obj_aluno = new clsPmieducarAluno($registro['ref_cod_aluno']);
          $det_aluno = $obj_aluno->detalhe();
          $ref_idpes = $det_aluno['ref_idpes'];

          // Pesquisa a tabela de pessoa para recuperar o nome
          $obj_pessoa = new clsPessoa_($ref_idpes);
          $det_pessoa = $obj_pessoa->detalhe();
          $registro['ref_cod_aluno'] = $det_pessoa['nome'];
        }
        else {
          $registro['ref_cod_aluno'] = $registro['nm_aluno'] . ' (aluno externo)';
        }

        // Array de dados formatados para apresenta��o
        $lista_busca = array(
          "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_aluno"]}</a>",
          "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_serie}</a>",
          "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_curso"]}</a>"
        );

        // Verifica por permiss�es
        if ($nivel_usuario == 1) {
          $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_escola}</a>";
          $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$registro["ref_cod_instituicao"]}</a>";
        }
        elseif ($nivel_usuario == 2) {
          $lista_busca[] = "<a href=\"educar_reservada_vaga_det.php?cod_reserva_vaga={$registro["cod_reserva_vaga"]}\">{$nm_escola}</a>";
        }

        $this->addLinhas($lista_busca);
      }
    }

    $this->addPaginador2('educar_reservada_vaga_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Listagem de vagas reservadas"
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

<script type="text/javascript">
document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}

function pesquisa_aluno() {
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}
</script>