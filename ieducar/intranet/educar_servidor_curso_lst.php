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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Projeto');
    $this->processoAp         = 0;
    $this->renderBanner       = FALSE;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
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

  var $cod_servidor;
  var $ref_cod_instituicao;
  var $ref_idesco;
  var $ref_cod_funcao;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_curso;
  var $ref_cod_disciplina;
  var $cursos_servidor;


  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();

    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

    if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
      $obj = new clsPmieducarServidor($this->cod_servidor, NULL, NULL, NULL, NULL,
        NULL, NULL, $this->ref_cod_instituicao);

      $registro  = $obj->detalhe();

      if ($registro) {
        $retorno = 'Editar';
      }
    }

    @session_start();
    $this->cursos_servidor = $_SESSION['cursos_servidor'];
    @session_write_close();

    if (!$this->cursos_servidor) {
      $obj_servidor_curso = new clsPmieducarServidorCursoMinistra();

      $lst_servidor_curso = $obj_servidor_curso->lista(NULL,
        $this->ref_cod_instituicao, $this->cod_servidor);

      if ($lst_servidor_curso) {
        foreach ($lst_servidor_curso as $curso) {
          $this->cursos_servidor[$curso['ref_cod_curso']] = $curso['ref_cod_curso'];
        }
      }
    }

    return $retorno;
  }

  function Gerar()
  {
    $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
    $opcoes = $opcoes_curso = array('' => 'Selecione');

    $obj_cursos = new clsPmieducarCurso();
    $obj_cursos->setOrderby('nm_curso');

    $lst_cursos = $obj_cursos->lista( NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, 1, NULL, $this->ref_cod_instituicao);

    if ($lst_cursos) {
      foreach ($lst_cursos as $curso) {
        $opcoes_curso[$curso['cod_curso']] = $curso['nm_curso'];
      }
    }

    $arr_valores = array();

    if ($this->cursos_servidor) {
      foreach ($this->cursos_servidor as $curso) {
        $arr_valores[] = array($curso);
      }
    }

    $this->campoTabelaInicio('cursos_ministra', 'Cursos Ministrados',
      array('Projeto'), $arr_valores, '');

    $this->campoLista('ref_cod_curso', 'Projeto', $opcoes_curso, $this->ref_cod_curso,
      '', '', '', '');

    $this->campoTabelaFim();
  }

  function Novo()
  {
    $curso_servidor = array();
    if ($this->ref_cod_curso) {
      foreach ($this->ref_cod_curso as $curso) {
        $curso_servidor[$curso] = $curso;
      }
    }

    @session_start();
    $_SESSION['cursos_servidor'] = $curso_servidor;
    $_SESSION['cod_servidor']    = $this->cod_servidor;
    @session_write_close();

    echo "<script>parent.fechaExpansivel( '{$_GET['div']}');</script>";
    die();
  }

  public function Editar()
  {
    return $this->Novo();
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

// Atribui o conte�do �� p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();