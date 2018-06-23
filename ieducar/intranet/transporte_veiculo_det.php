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
require_once 'include/modules/clsModulesVeiculo.inc.php';

require_once 'Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';


/**
 * clsIndexBase class.21239
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Veiculos');
    $this->processoAp = 21237;
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

    $this->titulo = 'Veiculo - Detalhe';


    $cod_veiculo = $_GET['cod_veiculo'];

    $tmp_obj = new clsModulesVeiculo($cod_veiculo);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
      header('Location: transporte_veiculo_lst.php');
      die();
    }
    
    $this->addDetalhe( array("C�digo do ve�culo", $cod_veiculo));
    $this->addDetalhe( array("Descri��o", $registro['descricao']) );
    $this->addDetalhe( array("Placa", $registro['placa']) );
    $this->addDetalhe( array("Renavam", $registro['renavam']) );
    $this->addDetalhe( array("Chassi", $registro['chassi']) );
    $this->addDetalhe( array("Marca", $registro['marca']) );
    $this->addDetalhe( array("Ano fabrica��o", $registro['ano_fabricacao']) );
    $this->addDetalhe( array("Ano modelo", $registro['ano_modelo']) );
    $this->addDetalhe( array("Limite de passageiros", $registro['passageiros']) );
    $malha ='';
    switch ($registro['malha']){
      case 'A':
        $malha = 'Aqu�tica/Embarca��o';
        break;
      case 'F':
        $malha = 'Ferrovi�ria';
        break;
      case 'R':
        $malha = 'Rodovi�ria';
        break;
    }
    $this->addDetalhe( array("Malha", $malha) );
    $this->addDetalhe( array("Categoria", $registro['descricao_tipo']) );
    $this->addDetalhe( array("Exclusivo para transporte escolar", ($registro['exclusivo_transporte_escolar'] == 'S' ? 'Sim' : 'N�o')) );
    $this->addDetalhe( array("Adaptado para pessoas com necessidades especiais", ($registro['adaptado_necessidades_especiais'] == 'S' ? 'Sim' : 'N�o')) );
    $this->addDetalhe( array("Ativo", ($registro['ativo'] == 'S' ? 'Sim' : 'N�o')) );
    if ($registro['ativo']=='N')
      $this->addDetalhe( array("Descri��o inativo", $registro['descricao_inativo']) );
    $this->addDetalhe( array("Empresa", $registro['nome_empresa']) );
    $this->addDetalhe( array("Motorista respons�vel", $registro['nome_motorista']) );
    $this->addDetalhe( array("Observa&ccedil;&atilde;o", $registro['observacao']));
    $this->url_cancelar = "transporte_veiculo_lst.php";

    $this->largura = "100%";

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(21237, $this->pessoa_logada,7,null,true))
    {
      $this->url_novo = "../module/TransporteEscolar/Veiculo";
      $this->url_editar = "../module/TransporteEscolar/Veiculo?id={$cod_veiculo}";
    }

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Detalhe do ve&iacute;culo"
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
