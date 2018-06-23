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
require_once("include/pmieducar/geral.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo("{$this->_instituicao} i-Educar - Entrevistas");
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

		$this->titulo = "Entrevistas - Detalhe";


		$this->cod_vps_entrevista=$_GET["cod_vps_entrevista"];

		$tmp_obj = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
		$registro = $tmp_obj->detalhe();

		if(!$registro)
		{
			header("location: educar_entrevista_lst.php");
			die();
		}

		if(class_exists("clsPmieducarEscola"))
		{
			$obj_ref_cod_escola = new clsPmieducarEscola($registro["ref_cod_escola"]);
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

		if(class_exists("clsPmieducarVPSFuncao"))
		{
			$obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registro["ref_cod_vps_funcao"]);
			$det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
			$registro["ref_cod_vps_funcao"] = $det_ref_cod_vps_funcao["nm_funcao"];
		}
		else
		{
			$registro["ref_cod_vps_funcao"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
		}

		if(class_exists("clsPmieducarVPSJornadaTrabalho"))
		{
			$obj_ref_cod_vps_jornada_trabalho = new clsPmieducarVPSJornadaTrabalho($registro["ref_cod_vps_jornada_trabalho"]);
			$det_ref_cod_vps_jornada_trabalho = $obj_ref_cod_vps_jornada_trabalho->detalhe();
			$registro["ref_cod_vps_jornada_trabalho"] = $det_ref_cod_vps_jornada_trabalho["nm_jornada_trabalho"];
		}
		else
		{
			$registro["ref_cod_vps_jornada_trabalho"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSJornadaTrabalho\n-->";
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

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if($registro["ref_cod_instituicao"])
			{
				$this->addDetalhe(array("Institui��o", "{$registro["ref_cod_instituicao"]}"));
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
			$this->addDetalhe(array("Empresa", "{$registro["ref_idpes"]}"));
		}
		if($registro["ref_cod_vps_funcao"])
		{
			$this->addDetalhe(array("Fun��o", "{$registro["ref_cod_vps_funcao"]}"));
		}
		if($registro["descricao"])
		{
			$this->addDetalhe(array("Descri��o", "{$registro["descricao"]}"));
		}
		if($registro["salario"])
		{
			$valor = "R$ " . number_format($registro["salario"], 2, ",", ".");
			$this->addDetalhe(array("Sal�rio", "{$valor}"));
		}
		if($registro["numero_vagas"])
		{
			$valor = $registro["numero_vagas"];
			$this->addDetalhe(array("N�mero de vagas", "{$valor} vagas"));
		}
		if($registro["numero_jovens"])
		{
			$valor = $registro["numero_jovens"];
			$this->addDetalhe(array("N�mero de jovens por vaga", "{$valor} jovens"));
		}
		if($registro["data_entrevista"])
		{
			$data = Portabilis_Date_Utils::pgSQLToBr($registro["data_entrevista"]);
			$this->addDetalhe(array("Data da entrevista", "{$data}"));
		}
		if($registro["hora_entrevista"])
		{
			$this->addDetalhe(array("Hora da entrevista", "{$registro["hora_entrevista"]}"));
		}
		if($registro["ref_cod_vps_jornada_trabalho"])
		{
			$this->addDetalhe(array("Jornada de trabalho", "{$registro["ref_cod_vps_jornada_trabalho"]}"));
		}

		$obj = new clsPmieducarVPSEntrevistaResponsavel();
		$obj->setOrderby("principal DESC");
		$lst = $obj->lista(null, $this->cod_vps_entrevista);
		if ($lst) {
			$tabela =
				'<table class="table sub table-striped table-bordered" cellspacing="0" width="30%">
					<thead>
						<tr align=center>
							<td bgcolor=#A1B3BD><B>Nome</B></td>
							<td bgcolor=#A1B3BD><B>Principal</B></td>
						</tr>
					</thead>';
			$cont = 0;

			foreach ($lst AS $valor)
			{
				if (($cont % 2) == 0)
				{
					$color = " bgcolor=#E4E9ED ";
				} else {
					$color = " bgcolor=#FFFFFF ";
				}

				$obj = new clsPmieducarVPSResponsavelEntrevista($valor["ref_cod_vps_responsavel_entrevista"]);
				$det = $obj->detalhe();
				$nm_autor = $det["nm_responsavel"];
				$principal = $valor["principal"];

				if ($principal == 1)
					$principal = "sim";
				else
					$principal = "n�o";

				$tabela .= "<tr>
								<td {$color} align=left>{$nm_autor}</td>
								<td {$color} align=left>{$principal}</td>
							</tr>";
				$cont++;
			}
			$tabela .= "</table>";
		}
		if($tabela)
		{
			$this->addDetalhe(array("Respons�vel", "{$tabela}"));
		}

		$obj = new clsPmieducarVPSIdioma();
		$obj = $obj->listaIdiomasEntrevista($this->cod_vps_entrevista);

		if (count($obj))
		{
			foreach ($obj as $reg)
			{
				$assuntos.= '<span style="background-color: #A1B3BD; padding: 2px;"><b>' . $reg['nome'] . '</b></span>&nbsp; ';
			}
			if(!empty($assuntos))
				$this->addDetalhe(array("Idiomas necess�rios", "{$assuntos}"));
		}

		$entrevistas = new clsPmieducarVPSAlunoEntrevista(null, null, $this->cod_vps_entrevista);
		$todasEntrevistas = $entrevistas->lista();

		if (count($todasEntrevistas))
		{
			$assuntos = "";

			$tabela =
				'<table class="table sub table-striped table-bordered" cellspacing="0" width="30%">
					<thead>
						<tr align=center>
							<td bgcolor=#A1B3BD><B>Nome</B></td>
						</tr>
					</thead>';
			$cont = 0;

			foreach ($todasEntrevistas AS $valor)
			{
				$nm_jovem = strtoupper($valor["nome"]);
				$id_jovem = $valor["ref_cod_aluno"];

				$tabela .= "<TR>
								<TD {$color} align=left><a href='/intranet/educar_aluno_det.php?cod_aluno={$id_jovem}' target ='_blank'>{$nm_jovem}</a></TD>
							</TR>";
				$cont++;
			}

			$tabela .= "</TABLE>";

			if($tabela)
			{
				$this->addDetalhe(array("Entrevistados", "{$tabela}"));
			}
			if(!empty($assuntos))
				$this->addDetalhe(array("Jovens entrevistados", "{$assuntos}"));

		}

		if($todasEntrevistas)
		{
			$index = 1;

			foreach($todasEntrevistas AS $campo => $val)
			{
				$this->{"aluno" . $index . "_id"} = $val['ref_cod_aluno'];
				$index++;
			}
		}

		$obj_permissoes = new clsPermissoes();
		if($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11))
		{
			$this->url_novo = "educar_entrevista_cad.php";
			$this->url_editar = "educar_entrevista_cad.php?cod_vps_entrevista={$registro["cod_vps_entrevista"]}";
		}

		$this->url_cancelar = "educar_entrevista_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos(array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			"educar_vps_index.php"                => "Trilha Jovem Iguassu - VPS",
			""                                    => "Detalhe da entrevista"
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
