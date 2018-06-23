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
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/View/Helper/Application.php';

require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Itiner�rio" );
		$this->processoAp = "21238";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_rota;
    var $descricao;

// INCLUI NOVO
    var $pontos;
    var $ref_cod_ponto_transporte_escolar;
    var $hora;
    var $tipo;
    var $ref_cod_veiculo;

//------INCLUI DISCIPLINA------//
    var $historico_disciplinas;
    var $nm_disciplina;
    var $nota;
    var $faltas;
    var $excluir_disciplina;
    var $ultimo_sequencial;

    var $aceleracao;

    function Inicializar()
    {
        $retorno = "Editar";
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_rota=$_GET["cod_rota"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 21238, $this->pessoa_logada, 7,  "transporte_rota_det.php?cod_rota={$this->cod_rota}" );
        $volta = false;
        if( is_numeric( $this->cod_rota ))
        {
            $obj = new clsModulesRotaTransporteEscolar( $this->cod_rota );
            $registro  = $obj->detalhe();
            if( $registro )
                $this->descricao = $registro['descricao'];
            else
                $volta = true;
        }else
            $volta = true;


        if ($volta){
            header('Location: transporte_rota_lst.php');
            die();
        }
        $this->url_cancelar = "transporte_rota_det.php?cod_rota={$this->cod_rota}";
        $this->nome_url_cancelar = "Cancelar";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""                                  => "Editar itiner&aacute;rio"
    ));
    $this->enviaLocalizacao($localizacao->montar());		

		return $retorno;
	}

	function Gerar()
	{

		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( !$this->$campo ) ?  $val : $this->$campo ;

		$this->campoRotulo("cod_rota","C�digo da rota" ,$this->cod_rota);
		$this->campoRotulo("descricao","Rota", $this->descricao );
	
		$this->campoQuebra();

		if( is_numeric( $this->cod_rota) && !$_POST)
		{
			$obj = new clsModulesItinerarioTransporteEscolar();
			$obj->setOrderby(" seq ASC");
			$registros = $obj->lista(null, $this->cod_rota);
			$qtd_pontos = 0;
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$this->pontos[$qtd_pontos][] = $campo["ref_cod_ponto_transporte_escolar"].' - '.$campo["descricao"];
					$this->pontos[$qtd_pontos][] = $campo["hora"];
					$this->pontos[$qtd_pontos][] = $campo["tipo"];
					$this->pontos[$qtd_pontos][] = $campo["ref_cod_veiculo"].' - '.$campo["nome_onibus"];
					$qtd_pontos++;
				}
			}
		}

		$this->campoTabelaInicio("pontos","Itiner�rio",array("Ponto (Requer pr�-cadastro)<br/> <spam style=\" font-weight: normal; font-size: 10px;\">Digite o c�digo ou nome do ponto e selecione o desejado</spam>","Hora","Tipo","Ve�culo (Requer pr�-cadastro)<br/> <spam style=\" font-weight: normal; font-size: 10px;\">Digite o c�digo, nome ou placa do ve�culo e selecione o desejado</spam>" ),$this->pontos);

		$this->campoTexto( "ref_cod_ponto_transporte_escolar", "Ponto (Requer pr�-cadastro)", $this->ref_cod_ponto_transporte_escolar, 50, 255, false, true, false, '', '', '', 'onfocus' );

		$this->campoHora( "hora", "Hora", $this->hora);
		$this->campoLista( "tipo", "Tipo", array( '' => "Selecione", 'I' => 'Ida', 'V' => 'Volta'),$this->tipo );
		$this->campoTexto( "ref_cod_veiculo", "Ve�culo", $this->ref_cod_veiculo, 50, 255, false, false, false, '', '', '', 'onfocus' );
		$this->campoTabelaFim();

		$this->campoQuebra();  

 	   	$style = "/modules/Portabilis/Assets/Stylesheets/Frontend.css";
 	   	Portabilis_View_Helper_Application::loadStylesheet($this, $style);


		Portabilis_View_Helper_Application::loadJQueryLib($this);
		Portabilis_View_Helper_Application::loadJQueryUiLib($this);

		Portabilis_View_Helper_Application::loadJavascript(
			$this,
			array('/modules/Portabilis/Assets/Javascripts/Utils.js',
						'/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js',
						'/modules/Portabilis/Assets/Javascripts/Validator.js')
		);
		$this->addBotao('Excluir todos',"transporte_itinerario_del.php?cod_rota={$this->cod_rota}");

	}

	function Novo()
	{
		return true;
	}

	function Editar()
	{


		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 21238, $this->pessoa_logada, 7,  "transporte_rota_det.php?cod_rota={$this->cod_rota}" );

		if ($this->ref_cod_ponto_transporte_escolar)
		{
			
			$obj  = new clsModulesItinerarioTransporteEscolar();
			$excluiu = $obj->excluirTodos( $this->cod_rota );
			if ( $excluiu )
			{
				$sequencial = 1;
				foreach ( $this->ref_cod_ponto_transporte_escolar AS $key => $ponto )
				{
				
					$obj = new clsModulesItinerarioTransporteEscolar(NULL, $this->cod_rota, $sequencial, $this->retornaCodigo($ponto), $this->retornaCodigo($this->ref_cod_veiculo[$key]),
     				$this->hora[$key], $this->tipo[$key]);
					$cadastrou1 = $obj->cadastra();
					if( !$cadastrou1 )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						return false;
					}
					$sequencial++;

				}
			}
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: transporte_rota_det.php?cod_rota={$this->cod_rota}" );
			die();
			return true;

		}

	}

	function Excluir()
	{
		 return true;
	}
	
	protected function retornaCodigo($palavra){
		
		return substr($palavra, 0, strpos($palavra, " -"));
	}

	protected function fixupFrequencia($frequencia) {
		if (strpos($frequencia, ',')) {
			$frequencia = str_replace('.', '',  $frequencia);
			$frequencia = str_replace(',', '.', $frequencia);
		}

		return $frequencia;
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

<script type="text/javascript">

    // autocomplete disciplina fields

  var handleSelect = function(event, ui){
        $j(event.target).val(ui.item.label);
        return false;
    };

    var search = function(request, response) {
        var searchPath = '/module/Api/Ponto?oper=get&resource=ponto-search';
        var params     = { query : request.term };

        $j.get(searchPath, params, function(dataResponse) {
            simpleSearch.handleSearch(dataResponse, response);
        });
    };

    var searchV = function(request, response) {
        var searchPath = '/module/Api/Veiculo?oper=get&resource=veiculo-search';
        var params     = { query : request.term };

        $j.get(searchPath, params, function(dataResponse) {
            simpleSearch.handleSearch(dataResponse, response);
        });
    };

    function setAutoComplete() {
        $j.each($j('input[id^="ref_cod_ponto_transporte_escolar"]'), function(index, field) {

            $j(field).autocomplete({
                source    : search,
                select    : handleSelect,
                minLength : 1,
                autoFocus : true
            });

        });
        $j.each($j('input[id^="ref_cod_veiculo"]'), function(index, field) {

            $j(field).autocomplete({
                source    : searchV,
                select    : handleSelect,
                minLength : 1,
                autoFocus : true
            });

        });
    }

    setAutoComplete();

    document.onclick = function(event) {
        var targetElement = event.target;
        if ( targetElement.value == " Cancelar " ) {

            var cod_rota = $j('#cod_rota').val();
            location.href="transporte_rota_det.php?cod_rota="+cod_rota;
        } else if(targetElement.value == "Excluir todos"){
            var cod_rota = $j('#cod_rota').val();
            if(confirm('Este procedimento irá excluir todos os pontos do itinerário. Tem certeza que deseja continuar?')){
                location.href="transporte_itinerario_del.php?cod_rota="+cod_rota;
            }
        }
    };

    var submitForm = function(event) {
        // Esse formUtils.submit() chama o Editar();
        // Mais à frente bolar uma validação aqui
    /*  var $frequenciaField = $j('#frequencia');
        var frequencia       = $frequenciaField.val();

	document.onclick = function(event) {
	    var targetElement = event.target;
	    if ( targetElement.value == " Cancelar " ) {
        
	       	var cod_rota = $j('#cod_rota').val();
	       	location.href="transporte_rota_det.php?cod_rota="+cod_rota;
	    } else if(targetElement.value == "Excluir todos"){
	    	var cod_rota = $j('#cod_rota').val();
	    	if(confirm('Este procedimento ir� excluir todos os pontos do itiner�rio. Tem certeza que deseja continuar?')){
	    		location.href="transporte_itinerario_del.php?cod_rota="+cod_rota;
	    	}
	    }
	};

      if (validatesIfNumericValueIsInRange(frequencia, $frequenciaField, 0, 100))*/
        formUtils.submit();
    }


    // bind events

    var $addPontosButton = $j('#btn_add_tab_add_1');

    $addPontosButton.click(function(){
        setAutoComplete();
    });


    // submit button

    var $submitButton = $j('#btn_enviar');

    $submitButton.removeAttr('onclick');
    $submitButton.click(submitForm);

</script>
