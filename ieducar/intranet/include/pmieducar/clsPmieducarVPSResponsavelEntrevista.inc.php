<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 16/08/2006 15:03 pelo gerador automatico de classes
*/

require_once("include/pmieducar/geral.inc.php");

class clsPmieducarVPSResponsavelEntrevista
{
	var $cod_vps_responsavel_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_responsavel;
	var $email;
	var $ddd_telefone_com;
	var $telefone_com;
	var $ddd_telefone_cel;
	var $telefone_cel;
	var $observacao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_escola;
	var $ref_idpes;

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
	function clsPmieducarVPSResponsavelEntrevista($cod_vps_responsavel_entrevista = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $nm_responsavel = null, $email = null, $ddd_telefone_com = null, $telefone_com = null, $ddd_telefone_cel = null, $telefone_cel = null, $observacao = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_escola = null, $ref_idpes = null)
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}vps_responsavel_entrevista";

		$this->_campos_lista = $this->_todos_campos = "cod_vps_responsavel_entrevista, ref_usuario_exc, ref_usuario_cad, nm_responsavel, email, ddd_telefone_com, telefone_com, ddd_telefone_cel, telefone_cel, observacao, data_cadastro, data_exclusao, ativo, ref_idpes, ref_cod_escola";

		if(is_numeric($ref_cod_escola))
		{
			if(class_exists("clsPmieducarEscola"))
			{
				$tmp_obj = new clsPmieducarEscola($ref_cod_escola);
				
				if(method_exists($tmp_obj, "existe"))
				{
					if($tmp_obj->existe())
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
				else if(method_exists($tmp_obj, "detalhe"))
				{
					if($tmp_obj->detalhe())
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
			}
			else
			{
				if($db->CampoUnico("SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'"))
				{
					$this->ref_cod_escola = $ref_cod_escola;
				}
			}
		}
		
		if(is_numeric($ref_idpes))
		{
			if(class_exists("clsPessoaJuridica"))
			{
				$tmp_obj = new clsPessoaJuridica($ref_idpes);
				
				if(method_exists($tmp_obj, "existe"))
				{
					if($tmp_obj->existe())
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
				else if(method_exists($tmp_obj, "detalhe"))
				{
					if($tmp_obj->detalhe())
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
			}
			else
			{
				if($db->CampoUnico("SELECT 1 FROM pmieducar.escola WHERE idpes = '{$ref_idpes}'"))
				{
					$this->ref_idpes = $ref_idpes;
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


		if(is_numeric($cod_vps_responsavel_entrevista))
		{
			$this->cod_vps_responsavel_entrevista = $cod_vps_responsavel_entrevista;
		}
		if(is_string($nm_responsavel))
		{
			$this->nm_responsavel = $nm_responsavel;
		}

		if(is_string($email))
		{
			$this->email = $email;
		}
		
		if(is_numeric($ddd_telefone_com) && is_numeric($telefone_com))
		{
			$this->ddd_telefone_com = $ddd_telefone_com;
			$this->telefone_com = $telefone_com;
		}

		if(is_numeric($ddd_telefone_cel) && is_numeric($telefone_cel))
		{
			$this->ddd_telefone_cel = $ddd_telefone_cel;
			$this->telefone_cel = $telefone_cel;
		}

		if(is_string($observacao))
		{
			$this->observacao = $observacao;
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
		if(is_numeric($this->ref_usuario_cad) && is_string($this->nm_responsavel) && is_numeric($this->ref_cod_escola) && is_numeric($this->ref_idpes))
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if(is_numeric($this->ref_usuario_cad))
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}

			if(is_string($this->nm_responsavel))
			{
				$campos .= "{$gruda}nm_responsavel";
				$valores .= "{$gruda}'{$this->nm_responsavel}'";
				$gruda = ", ";
			}
			
			if(is_string($this->email))
			{
				$campos .= "{$gruda}email";
				$valores .= "{$gruda}'{$this->email}'";
				$gruda = ", ";
			}
			
			if(is_numeric($this->ddd_telefone_com) && is_numeric($this->telefone_com))
			{
				$campos .= "{$gruda}ddd_telefone_com";
				$valores .= "{$gruda}'{$this->ddd_telefone_com}'";
				$gruda = ", ";
				$campos .= "{$gruda}telefone_com";
				$valores .= "{$gruda}'{$this->telefone_com}'";
				$gruda = ", ";
			}

			if(is_numeric($this->ddd_telefone_cel) && is_numeric($this->telefone_cel))
			{
				$campos .= "{$gruda}ddd_telefone_cel";
				$valores .= "{$gruda}'{$this->ddd_telefone_cel}'";
				$gruda = ", ";
				$campos .= "{$gruda}telefone_cel";
				$valores .= "{$gruda}'{$this->telefone_cel}'";
				$gruda = ", ";
			}

			if(is_string($this->observacao))
			{
				$campos .= "{$gruda}observacao";
				$valores .= "{$gruda}'{$this->observacao}'";
				$gruda = ", ";
			}

			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";

			if(is_numeric($this->ref_cod_escola))
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}

			if(is_numeric($this->ref_idpes))
			{
				$campos .= "{$gruda}ref_idpes";
				$valores .= "{$gruda}'{$this->ref_idpes}'";
				$gruda = ", ";
			}

			$db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES($valores)");
			return $db->InsertId("{$this->_tabela}_cod_vps_responsavel_entrevista_seq");
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
		if(is_numeric($this->cod_vps_responsavel_entrevista) && is_numeric($this->ref_usuario_exc))
		{
			$db = new clsBanco();
			$set = "";

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

			if(is_string($this->nm_responsavel))
			{
				$set .= "{$gruda}nm_responsavel = '{$this->nm_responsavel}'";
				$gruda = ", ";
			}

			if(is_string($this->email))
			{
				$set .= "{$gruda}email = '{$this->email}'";
				$gruda = ", ";
			}
			
			if(is_numeric($this->ddd_telefone_com))
			{
				$set .= "{$gruda}ddd_telefone_com = '{$this->ddd_telefone_com}'";
				$gruda = ", ";
			}

			if(is_numeric($this->telefone_com))
			{
				$set .= "{$gruda}telefone_com = '{$this->telefone_com}'";
				$gruda = ", ";
			}

			if(is_numeric($this->ddd_telefone_cel))
			{
				$set .= "{$gruda}ddd_telefone_cel = '{$this->ddd_telefone_cel}'";
				$gruda = ", ";
			}

			if(is_numeric($this->telefone_cel))
			{
				$set .= "{$gruda}telefone_cel = '{$this->telefone_cel}'";
				$gruda = ", ";
			}

			if(is_string($this->observacao))
			{
				$set .= "{$gruda}observacao = '{$this->observacao}'";
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

			if(is_numeric($this->ref_cod_escola))
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
				$gruda = ", ";
			}

			if(is_numeric($this->ref_idpes))
			{
				$set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
				$gruda = ", ";
			}

			if($set)
			{
				$db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_vps_responsavel_entrevista = '{$this->cod_vps_responsavel_entrevista}'");
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
	function lista($int_cod_vps_responsavel_entrevista = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_responsavel = null, $str_observacao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_escola = null, $int_ref_idpes = null, $int_ref_cod_instituicao = null)
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} aa ";
		$filtros = "";

		$whereAnd = " WHERE ";

		if(is_numeric($int_cod_vps_responsavel_entrevista))
		{
			$filtros .= "{$whereAnd} cod_vps_responsavel_entrevista = '{$int_cod_vps_responsavel_entrevista}'";
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
		if(is_string($str_nm_responsavel))
		{
			$str_nm_responsavel = addslashes($str_nm_responsavel);
			$filtros .= "{$whereAnd} nm_responsavel ILIKE ('%{$str_nm_responsavel}%')";
			$whereAnd = " AND ";
		}
		if(is_string($str_observacao))
		{
			$filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
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
		if(is_numeric($int_ref_cod_escola))
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_ref_idpes))
		{
			$filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
			$whereAnd = " AND ";
		}
		if(is_numeric($int_ref_cod_instituicao))
		{
			$whereAnd2 = "";
			$filtros2 = "";

			if(is_numeric($int_ref_cod_instituicao))
			{
				$filtros2 .= "{$whereAnd2}b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
				$whereAnd2 = " AND ";
			}
			$filtros .= "{$whereAnd} AND {$filtros2})";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count(explode(",", $this->_campos_lista));
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} aa {$filtros}");

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
		if(is_numeric($this->cod_vps_responsavel_entrevista))
		{
			$db = new clsBanco();
			$db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_vps_responsavel_entrevista = '{$this->cod_vps_responsavel_entrevista}'");
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
		if(is_numeric($this->cod_vps_responsavel_entrevista))
		{
			$db = new clsBanco();
			$db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_vps_responsavel_entrevista = '{$this->cod_vps_responsavel_entrevista}'");
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
		if(is_numeric($this->cod_vps_responsavel_entrevista) && is_numeric($this->ref_usuario_exc))
		{
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
