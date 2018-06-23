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
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

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
class indice extends clsDetalhe
{
  var $titulo;

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
  var $regra_avaliacao_id;

  var $ref_cod_instituicao;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Eixo - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->cod_serie=$_GET["cod_serie"];

    $tmp_obj = new clsPmieducarSerie( $this->cod_serie );
    $registro = $tmp_obj->detalhe();

    if (!$registro) {
      header('Location: educar_serie_lst.php');
      die();
    }

    $obj_ref_cod_curso = new clsPmieducarCurso( $registro['ref_cod_curso'] );
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    $registro['ref_cod_instituicao'] = $det_ref_cod_curso['ref_cod_instituicao'];
    $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $obj_instituicao_det = $obj_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

    $obj_permissoes = new clsPermissoes();

    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      if ($registro['ref_cod_instituicao']) {
        $this->addDetalhe(array('Institui&ccedil;&atilde;o',
          $registro['ref_cod_instituicao']));
      }
    }

    if ( $registro['ref_cod_curso'] ) {
      $this->addDetalhe(array('Projeto', $registro['ref_cod_curso']));
    }

    if ($registro['nm_serie']) {
      $this->addDetalhe(array('Eixo', $registro['nm_serie']));
    }

    if ($registro['etapa_curso']) {
      $this->addDetalhe(array('Etapa Projeto', $registro['etapa_curso']));
    }

    if ($regraId = $registro['regra_avaliacao_id']) {
      $mapper = new RegraAvaliacao_Model_RegraDataMapper();
      $regra = $mapper->find($regraId);
      $this->addDetalhe(array('Regra Avalia��o', $regra));
    }

    if ($registro['concluinte']) {
      if ($registro['concluinte'] == 1) {
        $registro['concluinte'] = 'n&atilde;o';
      }
      else if ($registro['concluinte'] == 2) {
        $registro['concluinte'] = 'sim';
      }

      $this->addDetalhe(array('Concluinte', $registro['concluinte']));
    }

    if ($registro['carga_horaria']) {
      $this->addDetalhe(array('Carga Hor&aacute;ria', $registro['carga_horaria']));
    }

    $this->addDetalhe(array('Dias letivos', $registro['dias_letivos']));

    $this->addDetalhe(array('Idade padrão', $registro['idade_ideal']));

    if ($registro['observacao_historico']) {
      $this->addDetalhe(array('Observa��o hist�rico', $registro['observacao_historico']));
    }

    if ($obj_permissoes->permissao_cadastra(583, $this->pessoa_logada, 3)) {
      $this->url_novo = 'educar_serie_cad.php';
      $this->url_editar = "educar_serie_cad.php?cod_serie={$registro['cod_serie']}";
    }

    $this->url_cancelar = 'educar_serie_lst.php';
    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "Detalhe da s&eacute;rie"
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
