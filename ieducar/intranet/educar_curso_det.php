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
require_once 'include/clsDetalhe.inc.php';
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Projeto');
    $this->processoAp = '566';
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

  var $cod_curso;
  var $ref_usuario_cad;
  var $ref_cod_tipo_regime;
  var $ref_cod_nivel_ensino;
  var $ref_cod_tipo_ensino;
  var $nm_curso;
  var $sgl_curso;
  var $qtd_etapas;
  var $carga_horaria;
  var $ato_poder_publico;
  var $habilitacao;
  var $objetivo_curso;
  var $publico_alvo;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_usuario_exc;
  var $ref_cod_instituicao;
  var $padrao_ano_escolar;
  var $hora_falta;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Projeto - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->cod_curso = $_GET['cod_curso'];

    $tmp_obj = new clsPmieducarCurso( $this->cod_curso );
    $registro = $tmp_obj->detalhe();

    if (!$registro) {
      header('Location: educar_curso_lst.php');
      die();
    }

    $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $obj_instituicao_det = $obj_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

    $obj_ref_cod_tipo_regime = new clsPmieducarTipoRegime($registro['ref_cod_tipo_regime']);
    $det_ref_cod_tipo_regime = $obj_ref_cod_tipo_regime->detalhe();
    $registro['ref_cod_tipo_regime'] = $det_ref_cod_tipo_regime['nm_tipo'];

    $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino($registro['ref_cod_nivel_ensino']);
    $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
    $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

    $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino($registro['ref_cod_tipo_ensino']);
    $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
    $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      if ($registro['ref_cod_instituicao']) {
        $this->addDetalhe(array('Institui&ccedil;&atilde;o', $registro['ref_cod_instituicao']));
      }
    }

    if ($registro['ref_cod_tipo_regime']) {
      $this->addDetalhe(array('Tipo Regime', $registro['ref_cod_tipo_regime']));
    }

    if ($registro['ref_cod_nivel_ensino']) {
      $this->addDetalhe(array('N&iacute;vel Ensino', $registro['ref_cod_nivel_ensino']));
    }

    if ($registro['ref_cod_tipo_ensino']) {
      $this->addDetalhe(array('Tipo Ensino', $registro['ref_cod_tipo_ensino']));
    }

    if ($registro['nm_curso']) {
      $this->addDetalhe(array('Projeto', $registro['nm_curso']));
    }

    if ($registro['sgl_curso']) {
      $this->addDetalhe(array('Sigla Projeto', $registro['sgl_curso']));
    }

    if ($registro['qtd_etapas']) {
      $this->addDetalhe(array('Quantidade Etapas', $registro['qtd_etapas']));
    }

    if ($registro['hora_falta']) {
      $registro['hora_falta'] = number_format($registro['hora_falta'], 2, ',', '.');
      $this->addDetalhe(array('Hora/Falta', $registro['hora_falta']));
    }

    if ($registro['carga_horaria']) {
      $registro['carga_horaria'] = number_format($registro['carga_horaria'], 2, ',', '.');
      $this->addDetalhe(array('Carga Hor&aacute;ria', $registro['carga_horaria']));
    }

    if ($registro['ato_poder_publico']) {
      $this->addDetalhe(array('Ato Poder P&uacute;blico', $registro['ato_poder_publico']));
    }

    $obj = new clsPmieducarHabilitacaoCurso(NULL, $this->cod_curso);
    $lst = $obj->lista(NULL, $this->cod_curso);

    if ($lst) {
      $tabela = '<TABLE>
                 <TR align=center>
                     <TD bgcolor=#ccdce6><B>Nome</B></TD>
                 </TR>';
      $cont = 0;

      foreach ($lst as $valor) {
        if (($cont % 2) == 0) {
          $color = ' bgcolor=#f5f9fd ';
        }
        else {
          $color = ' bgcolor=#FFFFFF ';
        }

        $obj = new clsPmieducarHabilitacao($valor['ref_cod_habilitacao']);
        $obj_habilitacao = $obj->detalhe();
        $habilitacao = $obj_habilitacao['nm_tipo'];

        $tabela .= "<TR>
                  <TD {$color} align=left>{$habilitacao}</TD>
              </TR>";

        $cont++;
      }
      $tabela .= '</TABLE>';
    }

    if ($habilitacao) {
      $this->addDetalhe(array('Habilita&ccedil;&atilde;o', $tabela));
    }

    if ($registro['objetivo_curso']) {
      $this->addDetalhe( array('Objetivo Projeto', $registro['objetivo_curso']));
    }

    if ($registro['publico_alvo']) {
      $this->addDetalhe(array('P&uacute;blico Alvo', $registro['publico_alvo']));
    }

    if ($obj_permissoes->permissao_cadastra(566, $this->pessoa_logada, 3)) {
      $this->url_novo = 'educar_curso_cad.php';
      $this->url_editar = "educar_curso_cad.php?cod_curso={$registro["cod_curso"]}";
    }

    $this->url_cancelar = 'educar_curso_lst.php';
    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "Detalhe do curso"
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
