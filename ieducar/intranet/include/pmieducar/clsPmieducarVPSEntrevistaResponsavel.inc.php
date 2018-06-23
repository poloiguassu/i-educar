<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarVPSEntrevistaResponsavel
{
	var $ref_cod_vps_responsavel_entrevista;
	var $ref_cod_vps_entrevista;
	var $principal;

	// propriedades padrao

	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;

	/**
	 * Nome do schema
	 *
	 * @var string
	 */
	var $_schema;

	/**
	 * Nome da tabela
	 *
	 * @var string
	 */
	var $_tabela;

	/**
	 * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
	 *
	 * @var string
	 */
	var $_campos_lista;

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;

	/**
	 * Valor que define a quantidade de registros a ser retornada pelo metodo lista
	 *
	 * @var int
	 */
	var $_limite_quantidade;

	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	var $_limite_offset;

	/**
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_order_by;


	/**
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmieducarVPSEntrevistaResponsavel( $ref_cod_vps_responsavel_entrevista = null, $ref_cod_vps_entrevista = null, $principal = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}vps_entrevista_responsavel";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_vps_responsavel_entrevista, ref_cod_vps_entrevista, principal";

		if( is_numeric( $ref_cod_vps_entrevista ) )
		{
			if( class_exists( "clsPmieducarVPSEntrevista" ) )
			{
				$tmp_obj = new clsPmieducarVPSEntrevista( $ref_cod_vps_entrevista );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.entrevista WHERE cod_vps_entrevista = '{$ref_cod_vps_entrevista}'" ) )
				{
					$this->ref_cod_vps_entrevista = $ref_cod_vps_entrevista;
				}
			}
		}
		if( is_numeric( $ref_cod_vps_responsavel_entrevista ) )
		{
			if( class_exists( "clsPmieducarVPSResponsavelEntrevista" ) )
			{
				$tmp_obj = new clsPmieducarVPSResponsavelEntrevista( $ref_cod_vps_responsavel_entrevista );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_vps_responsavel_entrevista = $ref_cod_vps_responsavel_entrevista;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_vps_responsavel_entrevista = $ref_cod_vps_responsavel_entrevista;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.vps_entrevista_responsavel WHERE cod_vps_entrevista_responsavel = '{$ref_cod_vps_responsavel_entrevista}'" ) )
				{
					$this->ref_cod_vps_responsavel_entrevista = $ref_cod_vps_responsavel_entrevista;
				}
			}
		}


		if( is_numeric( $principal ) )
		{
			$this->principal = $principal;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_vps_responsavel_entrevista ) && is_numeric( $this->ref_cod_vps_entrevista ) && is_numeric( $this->principal ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_vps_responsavel_entrevista ) )
			{
				$campos .= "{$gruda}ref_cod_vps_responsavel_entrevista";
				$valores .= "{$gruda}'{$this->ref_cod_vps_responsavel_entrevista}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_vps_entrevista ) )
			{
				$campos .= "{$gruda}ref_cod_vps_entrevista";
				$valores .= "{$gruda}'{$this->ref_cod_vps_entrevista}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->principal ) )
			{
				$campos .= "{$gruda}principal";
				$valores .= "{$gruda}'{$this->principal}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return true;
		}
		return false;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		if( is_numeric( $this->ref_cod_vps_responsavel_entrevista ) && is_numeric( $this->ref_cod_vps_entrevista ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->principal ) )
			{
				$set .= "{$gruda}principal = '{$this->principal}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_vps_responsavel_entrevista = null, $int_ref_cod_vps_entrevista = null, $int_principal = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_vps_responsavel_entrevista ) )
		{
			$filtros .= "{$whereAnd} ref_cod_vps_responsavel_entrevista = '{$int_ref_cod_vps_responsavel_entrevista}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_vps_entrevista ) )
		{
			$filtros .= "{$whereAnd} ref_cod_vps_entrevista = '{$int_ref_cod_vps_entrevista}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_principal ) )
		{
			$filtros .= "{$whereAnd} principal = '{$int_principal}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->ref_cod_vps_responsavel_entrevista ) && is_numeric( $this->ref_cod_vps_entrevista ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function existe()
	{
		if( is_numeric( $this->ref_cod_vps_responsavel_entrevista ) && is_numeric( $this->ref_cod_vps_entrevista ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		if( is_numeric( $this->ref_cod_vps_responsavel_entrevista ) && is_numeric( $this->ref_cod_vps_entrevista ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_vps_responsavel_entrevista = '{$this->ref_cod_vps_responsavel_entrevista}' AND ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
		return true;
		*/


		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a um tipo de avaliacao
	 */
	function  excluirTodos()
	{
		if ( is_numeric( $this->ref_cod_vps_entrevista ) ) {
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_vps_entrevista = '{$this->ref_cod_vps_entrevista}'" );
			return true;
		}
		return false;
	}

	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista( $str_campos )
	{
		$this->_campos_lista = $str_campos;
	}

	/**
	 * Define que o metodo Lista devera retornoar todos os campos da tabela
	 *
	 * @return null
	 */
	function resetCamposLista()
	{
		$this->_campos_lista = $this->_todos_campos;
	}

	/**
	 * Define limites de retorno para o metodo lista
	 *
	 * @return null
	 */
	function setLimite( $intLimiteQtd, $intLimiteOffset = null )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		$this->_limite_offset = $intLimiteOffset;
	}

	/**
	 * Retorna a string com o trecho da query resposavel pelo Limite de registros
	 *
	 * @return string
	 */
	function getLimite()
	{
		if( is_numeric( $this->_limite_quantidade ) )
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if( is_numeric( $this->_limite_offset ) )
			{
				$retorno .= " OFFSET {$this->_limite_offset} ";
			}
			return $retorno;
		}
		return "";
	}

	/**
	 * Define campo para ser utilizado como ordenacao no metolo lista
	 *
	 * @return null
	 */
	function setOrderby( $strNomeCampo )
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();

		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_order_by = $strNomeCampo;
		}
	}

	/**
	 * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
	 *
	 * @return string
	 */
	function getOrderby()
	{
		if( is_string( $this->_campo_order_by ) )
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}

}
?>
