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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesMotorista.inc.php';

require_once 'Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';


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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Motoristas');
    $this->processoAp = 21236;
    $this->addEstilo('localizacaoSistema');
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

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    // Verifica��o de permiss�o para cadastro.
    $this->obj_permissao = new clsPermissoes();

    $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

    $this->titulo = 'Motorista - Detalhe';


    $cod_motorista = $_GET['cod_motorista'];

    $tmp_obj = new clsModulesMotorista($cod_motorista);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
      header('Location: transporte_motorista_lst.php');
      die();
    }
    
    $this->addDetalhe( array("C�digo do motorista", $cod_motorista));
    $this->addDetalhe( array("Nome", $registro['nome_motorista'].'<br/> <a target=\'_blank\' style=\' text-decoration: underline;\' href=\'atendidos_det.php?cod_pessoa='.$registro['ref_idpes'].'\'>Visualizar pessoa</a>') );
    $this->addDetalhe( array("CNH", $registro['cnh']) );
    $this->addDetalhe( array("Categoria", $registro['tipo_cnh']) );
    if (trim($registro['dt_habilitacao'])!='')
      $this->addDetalhe( array("Data da habilita��o", Portabilis_Date_Utils::pgSQLToBr($registro['dt_habilitacao']) ));
    if (trim($registro['vencimento_cnh'])!='')
      $this->addDetalhe( array("Vencimento da habilita��o", Portabilis_Date_Utils::pgSQLToBr($registro['vencimento_cnh']) ) );

    $this->addDetalhe( array("Observa&ccedil;&atilde;o", $registro['observacao']));
    $this->url_cancelar = "transporte_motorista_lst.php";

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21236, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Motorista";
      $this->url_editar = "../module/TransporteEscolar/motorista?id={$cod_motorista}";
    }

    $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe do motorista"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}

// Instancia o objeto da p�gina
$pagina = new clsIndexBase();

// Instancia o objeto de conte�do
$miolo = new indice();

// Passa o conte�do para a p�gina
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
