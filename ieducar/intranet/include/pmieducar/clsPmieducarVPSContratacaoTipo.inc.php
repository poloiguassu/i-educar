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

require_once("include/pmieducar/geral.inc.php");

class clsPmieducarVPSContratacaoTipo
{
	var $cod_vps_tipo_contratacao;
	var $ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

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
	function clsPmieducarVPSContratacaoTipo($cod_vps_tipo_contratacao = null, $ref_cod_instituicao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_tipo = null, $descricao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null)
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}vps_tipo_contratacao";

		$this->_campos_lista = $this->_todos_campos = "cod_vps_tipo_contratacao, ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, nm_tipo, descricao, data_cadastro, data_exclusao, ativo";

		if(is_numeric($ref_cod_instituicao))
		{
			if(class_exists("clsPmieducarInstituicao"))
			{
				$tmp_obj = new clsPmieducarInstituicao($ref_cod_instituicao);
				if(method_exists($tmp_obj, "existe"))
				{
					if($tmp_obj->existe())
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
				else if(method_exists($tmp_obj, "detalhe"))
				{
					if($tmp_obj->detalhe())
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
			}
			else
			{
				if($db->CampoUnico("SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'"))
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}
		if(is_numeric($ref_usuario_exc))
		{
			if(class_exists("clsPmieducarUsuario"))
			{
				$tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
				if(method_exists($tmp_obj, "existe"))
				{
					if($tmp_obj->existe())
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
				else if(method_exists($tmp_obj, "detalhe"))
				{
					if($tmp_obj->detalhe())
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
			}
			else
			{
				if($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'"))
				{
					$this->ref_usuario_exc = $ref_usuario_exc;
				}
			}
		}
		if(is_numeric($ref_usuario_cad))
		{
			if(class_exists("clsPmieducarUsuario"))
			{
				$tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
				if(method_exists($tmp_obj, "existe"))
				{
					if($tmp_obj->existe())
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				else if(method_exists($tmp_obj, "detalhe"))
				{
					if($tmp_obj->detalhe())
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			}
			else
			{
				if($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'"))
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}


		if(is_numeric($cod_vps_tipo_contratacao))
		{
			$this->cod_vps_tipo_contratacao = $cod_vps_tipo_contratacao;
		}
		if(is_string($nm_tipo))
		{
			$this->nm_tipo = $nm_tipo;
		}
		if(is_string($descricao))
		{
			$this->descricao = $descricao;
		}
		if(is_string($data_cadastro))
		{
			$this->data_cadastro = $data_cadastro;
		}
		if(is_string($data_exclusao))
		{
			$this->data_exclusao = $data_exclusao;
		}
		if(is_numeric($ativo))
		{
			$this->ativo = $ativo;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if(is_numeric($this->ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) && is_string($this->nm_tipo))
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if(is_numeric($this->ref_cod_instituicao))
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if(is_numeric($this->ref_usuario_cad))
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if(is_string($this->nm_tipo))
			{
				$campos .= "{$gruda}nm_tipo";
				$valores .= "{$gruda}'{$this->nm_tipo}'";
				$gruda = ", ";
			}
			if(is_string($this->descricao))
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");
			return $db->InsertId("{$this->_tabela}_cod_vps_tipo_contratacao_seq");
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
		if(is_numeric($this->cod_vps_tipo_contratacao) && is_numeric($this->ref_usuario_exc))
		{

			$db = new clsBanco();
			$set = "";

			if(is_numeric($this->ref_cod_instituicao))
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if(is_numeric($this->ref_usuario_exc))
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if(is_numeric($this->ref_usuario_cad))
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if(is_string($this->nm_tipo))
			{
				$set .= "{$gruda}nm_tipo = '{$this->nm_tipo}'";
				$gruda = ", ";
			}
			if(is_string($this->descricao))
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if(is_string($this->data_cadastro))
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if(is_numeric($this->ativo))
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if($set)
			{
				$db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_vps_tipo_contratacao = '{$this->cod_vps_tipo_contratacao}'");
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
	function lista($int_cod_vps_tipo_contratacao = null, $int_ref_cod_instituicao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_tipo = null, $str_descricao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null)
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";

		if(is_numeric($int_cod_vps_tipo_contratacao))
		{
			$filtros .= "{$whereAnd} cod_vps_tipo_contratacao = '{$int_cod_vps_tipo_contratacao}'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_ref_cod_instituicao))
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_ref_usuario_exc))
		{
			$filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_ref_usuario_cad))
		{
			$filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if(is_string($str_nm_tipo))
		{
			$filtros .= "{$whereAnd} nm_tipo LIKE '%{$str_nm_tipo}%'";
			$whereAnd = " AND ";
		}
		if(is_string($str_descricao))
		{
			$filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if(is_string($date_data_cadastro_ini))
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if(is_string($date_data_cadastro_fim))
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if(is_string($date_data_exclusao_ini))
		{
			$filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if(is_string($date_data_exclusao_fim))
		{
			$filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if(is_null($int_ativo) || $int_ativo)
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = '0'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count(explode(",", $this->_campos_lista));
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

		$db->Consulta($sql);

		if($countCampos > 1)
		{
			while ($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if(count($resultado))
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
		if(is_numeric($this->cod_vps_tipo_contratacao))
		{

		$db = new clsBanco();
		$db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_vps_tipo_contratacao = '{$this->cod_vps_tipo_contratacao}'");
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
		if(is_numeric($this->cod_vps_tipo_contratacao))
		{

		$db = new clsBanco();
		$db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_vps_tipo_contratacao = '{$this->cod_vps_tipo_contratacao}'");
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
		if(is_numeric($this->cod_vps_tipo_contratacao) && is_numeric($this->ref_usuario_exc))
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_vps_tipo_contratacao = '{$this->cod_vps_tipo_contratacao}'");
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista($str_campos)
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
	function setLimite($intLimiteQtd, $intLimiteOffset = null)
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
		if(is_numeric($this->_limite_quantidade))
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if(is_numeric($this->_limite_offset))
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
	function setOrderby($strNomeCampo)
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();

		if(is_string($strNomeCampo) && $strNomeCampo)
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
		if(is_string($this->_campo_order_by))
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}

}
?>
