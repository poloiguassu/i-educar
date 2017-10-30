<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

/**
 * clsPreInscrito class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPreInscrito
{
	var $cod_inscrito;
	var $nome;
	var $data_nasc;
	var $cpf;
	var $rg;
	var $encaminhamento;
	var $sexo;
	var $serie;
	var $turno;
	var $egresso;
	var $indicacao;
	var $nm_responsavel;
	var $etapa_1;
	var $etapa_2;
	var $etapa_3;

	var $ref_usuario_cad;

	var $cod_escola;
	var $ref_cod_escola;
	var $ano;

	var $guarda_mirim;
	var $copia_rg;
	var $copia_cpf;
	var $copia_residencia;
	var $copia_historico;
	var $copia_renda;

	var $_tabela;
	var $_schema;

	/**
	* Construtor.
	*/
	function clsPreInscrito($int_cod_inscrito = NULL, $str_nome = NULL, $date_data_nasc = NULL,
		$numeric_cpf = NULL, $numeric_rg = NULL, $str_sexo = NULL, $int_serie = NULL, $int_turno = NULL,
		$int_egresso = NULL, $str_nm_responsavel = NULL, $int_cod_escola = NULL, $int_ano = NULL,
		$str_indicacao = NULL, $int_guarda_mirim = NULL, $int_copia_rg = NULL, $int_copia_cpf = NULL,
		$int_copia_residencia = NULL, $int_copia_historico = NULL, $int_copia_renda = NULL, $ref_usuario_cad = NULL)
	{
		if(is_numeric($int_cod_inscrito))
		{
			$this->cod_inscrito = $int_cod_inscrito;
		}

		$this->nome = $str_nome;
		$this->data_nasc = $date_data_nasc;

		if(is_numeric($this->cpf))
		{
			$this->cpf = $numeric_cpf;
		}

		if(is_numeric($this->rg))
		{
			$this->rg = $numeric_rg;
		}

		if (is_numeric($ref_usuario_cad)) {
			if (class_exists('clsPmieducarUsuario'))
			{
				$tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);

				if (method_exists($tmp_obj, 'existe'))
				{
					if ($tmp_obj->existe())
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				elseif (method_exists($tmp_obj, 'detalhe'))
				{
					if ($tmp_obj->detalhe())
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			} else {
				if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'"))
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}

		$this->sexo = $str_sexo;
		$this->serie = $int_serie;
		$this->turno = $int_turno;
		$this->egresso = $int_egresso;
		$this->indicacao = $str_indicacao;
		$this->nm_responsavel = $str_nm_responsavel;

		$this->cod_escola = $int_cod_escola;
		$this->ano = $int_ano;

		$this->guarda_mirim = $int_guarda_mirim;
		$this->copia_rg = $int_copia_rg;
		$this->copia_cpf = $int_copia_cpf;
		$this->copia_residencia = $int_copia_residencia;
		$this->copia_historico = $int_copia_historico;
		$this->copia_renda = $int_copia_renda;

		$this->_schema = "pmieducar.";
		$this->_tabela = $this->_schema . "selecao_inscrito";
	}

	function lista($numeric_etapa_1 = FALSE, $numeric_etapa_2 = FALSE, $numeric_etapa_3 = FALSE, $int_cod_inscrito = FALSE, $str_nome = FALSE, $numeric_cpf = FALSE, $numeric_rg = FALSE, $inicio_limite = FALSE,
		$qtd_registros = FALSE, $str_orderBy = FALSE, $int_ref_cod_sistema = FALSE)
	{
		$whereAnd = '';
		$where    = '';

		if (is_string($str_nome) && $str_nome != '')
		{
			$str_nome = addslashes($str_nome);
			$str_nome = str_replace(' ', '%', $str_nome);

			$where   .= "{$whereAnd} nome ILIKE E'%{$str_nome}%' ";
			$whereAnd = ' AND ';
		}

		if (is_string($numeric_cpf))
		{
			$numeric_cpf = addslashes($numeric_cpf);

			$where   .= "{$whereAnd} cpf ILIKE E'%{$numeric_cpf}%' ";
			$whereAnd = ' AND ';
		}

		if (is_numeric($int_cod_inscrito))
		{
			$where   .= "{$whereAnd} cod_inscrito = '$int_cod_inscrito'";
			$whereAnd = ' AND ';
		}

		if (is_numeric($numeric_etapa_1))
		{
			$where   .= "{$whereAnd} etapa_1 = '$numeric_etapa_1'";
			$whereAnd = ' AND ';
		}

		if (is_numeric($numeric_etapa_2))
		{
			$where   .= "{$whereAnd} etapa_2 = '$numeric_etapa_2'";
			$whereAnd = ' AND ';
		}

		if (is_numeric($numeric_etapa_3))
		{
			$where   .= "{$whereAnd} etapa_3 = '$numeric_etapa_3'";
			$whereAnd = ' AND ';
		}

		if ($inicio_limite !== FALSE && $qtd_registros)
		{
			$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
		}

		$orderBy = ' ORDER BY ';

		if ($str_orderBy)
		{
			$orderBy .= $str_orderBy . ' ';
		} else {
			$orderBy .= 'fcn_upper_nrm(nome) ';
		}

		$db  = new clsBanco();

		if ($where)
		{
			$where = "WHERE ".$where;
		}

		$total = $db->CampoUnico('SELECT COUNT(0) FROM pmieducar.selecao_inscrito ' . $where);

		$db->Consulta(sprintf(
			'SELECT cod_inscrito, nome, email, cpf, data_nasc, ddd_telefone_1, telefone_1, ddd_telefone_2, telefone_2, turno, etapa_1, etapa_2, etapa_3, bairro, egresso FROM pmieducar.selecao_inscrito %s %s %s',
			$where, $orderBy, $limite
		));

		$resultado = array();

		while ($db->ProximoRegistro())
		{
			$tupla          = $db->Tupla();
			$tupla['nome']  = transforma_minusculo($tupla['nome']);
			$tupla['total'] = $total;


			$resultado[] = $tupla;
		}

		if (count($resultado) > 0)
		{
			return $resultado;
		}

		return FALSE;
	}

	function lista2($numeric_etapa_1 = FALSE, $numeric_etapa_2 = FALSE, $numeric_etapa_3 = FALSE, $int_cod_inscrito = FALSE, $str_nome = FALSE, $numeric_cpf = FALSE, $numeric_rg = FALSE, $inicio_limite = FALSE,
		$qtd_registros = FALSE, $str_orderBy = FALSE, $int_ref_cod_sistema = FALSE)
	{
		$whereAnd = '';
		$where    = '';

		if (is_string($str_nome) && $str_nome != '')
		{
			$str_nome = addslashes($str_nome);
			$str_nome = str_replace(' ', '%', $str_nome);

			$where   .= "{$whereAnd} nome ILIKE E'%{$str_nome}%' ";
			$whereAnd = ' AND ';
		}

		if (is_string($numeric_cpf))
		{
			$numeric_cpf = addslashes($numeric_cpf);

			$where   .= "{$whereAnd} cpf ILIKE E'%{$numeric_cpf}%' ";
			$whereAnd = ' AND ';
		}

		if (is_numeric($int_cod_inscrito))
		{
			$where   .= "{$whereAnd} cod_inscrito = '$int_cod_inscrito'";
			$whereAnd = ' AND ';
		}

		if (is_numeric($numeric_etapa_1))
		{
			$where   .= "{$whereAnd} etapa_1 >= '$numeric_etapa_1'";
			$whereAnd = ' AND ';
		}

		if (is_numeric($numeric_etapa_2))
		{
			$where   .= "{$whereAnd} etapa_2 = '$numeric_etapa_2'";
			$whereAnd = ' AND ';
		}

		if (is_numeric($numeric_etapa_3))
		{
			$where   .= "{$whereAnd} etapa_3 = '$numeric_etapa_3'";
			$whereAnd = ' AND ';
		}

		if ($inicio_limite !== FALSE && $qtd_registros)
		{
			$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
		}

		$orderBy = ' ORDER BY ';

		if ($str_orderBy)
		{
			$orderBy .= $str_orderBy . ' ';
		} else {
			$orderBy .= 'fcn_upper_nrm(nome) ';
		}

		$db  = new clsBanco();

		if ($where)
		{
			$where = "WHERE ".$where;
		}

		$total = $db->CampoUnico('SELECT COUNT(0) FROM pmieducar.selecao_inscrito ' . $where);

		$db->Consulta(sprintf(
			'SELECT cod_inscrito, nome, email, cpf, data_nasc, ddd_telefone_1, telefone_1, ddd_telefone_2, telefone_2, turno, etapa_1, etapa_2, etapa_3, bairro, egresso FROM pmieducar.selecao_inscrito %s %s %s',
			$where, $orderBy, $limite
		));

		$resultado = array();

		while ($db->ProximoRegistro())
		{
			$tupla          = $db->Tupla();
			$tupla['nome']  = transforma_minusculo($tupla['nome']);
			$tupla['total'] = $total;


			$resultado[] = $tupla;
		}

		if (count($resultado) > 0)
		{
			return $resultado;
		}

		return FALSE;
	}

	function cadastra()
	{
		// verificacoes de campos obrigatorios para insercao
		if(is_numeric($this->cpf) || is_numeric($this->rg))
		{
			$db = new clsBanco();

			$campos  = '';
			$valores = '';
			$gruda   = '';

			if(is_string($this->nome))
			{
				$campos  .= "{$gruda}nome";
				$valores .= "{$gruda}'{$this->nome}'";
				$gruda = ', ';
			}
			if ($this->data_nasc) {
				$campos  .= "{$gruda}data_nasc";
				$valores .= "{$gruda}'{$this->data_nasc}'";
				$gruda = ', ';
			}
			if ($this->sexo) {
				$campos  .= "{$gruda}sexo";
				$valores .= "{$gruda}'{$this->sexo}'";
				$gruda = ', ';
			}

			if(is_numeric($this->cod_escola))
			{
				$campos  .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->cod_escola}'";
				$gruda = ', ';
			}
			if(is_string($this->nm_responsavel))
			{
				$campos  .= "{$gruda}nm_responsavel";
				$valores .= "{$gruda}'{$this->nm_responsavel}'";
				$gruda = ', ';
			}
			if(is_string($this->bairro))
			{
				$campos  .= "{$gruda}bairro";
				$valores .= "{$gruda}'{$this->bairro}'";
				$gruda = ', ';
			}

			if(is_numeric($this->cpf))
			{
				$campos  .= "{$gruda}cpf";
				$valores .= "{$gruda}'{$this->cpf}'";
				$gruda = ', ';
			}
			if(is_numeric($this->rg))
			{
				$campos  .= "{$gruda}rg";
				$valores .= "{$gruda}'{$this->rg}'";
				$gruda = ', ';
			}

			if(is_numeric($this->ddd_telefone_1))
			{
				$campos  .= "{$gruda}ddd_telefone_1";
				$valores .= "{$gruda}'{$this->ddd_telefone_1}'";
				$gruda = ', ';
			}
			if(is_numeric($this->telefone_1))
			{
				$campos  .= "{$gruda}telefone_1";
				$valores .= "{$gruda}'{$this->telefone_1}'";
				$gruda = ', ';
			}
			if(is_numeric($this->ddd_telefone_2))
			{
				$campos  .= "{$gruda}ddd_telefone_2";
				$valores .= "{$gruda}'{$this->ddd_telefone_2}'";
				$gruda = ', ';
			}
			if(is_numeric($this->telefone_2))
			{
				$campos  .= "{$gruda}telefone_2";
				$valores .= "{$gruda}'{$this->telefone_2}'";
				$gruda = ', ';
			}
			if(is_numeric($this->ddd_telefone_mov))
			{
				$campos  .= "{$gruda}ddd_telefone_mov";
				$valores .= "{$gruda}'{$this->ddd_telefone_mov}'";
				$gruda = ', ';
			}
			if(is_numeric($this->telefone_mov))
			{
				$campos  .= "{$gruda}telefone_mov";
				$valores .= "{$gruda}'{$this->telefone_mov}'";
				$gruda = ', ';
			}

			if($this->guarda_mirim)
			{
				$campos  .= "{$gruda}guarda_mirim";
				$valores .= "{$gruda}'{$this->guarda_mirim}'";
				$gruda = ', ';
			}
			if($this->encaminhamento)
			{
				$campos  .= "{$gruda}encaminhamento";
				$valores .= "{$gruda}'{$this->encaminhamento}'";
				$gruda = ', ';
			}
			if(is_numeric($this->serie))
			{
				$campos  .= "{$gruda}serie";
				$valores .= "{$gruda}'{$this->serie}'";
				$gruda = ', ';
			}
			if(is_numeric($this->turno))
			{
				$campos  .= "{$gruda}turno";
				$valores .= "{$gruda}'{$this->turno}'";
				$gruda = ', ';
			}
			if(is_numeric($this->egresso))
			{
				$campos  .= "{$gruda}egresso";
				$valores .= "{$gruda}'{$this->egresso}'";
				$gruda = ', ';
			}
			if(is_string($this->indicacao))
			{
				$campos  .= "{$gruda}indicacao";
				$valores .= "{$gruda}'{$this->indicacao}'";
				$gruda = ', ';
			}
			if(is_string($this->email))
			{
				$campos  .= "{$gruda}email";
				$valores .= "{$gruda}'{$this->email}'";
				$gruda = ', ';
			}

			if($this->copia_cpf)
			{
				$campos  .= "{$gruda}copia_cpf";
				$valores .= "{$gruda}'{$this->copia_cpf}'";
				$gruda = ', ';
			}
			if($this->copia_rg)
			{
				$campos  .= "{$gruda}copia_rg";
				$valores .= "{$gruda}'{$this->copia_rg}'";
				$gruda = ', ';
			}
			if($this->copia_residencia)
			{
				$campos  .= "{$gruda}copia_residencia";
				$valores .= "{$gruda}'{$this->copia_residencia}'";
				$gruda = ', ';
			}
			if($this->copia_historico)
			{
				$campos  .= "{$gruda}copia_historico";
				$valores .= "{$gruda}'{$this->copia_historico}'";
				$gruda = ', ';
			}
			if($this->copia_renda)
			{
				$campos  .= "{$gruda}copia_renda";
				$valores .= "{$gruda}'{$this->copia_renda}'";
				$gruda = ', ';
			}

			if($this->etapa_1)
			{
				$campos  .= "{$gruda}etapa_1";
				$valores .= "{$gruda}'{$this->etapa_1}'";
				$gruda = ', ';
			}

			if($this->etapa_2)
			{
				$campos  .= "{$gruda}etapa_2";
				$valores .= "{$gruda}'{$this->etapa_2}'";
				$gruda = ', ';
			}

			if($this->etapa_3)
			{
				$campos  .= "{$gruda}etapa_3";
				$valores .= "{$gruda}'{$this->etapa_3}'";
				$gruda = ', ';
			}

			if (is_numeric($this->ref_usuario_cad)) {
				$campos  .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ', ';
			}

			if(is_numeric($this->ano))
			{
				$campos  .= "{$gruda}ref_ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ', ';
			}

			if(is_numeric($this->ref_cod_escola))
			{
				$campos  .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ', ';
			}

			$campos  .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ', ';

			$campos  .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ', ';

			$db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
			return $db->InsertId("selecao_cod_inscrito_seq");
		}
		return false;
	}

	function edita()
	{
		// verificacoes de campos obrigatorios para insercao
		if(is_numeric($this->cod_inscrito))
		{
			$set = "";
			$gruda = "";

			if(is_string($this->nome))
			{
				$set .= " nome =  '$this->nome' ";
				$gruda = ", ";
			}
			if ($this->data_nasc) {
				$set .= "$gruda data_nasc =  '$this->data_nasc' ";
				$gruda = ", ";
			}
			if ($this->sexo) {
				$set .= "$gruda sexo =  '$this->sexo' ";
				$gruda = ", ";
			}

			if(is_numeric($this->cod_escola))
			{
				$set .= "$gruda cod_escola =  '$this->cod_escola' ";
				$gruda = ", ";
			}

			if($this->nm_responsavel && is_string($this->nm_responsavel))
			{
				$set .= "$gruda nm_responsavel =  '$this->nm_responsavel' ";
				$gruda = ", ";
			}

			if($this->bairro && is_string($this->bairro))
			{
				$set .= "$gruda bairro =  '$this->bairro' ";
				$gruda = ", ";
			}

			if(is_numeric($this->cpf))
			{
				$set .= "$gruda cpf =  '$this->cpf' ";
				$gruda = ", ";
			}
			if(is_numeric($this->rg))
			{
				$set .= "$gruda rg =  '$this->rg' ";
				$gruda = ", ";
			}

			if(is_numeric($this->ddd_telefone_1))
			{
				$set .= "$gruda ddd_telefone_1 =  '$this->ddd_telefone_1' ";
				$gruda = ", ";
			}
			if(is_numeric($this->telefone_1))
			{
				$set .= "$gruda telefone_1 =  '$this->telefone_1' ";
				$gruda = ", ";
			}
			if(is_numeric($this->ddd_telefone_2))
			{
				$set .= "$gruda ddd_telefone_2 =  '$this->ddd_telefone_2' ";
				$gruda = ", ";
			}
			if(is_numeric($this->telefone_2))
			{
				$set .= "$gruda telefone_2 =  '$this->telefone_2' ";
				$gruda = ", ";
			}
			if(is_numeric($this->ddd_telefone_mov))
			{
				$set .= "$gruda ddd_telefone_mov =  '$this->ddd_telefone_mov' ";
				$gruda = ", ";
			}
			if(is_numeric($this->telefone_mov))
			{
				$set .= "$gruda telefone_mov =  '$this->telefone_mov' ";
				$gruda = ", ";
			}

			if($this->guarda_mirim)
			{
				$set .= "$gruda guarda_mirim =  '$this->guarda_mirim' ";
				$gruda = ", ";
			}
			if($this->encaminhamento)
			{
				$set .= "$gruda encaminhamento =  '$this->encaminhamento' ";
				$gruda = ", ";
			}

			if(is_numeric($this->serie))
			{
				$set .= "$gruda serie =  '$this->serie' ";
				$gruda = ", ";
			}
			if(is_numeric($this->turno))
			{
				$set .= "$gruda turno =  '$this->turno' ";
				$gruda = ", ";
			}
			if(is_numeric($this->egresso))
			{
				$set .= "$gruda egresso =  '$this->egresso' ";
				$gruda = ", ";
			}
			if(is_string($this->indicacao))
			{
				$set .= "$gruda indicacao =  '$this->indicacao' ";
				$gruda = ", ";
			}
			if(is_string($this->email))
			{
				$set .= "$gruda email =  '$this->email' ";
				$gruda = ", ";
			}

			if($this->copia_cpf)
			{
				$set .= "$gruda copia_cpf =  '$this->copia_cpf' ";
				$gruda = ", ";
			}
			if($this->copia_rg)
			{
				$set .= "$gruda copia_rg =  '$this->copia_rg' ";
				$gruda = ", ";
			}
			if($this->copia_residencia)
			{
				$set .= "$gruda copia_residencia =  '$this->copia_residencia' ";
				$gruda = ", ";
			}
			if($this->copia_historico)
			{
				$set .= "$gruda copia_historico =  '$this->copia_historico' ";
				$gruda = ", ";
			}
			if($this->copia_renda)
			{
				$set .= "$gruda copia_renda =  '$this->copia_renda' ";
				$gruda = ", ";
			}

			if($this->etapa_1)
			{
				$set .= "$gruda etapa_1 = '$this->etapa_1'";
				$gruda = ", ";
			}

			if($this->etapa_2)
			{
				$set .= "$gruda etapa_2 = '$this->etapa_2'";
				$gruda = ", ";
			}

			if($this->etapa_3)
			{
				$set .= "$gruda etapa_3 = '$this->etapa_3'";
				$gruda = ", ";
			}

			if($set)
			{
				$db = new clsBanco();
				$db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_inscrito = '$this->cod_inscrito'");
				return true;
			}
		}
		return false;
	}

	function detalhe()
	{
		if ($this->cod_inscrito)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT cod_inscrito, nome, data_nasc, cpf, rg, sexo, ddd_telefone_1, telefone_1, ddd_telefone_2, telefone_2, ddd_telefone_mov, telefone_mov, email, nm_responsavel, indicacao, encaminhamento, guarda_mirim, serie, turno, egresso, copia_rg, copia_cpf, copia_residencia, copia_historico, copia_renda, ref_ano, ref_cod_escola, etapa_1, etapa_2, etapa_3, bairro, ref_usuario_cad  FROM {$this->_tabela} WHERE cod_inscrito = {$this->cod_inscrito}");

			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				return $tupla;
			}
		}

		return FALSE;
	}

	function queryRapida($int_cod_inscrito)
	{
		$this->cod_inscrito = $int_cod_inscrito;
		$this->detalhe();

		$resultado = array();
		$pos       = 0;

		for ($i = 1; $i < func_num_args(); $i++)
		{
			$campo = func_get_arg($i);

			$resultado[$pos]   = $this->$campo ? $this->$campo : '';
			$resultado[$campo] = & $resultado[$pos];

			$pos++;
		}

		if (count($resultado) > 0)
			return $resultado;

		return FALSE;
	}

	function queryRapidaCPF($int_cpf)
	{
		$this->cpf = $int_cpf + 0;
		$this->detalhe();

		$resultado = array();
		$pos       = 0;

		for ($i = 1; $i < func_num_args(); $i++)
		{
			$campo = func_get_arg($i);
			$resultado[$pos]   = $this->$campo ? $this->$campo : '';
			$resultado[$campo] = & $resultado[$pos];
			$pos++;
		}

		if (count($resultado) > 0)
		{
			return $resultado;
		}

		return FALSE;
	}

	function excluir()
	{
		if ($this->cod_inscrito)
		{
			$db  = new clsBanco();
			$obj = new clsFuncionario($this->cod_inscrito);

			if (! $obj->detalhe()) {
				$db->Consulta('DELETE FROM cadastro.fone_pessoa WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.fisica WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.documento WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.endereco_pessoa WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.endereco_externo WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.documento WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.documento WHERE cod_inscrito = ' . $this->cod_inscrito);
				$db->Consulta('DELETE FROM cadastro.pessoa WHERE cod_inscrito = ' . $this->cod_inscrito);
			}
		}
	}
}
