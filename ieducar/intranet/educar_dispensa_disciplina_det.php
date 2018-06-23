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

require_once "include/clsBase.inc.php";
require_once "include/clsDetalhe.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Dispensa Componente Curricular');
    $this->processoAp = 578;
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
class indice extends clsDetalhe
{
  var $titulo;

  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $ref_cod_serie;
  var $ref_cod_escola;
  var $ref_cod_disciplina;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_tipo_dispensa;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $observacao;
  var $ref_sequencial;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Dispensa Componente Curricular - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
    $this->ref_cod_matricula  = $_GET['ref_cod_matricula'];
    $this->ref_cod_serie      = $_GET['ref_cod_serie'];
    $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
    $this->ref_cod_escola     = $_GET['ref_cod_escola'];

    $tmp_obj = new clsPmieducarDispensaDisciplina($this->ref_cod_matricula,
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_disciplina);

    $registro = $tmp_obj->detalhe();

    if (!$registro) {
      header('Location: educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);
      die();
    }

    if (class_exists('clsPmieducarSerie')) {
      $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
      $det_serie = $obj_serie->detalhe();
      $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];
    }
    else {
      $registro['ref_ref_cod_serie'] = 'Erro na geracao';
      echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
    }

    // Dados da matr�cula
    $obj_ref_cod_matricula = new clsPmieducarMatricula();
    $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

    $obj_aluno = new clsPmieducarAluno();
    $det_aluno = array_shift($obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],
       NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
    $det_escola = $obj_escola->detalhe();

    $nm_aluno = $det_aluno['nome_aluno'];

    // Dados do curso
    $obj_ref_cod_curso = new clsPmieducarCurso($detalhe_aluno['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    // Tipo de dispensa
    $obj_ref_cod_tipo_dispensa = new clsPmieducarTipoDispensa($registro['ref_cod_tipo_dispensa']);
    $det_ref_cod_tipo_dispensa = $obj_ref_cod_tipo_dispensa->detalhe();
    $registro['ref_cod_tipo_dispensa'] = $det_ref_cod_tipo_dispensa['nm_tipo'];

    if ($registro['ref_cod_matricula']) {
      $this->addDetalhe(array('Matricula', $registro['ref_cod_matricula']));
    }

    if ($nm_aluno) {
      $this->addDetalhe(array('Aluno', $nm_aluno));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Projeto', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('S�rie', $registro['ref_ref_cod_serie']));
    }

    if ($registro['ref_cod_disciplina']) {
      $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
      $componente = $componenteMapper->find($registro['ref_cod_disciplina']);
      $this->addDetalhe(array('Componente Curricular', $componente->nome));
    }

    if ($registro['ref_cod_tipo_dispensa']) {
      $this->addDetalhe(array('Tipo Dispensa', $registro['ref_cod_tipo_dispensa']));
    }

    if ($registro['observacao']) {
      $this->addDetalhe(array('Observa��o', $registro['observacao']));
    }

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      $this->url_novo   = sprintf('educar_dispensa_disciplina_cad.php?ref_cod_matricula=%d',
        $this->ref_cod_matricula);
      $this->url_editar = sprintf('educar_dispensa_disciplina_cad.php?ref_cod_matricula=%d&ref_cod_disciplina=%d',
        $registro['ref_cod_matricula'], $registro['ref_cod_disciplina']);
    }

    $this->url_cancelar = 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
    $this->largura      = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe da dispensas de disciplina"
    ));
    $this->enviaLocalizacao($localizacao->montar());
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