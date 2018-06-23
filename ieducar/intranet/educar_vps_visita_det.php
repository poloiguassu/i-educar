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

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("lib/App/Model/SimNao.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} - Detalhamento de Visitas" );
		$this->processoAp = "598";
		$this->addEstilo("localizacaoSistema");
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_funcao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_funcao;
	var $abreviatura;
	var $professor;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Visitas - Detalhe";

		$this->cod_vps_visita = $_GET["cod_vps_visita"];

		$tmp_obj = new clsPmieducarVPSVisita($this->cod_vps_visita);
		$registro = $tmp_obj->detalhe();

		if(!$registro)
		{
			header( "location: educar_vps_visita_lst.php" );
			die();
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if( class_exists( "clsPessoa_" ) )
		{
			$obj_cod_usuario = new clsPessoa_($registro["ref_cod_usuario"] );
			$obj_usuario_det = $obj_cod_usuario->detalhe();
			$registro["nm_usuario"] = $obj_usuario_det['nome'];
		}

		if($registro["ref_cod_vps_aluno_entrevista"])
		{
			$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($registro["ref_cod_vps_aluno_entrevista"]);
			$registroAlunoEntrevista = $alunoEntrevista->detalhe();

			$ref_cod_vps_entrevista = $registroAlunoEntrevista["ref_cod_vps_entrevista"];

			if($registroAlunoEntrevista["ref_cod_aluno"])
			{
				$alunoVPS = new clsPmieducarAluno($registroAlunoEntrevista["ref_cod_aluno"]);

				if($alunoVPS && $alunoVPS->existe())
				{
					$registroAluno = $alunoVPS->detalhe();

					if(class_exists("clsPessoaFj"))
					{
						$obj_ref_idpes = new clsPessoaFj($registroAluno["ref_idpes"]);
						$det_ref_idpes = $obj_ref_idpes->detalhe();
						$registroAluno["ref_idpes"] = $det_ref_idpes["nome"];
					}
					else
					{
						$registro["ref_idpes"] = "Erro na geracao";
						echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
					}
					$this->addDetalhe(array("Aluno", "{$registroAluno["ref_idpes"]}"));
				}
			}

			if($ref_cod_vps_entrevista)
			{
				$entrevista = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
				$registroEntrevista = $entrevista->detalhe();

				if(class_exists("clsPmieducarVPSFuncao"))
				{
					$obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registroEntrevista["ref_cod_vps_funcao"]);
					$det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();

					$this->addDetalhe(array("Cumprindo VPS", "{$det_ref_cod_vps_funcao["nm_funcao"]}"));
				}
				else
				{
					$registro["ref_cod_vps_funcao"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
				}

				if(class_exists("clsPessoaFj"))
				{
					$empresa_id = $registroEntrevista["ref_idpes"];
					$obj_ref_idpes = new clsPessoaFj($empresa_id);
					$det_ref_idpes = $obj_ref_idpes->detalhe();
					$nm_empresa = $det_ref_idpes["nome"];

					$objPessoaJuridica = new clsPessoaJuridica();
					list ($endereco, $numero, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1,
						$ddd_telefone_2, $telefone_2, $ddd_telefone_mov,
					 	$telefone_mov, $idtlog
					) = $objPessoaJuridica->queryRapida($empresa_id,
						"logradouro", "numero", "cep","bairro","cidade", "ddd_1","fone_1",
						"ddd_2","fone_2","ddd_mov","fone_mov","idtlog"
					);

					$this->addDetalhe(array("Empresa", "{$nm_empresa}"));

					if(is_string($endereco))
					{
						$endereco .= ", {$numero}, {$nm_bairro} - {$cidade} CEP: {$cep}";

						$this->addDetalhe(array("Endere�o Empresa", "{$endereco}"));
					}

					if(!empty($telefone_1))
						$this->addDetalhe(array("Empresa Telefone 1", "({$ddd_telefone_1}) {$telefone_1}"));

					if(!empty($telefone_2))
						$this->addDetalhe(array("Empresa Telefone 2", "({$ddd_telefone_2}) {$telefone_2}"));

					if(!empty($telefone_mov))
						$this->addDetalhe(array("Empresa Celular", "({$ddd_telefone_mov}) {$telefone_mov}"));
				}
				else
				{
					$registro["ref_idpes"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
				}
			}

			if($registroAlunoEntrevista["inicio_vps"])
				$inicioVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["inicio_vps"]);

			if($registroAlunoEntrevista["termino_vps"])
				$terminoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["termino_vps"]);

			if($registroAlunoEntrevista["insercao_vps"])
				$insercaoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["insercao_vps"]);
		}

		if($registro["nm_usuario"])
		{
			$this->addDetalhe(array("Respons�vel Visita", "{$registro["nm_usuario"]}"));
		}


		if($registro["data_visita"])
		{
			$data_visita = Portabilis_Date_Utils::pgSQLToBr($registro["data_visita"]);
			$this->addDetalhe(array("Data da Visita", "{$data_visita}"));
		}

		if($registro["hora_visita"])
		{
			$this->addDetalhe(array("Hora da Visita", "{$registro["hora_visita"]}"));
		}

		if($registro["motivo_visita"])
		{
			$this->addDetalhe(array("Motivo da visita", "{$registro["motivo_visita"]}"));
		}

		if(is_numeric($registro["avaliacao"]))
		{
			$avaliacao = App_Model_SimNao::getInstance()->getValue($registro["avaliacao"]);
			$this->addDetalhe(array("Avalia��o de VPS?", "{$avaliacao}"));
		}

		if($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11))
		{
			$this->url_editar = "educar_vps_visita_cad.php?cod_vps_visita={$this->cod_vps_visita}";
		}

		$this->url_cancelar = "educar_vps_visita_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet"	=> "In�cio",
			"educar_index.php"						=> "Trilha Jovem Iguassu - VPS",
			""										=> "Detalhe da visita"
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
