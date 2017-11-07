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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("lib/App/Model/PrioridadeVPS.php");
require_once ("lib/App/Model/VivenciaProfissionalSituacao.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} - Aluno VPS");
		$this->processoAp = "598";
		$this->addEstilo('localizacaoSistema');
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

	var $cod_vps_entrevista;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_vps_entrevista;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_vps_funcao;
	var $ref_cod_vps_jornada_trabalho;
	var $ref_cod_tipo_contratacao;
	var $empresa_id;
	var $nm_entrevista;
	var $descricao;
	var $data_entrevista;
	var $hora_entrevista;
	var $ano;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;
	var $ref_cod_escola;

	var $checked;

	var $vps_entrevista_responsavel;
	var $ref_cod_vps_responsavel_entrevista;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Aluno VPS - Detalhe";


		$this->cod_aluno = $_GET["cod_aluno"];

		$tmp_obj = new clsPmieducarAluno($this->cod_aluno);
		$tmp_obj = new clsPmieducarMatricula();
		$registro = $tmp_obj->lista(null, null, null, null, null, null, $this->cod_aluno);
		$registro = $registro[0];

		if(!$registro)
		{
			header("location: educar_vps_aluno_lst.php");
			die();
		}

		if(class_exists("clsPmieducarEscola"))
		{
			$obj_ref_cod_escola = new clsPmieducarEscola($registro["ref_ref_cod_escola"]);
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$idpes = $det_ref_cod_escola["ref_idpes"];
			if ($idpes)
			{
				$obj_escola = new clsPessoaJuridica($idpes);
				$obj_escola_det = $obj_escola->detalhe();
				$registro["ref_cod_escola"] = $obj_escola_det["fantasia"];
			}
			else
			{
				$obj_escola = new clsPmieducarEscolaComplemento($registro["ref_cod_escola"]);
				$obj_escola_det = $obj_escola->detalhe();
				$registro["ref_cod_escola"] = $obj_escola_det["nm_escola"];
			}
			if(class_exists("clsPmieducarInstituicao"))
			{
				$registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];
				$obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
				$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
				$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
			}
			else
			{
				$registro["ref_cod_instituicao"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
			}
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}

		if(class_exists("clsPmieducarCurso"))
		{
			$obj_ref_cod_curso = new clsPmieducarCurso($registro["ref_cod_curso"]);
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
		}

		if(class_exists("clsPessoaFj"))
		{
			$obj_ref_idpes = new clsPessoaFj($registro["ref_idpes"]);
			$det_ref_idpes = $obj_ref_idpes->detalhe();
			$registro["ref_idpes"] = $det_ref_idpes["nome"];
		}
		else
		{
			$registro["ref_idpes"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
		}

		$alunoVPS = new clsPmieducarAlunoVPS($this->cod_aluno);

		if($alunoVPS && $alunoVPS->existe())
			$registroVPS = $alunoVPS->detalhe();

		if($registroVPS["situacao_vps"])
			$situacaoVPS = App_Model_VivenciaProfissionalSituacao::getInstance()->getValue($registroVPS["situacao_vps"]);

		if($registroVPS["ref_cod_vps_aluno_entrevista"])
		{
			$alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($registroVPS["ref_cod_vps_aluno_entrevista"]);
			$registroAlunoEntrevista = $alunoEntrevista->detalhe();

			$ref_cod_vps_entrevista = $registroAlunoEntrevista["ref_cod_vps_entrevista"];

			if($ref_cod_vps_entrevista)
			{
				$entrevista = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
				$registroEntrevista = $entrevista->detalhe();
				$nm_entrevista = $registroEntrevista["nm_entrevista"];
			}

			if($registroAlunoEntrevista["inicio_vps"])
				$inicioVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["inicio_vps"]);

			if($registroAlunoEntrevista["termino_vps"])
				$terminoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["termino_vps"]);

			if($registroAlunoEntrevista["insercao_vps"])
				$insercaoVPS = Portabilis_Date_Utils::pgSQLToBr($registroAlunoEntrevista["insercao_vps"]);
		}

		if(is_numeric($registroVPS["prioridade"]))
			$prioridadeVPS = App_Model_PrioridadeVPS::getInstance()->getValue($registroVPS["prioridade"]);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if($registro["ref_cod_instituicao"])
			{
				$this->addDetalhe(array("Instituição", "{$registro["ref_cod_instituicao"]}"));
			}
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if($registro["ref_cod_escola"])
			{
				$this->addDetalhe(array("Escola", "{$registro["ref_cod_escola"]}"));
			}
		}
		if($registro["ref_cod_curso"])
		{
				$this->addDetalhe(array("Projeto", "{$registro["ref_cod_curso"]}"));
		}
		if($registro["ano"])
		{
				$this->addDetalhe(array("Ano", "{$registro["ano"]}"));
		}
		if($registro["ref_idpes"])
		{
			$this->addDetalhe(array("Aluno", "{$registro["ref_idpes"]}"));
		}

		if($numero_entrevistas)
		{
			$this->addDetalhe(array("Número de Entrevistas", "{$numero_entrevistas}"));
		}
		if($situacaoVPS)
		{
			$this->addDetalhe(array("Situação VPS", "{$situacaoVPS}"));
		}
		if($prioridadeVPS)
		{
			$this->addDetalhe(array("Prioridade VPS", "{$prioridadeVPS}"));
		}
		if($registroVPS["motivo_desligamento"])
		{
			$this->addDetalhe(array("Motivo Desligamento VPS", "{$registroVPS["motivo_desligamento"]}"));
		}
		if($nm_entrevista)
		{
			$this->addDetalhe(array("Entrevista início VPS", "<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$nm_entrevista}</a>"));
		}
		if($inicioVPS)
		{
			$this->addDetalhe(array("Data Início VPS", "$inicioVPS"));
		}
		if($terminoVPS)
		{
			$this->addDetalhe(array("Data Término VPS", "$terminoVPS"));
		}
		if($insercaoVPS)
		{
			$this->addDetalhe(array("Data Inserção Profissional", "$insercaoVPS"));
		}

		$obj = new clsPmieducarVPSAlunoEntrevista();
		$entrevistas = $obj->lista($this->cod_aluno);

		if ($entrevistas) {
			$tabela = "<TABLE>
					       <TR align=center>
					           <TD bgcolor=#A1B3BD><B>Entrevista</B></TD>
					           <TD bgcolor=#A1B3BD><B>Data</B></TD>
					       </TR>";
			$cont = 0;

			foreach ($entrevistas AS $valor)
			{
				if (($cont % 2) == 0)
				{
					$color = " bgcolor=#E4E9ED ";
				} else {
					$color = " bgcolor=#FFFFFF ";
				}

				$ref_cod_vps_entrevista = $valor["ref_cod_vps_entrevista"];
				$obj = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
				$det = $obj->detalhe();

				$nm_entrevista = $det["nm_entrevista"];
				$data_entrevista = Portabilis_Date_Utils::pgSQLToBr($det["data_entrevista"]);
				$hora_entrevista = $det["hora_entrevista"];

				$tabela .= "<TR>


							    <TD {$color} align=left>
									<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$nm_entrevista}</a>
								</TD>
							    <TD {$color} align=left>
									<a href=\"educar_resultado_entrevista_cad.php?cod_vps_entrevista={$ref_cod_vps_entrevista}\" target=\"_blank\">{$data_entrevista} às {$hora_entrevista}</a>
								</TD>
							</TR>";
				$cont++;
			}
			$tabela .= "</TABLE>";
		}
		if($tabela)
		{
			$this->addDetalhe(array("Todas as Entrevistas", "{$tabela}"));
		}

		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11))
		{
			$this->array_botao = array('Atualizar Situação', 'Agendar Visita VPS');

			$this->array_botao_url_script = array(
				sprintf('go("educar_vps_aluno_cad.php?cod_aluno=%d");', $this->cod_aluno),
				sprintf('go("educar_vps_visita_cad.php?ref_cod_aluno=%d");', $this->cod_aluno)
			);
			$this->url_editar = "educar_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}";
		}

		$this->url_cancelar = "educar_vps_aluno_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
			""                                    => "Detalhe do Aluno"
		));

		$this->enviaLocalizacao($localizacao->montar());
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
?>
