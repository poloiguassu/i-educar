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
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/public/geral.inc.php" );

require_once("include/modules/clsModulesEmpresaTransporteEscolar.inc.php");
require_once("include/modules/clsModulesVeiculo.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Ve�culos" );
		$this->processoAp = "21237";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $__pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $__titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $__limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $__offset;

	var $cod_veiculo;
	var $descricao;
	var $placa;
	var $renavam;
	var $marca;
	var $ativo;
	var $cod_empresa;
	var $nome_motorista;

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "Ve�culos - Listagem";

		foreach( $_GET AS $var => $val ) 
			$this->$var = ( $val === "" ) ? null: $val;

		

		$this->addCabecalhos( array(
			"C�digo do ve�culo",
			"Descri��o",
			"Placa",
			"Marca",
			"Empresa",
			"Motorista respons�vel"
		) );

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		
		$objTemp = new clsModulesEmpresaTransporteEscolar();
		$objTemp->setOrderby(' nome_empresa ASC');
		$lista = $objTemp->lista();
		if ( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista as $registro )
			{
				$opcoes["{$registro['cod_empresa_transporte_escolar']}"] = "{$registro['nome_empresa']}";
			}
		}else{
			$opcoes = array( "" => "Sem empresas cadastradas" );
		}

		$this->campoNumero('cod_veiculo','C�digo do ve�culo',$this->cod_veiculo,29,15);
		$this->campoTexto( "descricao", "Descri��o", $this->descricao, 29, 50, false );
		$this->campoTexto( "placa", "Placa", $this->placa, 29, 10, false );
		$this->campoTexto( "renavam", "Renavam", $this->renavam, 29, 30, false );
		$this->campoTexto( "marca", "Marca", $this->marca, 29, 50, false );

		$this->campoLista( "ativo", "Ativo", array( null => 'Selecione', 'S' => 'Ativo', 'N' => 'Inativo'), $this->ativo, "", false, "", "", false, false );
		$this->campoLista( "cod_empresa", "Empresa", $opcoes, $this->cod_empresa, "", false, "", "", false, false );
		$this->campoTexto( "nome_motorista", "Motorista respons�vel", $this->nome_motorista, 29, 30, false );


		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj = new clsModulesVeiculo();
		$obj->setOrderby( " descricao ASC" );
		$obj->setLimite( $this->__limite, $this->__offset );

		$lista = $obj->lista(
			$this->cod_veiculo,
			$this->descricao,
			$this->placa,
			$this->renavam,
			$this->nome_motorista,
			$this->cod_empresa,
			$this->marca,
			$this->ativo

		);

		$total = $obj->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$this->addLinhas( array(
					"<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["cod_veiculo"]}</a>",
					"<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["descricao"]}</a>",
					"<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["placa"]}</a>",
					"<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["marca"]}</a>",
					"<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["nome_empresa"]}</a>",
					"<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["nome_motorista"]}</a>"
				) );
			}
		}
		
		$this->addPaginador2( "transporte_veiculo_lst.php", $total, $_GET, $this->nome, $this->__limite );

		$this->acao = "go(\"/module/TransporteEscolar/Veiculo\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Listagem de ve&iacute;culos"
    ));
    $this->enviaLocalizacao($localizacao->montar());		
	}
}
// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
