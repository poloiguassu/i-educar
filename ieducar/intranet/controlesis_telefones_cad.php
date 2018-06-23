<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Agenda telefonica" );
		$this->processoAp = "611";
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

	var $cod_telefones;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $nome;
	var $ddd_numero;
	var $numero;
	var $responsavel;
	var $ddd_celular;
	var $celular;
	var $email;
	var $endereco;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_telefones=$_GET["cod_telefones"];


		if( is_numeric( $this->cod_telefones ) )
		{

			$obj = new clsPmicontrolesisTelefones( $this->cod_telefones );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_telefones_det.php?cod_telefones={$registro["cod_telefones"]}" : "controlesis_telefones_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_telefones", $this->cod_telefones );

		// foreign keys

		// text
		$this->campoTexto( "nome", "Institui��o", $this->nome, 30, 255, true );
		$this->campoTexto( "responsavel", "Respons�vel", $this->responsavel, 30, 255, true );
		$this->inputTelefone('numero', 'Telefone');
		$this->inputTelefone('celular', 'Celular');
		$this->campoTexto( "email", "Email", $this->email, 30, 255, false );
		$this->campoTexto( "endereco", "Endere�o", $this->endereco, 30, 255, false );

		// data

	}
	
	protected function inputTelefone($type, $typeLabel = '')
	{
		if (! $typeLabel)
			$typeLabel = "Telefone {$type}";

		// ddd

		$options = array(
			'required'    => false,
			'label'       => "(ddd) / {$typeLabel}",
			'placeholder' => 'ddd',
			'value'       => $this->{"ddd_{$type}"},
			'max_length'  => 3,
			'size'        => 3,
			'inline'      => true
		);

		$this->inputsHelper()->integer("ddd_{$type}", $options);


		// telefone

		$options = array(
			'required'    => false,
			'label'       => '',
			'placeholder' => $typeLabel,
			'value'       => $this->{"{$type}"},
			'max_length'  => 11
		);

		$this->inputsHelper()->integer("{$type}", $options);
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisTelefones( $this->cod_telefones, $this->pessoa_logada, null, $this->nome, $this->ddd_numero, $this->numero, null, null, 1, $this->ddd_celular, $this->celular, $this->responsavel, $this->email, $this->endereco);
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: controlesis_telefones_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n�o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisTelefones\nvalores obrigatorios\nis_numeric( $this->ref_funcionario_cad ) && is_string( $this->nome )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisTelefones($this->cod_telefones, null, $this->pessoa_logada, $this->nome, $this->ddd_numero, $this->numero, null, null, 1, $this->ddd_celular, $this->celular, $this->responsavel, $this->email, $this->endereco);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi��o efetuada com sucesso.<br>";
			header( "Location: controlesis_telefones_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi��o n�o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisTelefones\nvalores obrigatorios\nif( is_numeric( $this->cod_telefones ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmicontrolesisTelefones($this->cod_telefones, null, $this->pessoa_logada, $this->nome, $this->ddd_numero, $this->numero, null, null, 0, $this->ddd_celular, $this->celular, $this->responsavel, $this->email, $this->endereco);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus�o efetuada com sucesso.<br>";
			header( "Location: controlesis_telefones_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus�o n�o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisTelefones\nvalores obrigatorios\nif( is_numeric( $this->cod_telefones ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
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
