<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsPreInscrito.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'image_check.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo($this->_instituicao . ' Inscritos no Processo Seletivo - Cadastro');
		$this->processoAp = 43;
		$this->addEstilo('localizacaoSistema');
	}
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
	var $cod_inscrito;
	var $nome;
	var $data_nasc;
	var $cpf;
	var $rg;
	var $sexo;
	var $ddd_telefone_1;
	var $telefone_1;
	var $ddd_telefone_2;
	var $telefone_2;
	var $ddd_telefone_mov;
	var $telefone_mov;
	var $email;
	var $nm_responsavel;
	var $busca_pessoa;
	var $retorno;

	var $indicacao;
	var $guarda_mirim;
	var $serie;
	var $turno;
	var $egresso;
	var $copia_rg;
	var $copia_cpf;
	var $copia_residencia;
	var $copia_historico;
	var $copia_renda;
	var $etapa_1;
	var $etapa_2;
	var $etapa_3;
	var $encaminhamento;

	//var $cod_escola;
	//var $ref_cod_escola;
	//var $ano;

	var $caminho_det;
	var $caminho_lst;

	function Inicializar()
	{
		$this->cod_inscrito = @$_GET['cod_inscrito'];
		$this->retorno       = 'Novo';

		if (is_numeric($this->cod_inscrito))
		{
			$this->retorno = 'Editar';
			$objPessoa     = new clsPreInscrito($this->cod_inscrito);

			$registro = $objPessoa->detalhe();

			if($registro)
			{
				foreach($registro AS $campo => $val)	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
			}

			$this->cpf = is_numeric($this->cpf) ? int2CPF($this->cpf) : '';
			$this->data_nasc = $this->data_nasc ? dataFromPgToBr($this->data_nasc) : '';

			$obj_esc  = new clsPmieducarEscola($this->ref_cod_escola);
			$det_esc  = $obj_esc->detalhe();

			$this->ref_cod_escola      = $det_esc['cod_escola'];
			$this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
			$this->ref_cod_curso       = 2; // HAAAAAACK
			$this->ano = $this->ref_ano;
		}

		$this->nome_url_cancelar = 'Cancelar';

		$nomeMenu = $this->retorno == "Editar" ? $this->retorno : "Cadastrar";
		$localizacao = new LocalizacaoSistema();

		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "Início",
			""                                    => "$nomeMenu Inscrito Processo Seletivo"
		));

		$this->enviaLocalizacao($localizacao->montar());

		return $this->retorno;
	}

	function Gerar()
	{
		$this->url_cancelar = $this->retorno == 'Editar' ?
			'selecao_inscritos_det.php?cod_pessoa=' . $this->cod_inscrito : 'selecao_inscritos_lst.php';

		$obrigatorio              = FALSE;
		$instituicao_obrigatorio  = TRUE;
		$escola_curso_obrigatorio = TRUE;
		$curso_obrigatorio        = TRUE;
		$get_escola               = TRUE;
		$get_escola_curso_serie   = FALSE;
		$sem_padrao               = TRUE;
		$get_curso                = TRUE;

		$bloqueia = false;
		$anoVisivel = TRUE;

		$desabilitado = $bloqueia;

		include 'include/pmieducar/educar_campo_lista.php';

		if ($anoVisivel)
		{
			$this->inputsHelper()->dynamic('anoLetivo', array('disabled' => $bloqueia));
			if($bloqueia)
				$this->inputsHelper()->hidden('ano_hidden', array('value' => $this->ano));
		}

		$this->campoTexto('nome', 'Nome', $this->nome, '50', '255', TRUE);

		$this->campoTexto('nm_responsavel', 'Responsável', $this->nm_responsavel, '50', '255', false);

		$this->campoCpf('cpf', 'CPF', $this->cpf, FALSE);

		$this->campoOculto('cod_inscrito', $this->cod_inscrito);

		// documentos
		$documentos        = new clsDocumento();
		$documentos->idpes = $this->cod_inscrito;
		$documentos        = $documentos->detalhe();

		// rg
		// o rg é obrigatorio ao cadastrar pai ou mãe, exceto se configurado como opcional.

		$required = (! empty($parentType));

		if ($required && $GLOBALS['coreExt']['Config']->app->rg_pessoa_fisica_pais_opcional)
		{
			$required = false;
		}

		$options = array(
			'required'    => $required,
			'label'       => 'RG / Data emissão',
			'placeholder' => 'Documento identidade',
			'value'       => $this->rg,
			'max_length'  => 20,
			'size'        => 27,
		);

		$this->inputsHelper()->integer('rg', $options);


		// data emissão rg

		/*$options = array(
			'required'    => false,
			'label'       => '',
			'placeholder' => 'Data emissão',
			'value'       => $documentos['data_exp_rg'],
			'size'        => 19
		);

		$this->inputsHelper()->date('data_emissao_rg', $options);


		// orgão emissão rg

		$selectOptions = array( null => 'Orgão emissor' );
		$orgaos        = new clsOrgaoEmissorRg();
		$orgaos        = $orgaos->lista();

		foreach ($orgaos as $orgao)
			$selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];

		$selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

		$options = array(
			'required'  => false,
			'label'     => '',
			'value'     => $documentos['idorg_exp_rg'],
			'resources' => $selectOptions,
			'inline'    => true
		);

		$this->inputsHelper()->select('orgao_emissao_rg', $options);


		// uf emissão rg

		$options = array(
			'required' => false,
			'label'    => '',
			'value'    => $documentos['sigla_uf_exp_rg']
		);

		$helperOptions = array(
			'attrName' => 'uf_emissao_rg'
		);

		$this->inputsHelper()->uf($options, $helperOptions);*/

		// sexo
		$sexo = $this->sexo;

		$options = array(
			'label'    => 'Sexo',
			'value'    => $sexo,
			'required' => true,
			'resources' => array(
				''  => 'Sexo',
				'M' => 'Masculino',
				'F' => 'Feminino'
			),
		);

		$this->inputsHelper()->select('sexo', $options);

		$options = array(
			'label'       => 'Data nascimento',
			'value'       => $this->data_nasc,
			'required'    => empty($parentType)
		);

		$this->inputsHelper()->date('data_nasc', $options);

		// contato

		$this->inputTelefone('1', 'Telefone residencial');
		$this->inputTelefone('mov', 'Celular');
		$this->inputTelefone('2', 'Telefone adicional');

		$this->campoTexto('bairro', 'Bairro', $this->bairro, '50', '255', FALSE);

		$this->campoTexto('indicacao', 'Como ficou sabendo?', $this->indicacao, '50', '255', FALSE);

		$this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);

		$this->campoCheck('guarda_mirim', 'Guarda Mirim', $this->guarda_mirim, '', FALSE, FALSE);

		$this->campoCheck('encaminhamento', 'Encaminhado pela rede de proteção', $this->encaminhamento, '', FALSE, FALSE);

		$options = array(
			'required'	=> false,
			'label'     => 'Série',
			'value'     => $this->serie,
			'resources' => array(
				'' => 'Série',
				'1' => '5ª série',
				'2' => '6ª série',
				'3' => '7ª série',
				'4' => '8ª série',
				'5' => '9ª série',
				'6' => '1º ano Ensino Médio',
				'7' => '2º ano Ensino Médio',
				'8' => '3º ano Ensino Médio',
				'9' => 'Concluido',
                                '10' => 'EJA'
			),
		);

		$this->inputsHelper()->select('serie', $options);

		$options = array(
			'required'	=> false,
			'label'     => 'Turno em que estuda',
			'value'     => $this->turno,
			'resources' => array(
				'' => 'Turno',
				'1' => 'Manhã',
				'2' => 'Tarde',
				'3' => 'Noite',
			),
		);

		$this->inputsHelper()->select('turno', $options);

		$options = array(
			'required'    => $required,
			'label'       => 'Ano de Conclusão (egresso)',
			'placeholder' => 'Ano',
			'value'       => $this->egresso,
			'max_length'  => 4,
			'size'        => 6,
		);

		$this->inputsHelper()->integer('egresso', $options);

		$this->campoCheck('copia_rg', 'Cópia do RG', $this->copia_rg, '', FALSE, FALSE);

		$this->campoCheck('copia_cpf', 'Cópia do CPF', $this->copia_cpf, '', FALSE, FALSE);

		$this->campoCheck('copia_residencia', 'Cópia do Comprovante de Residencia', $this->copia_residencia, '', FALSE, FALSE);

		$this->campoCheck('copia_historico', 'Cópia do Histório / Declaração', $this->copia_historico, '', FALSE, FALSE);

		$this->campoCheck('copia_renda', 'Comprovante de renda', $this->copia_renda, '', FALSE, FALSE);

		$options = array(
			'required' => false,
			'label'    => "Avaliação Projeto Etapa 1",
			'inline'   => false,
			'value'     => $this->etapa_1,
			'resources' => array(
				'' => '1ª Etapa',
				'1' => 'Não Adequado',
				'2' => 'Parcialmente Adequado',
				'3' => 'Adequado'
			),
		);

		$this->inputsHelper()->select('etapa_1', $options);

		$options = array(
			'required' => false,
			'label'    => "Avaliação Projeto Etapa 1",
			'inline'   => false,
			'value'     => $this->etapa_2,
			'resources' => array(
				'-1' => '2ª Etapa',
				'1' => 'Não Adequado',
				'2' => 'Parcialmente Adequado',
				'3' => 'Adequado'
			),
		);

		$this->inputsHelper()->select('etapa_2', $options);

		$options = array(
			'required' => false,
			'label'    => "Avaliação Projeto Etapa 1",
			'inline'   => false,
			'value'     => $this->etapa_3,
			'resources' => array(
				'-1' => '3ª Etapa',
				'1' => 'Não Aprovado',
				'2' => 'Parcialmente Aprovado',
				'3' => 'Aprovado'
			),
		);

		$this->inputsHelper()->select('etapa_3', $options);
	}

	function Novo() {
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$cadastrou = $this->createOrUpdate();

		if ($cadastrou)
		{
			$this->mensagem .= 'Cadastro efetuado com sucesso.';
			header('Location: selecao_inscritos_lst.php');
			die();
		}

		$this->mensagem = Portabilis_String_utils::toLatin1('Cadastro não realizado.');

		return false;
	}

	function Editar() {
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$cadastrou = $this->createOrUpdate($this->cod_inscrito);

		if ($cadastrou)
		{
			$this->mensagem .= 'Edição efetuada com sucesso.';
			header('Location: selecao_inscritos_lst.php');
			die();
		}
	}

	function Excluir() {
		echo '<script>document.location="selecao_inscritos_lst.php";</script>';
		return TRUE;
	}

	function afterChangePessoa($id) {
	}

	// mostra uma dica nos casos em que foi informado apenas o nome dos pais,
	//pela antiga interface do cadastro de alunos.

	protected function validatesCpf($cpf) {
		$isValid = true;

		if ($cpf && ! Portabilis_Utils_Validation::validatesCpf($cpf))
		{
			$this->erros['cpf'] = 'CPF inválido.';
			$isValid = false;
		} else if($cpf) {
			$fisica      = new clsFisica();
			$fisica->cpf = idFederal2int($cpf);
			$fisica      = $fisica->detalhe();

			if ($fisica['cpf'] && $this->cod_inscrito != $fisica['idpes'])
			{
				$link = "<a class='decorated' target='__blank' href='/intranet/atendidos_cad.php?cod_inscrito=" .
				"{$fisica['idpes']}'>{$fisica['idpes']}</a>";

				$this->erros['cpf'] = "CPF já utilizado pela pessoa $link.";
				$isValid = false;
			}
		}

		return $isValid;
	}

	protected function createOrUpdate($pessoaIdOrNull = null)
	{
		if (!$this->validatesCpf($this->cpf) && !is_numeric($this->rg))
			return false;

		$pessoaId = $this->createOrUpdatePreInscrito($pessoaIdOrNull);

		$this->afterChangePessoa($pessoaId);

		return $pessoaId;
	}

	protected function createOrUpdatePreInscrito($pessoaId = null) {
		$preInscrito                     = new clsPreInscrito();

		if($this->pessoa_logada)
		{
			$preInscrito->ref_usuario_cad = $this->pessoa_logada;
		}
		$preInscrito->cod_inscrito = $pessoaId;
		$preInscrito->nome = $this->nome;
		$preInscrito->data_nasc = Portabilis_Date_Utils::brToPgSQL($this->data_nasc);
		$preInscrito->cpf = $this->cpf ? idFederal2int($this->cpf) : 'NULL';
		$preInscrito->rg = $this->rg;
		$preInscrito->email = $this->email;
		$preInscrito->ddd_telefone_1 = $this->ddd_telefone_1;
		$preInscrito->telefone_1 = $this->telefone_1;
		$preInscrito->ddd_telefone_2 = $this->ddd_telefone_2;
		$preInscrito->telefone_2 = $this->telefone_2;
		$preInscrito->ddd_telefone_mov = $this->ddd_telefone_mov;
		$preInscrito->telefone_mov = $this->telefone_mov;
		$preInscrito->bairro = $this->bairro;

		$preInscrito->sexo = $this->sexo;
		$preInscrito->serie = $this->serie;
		$preInscrito->turno = $this->turno;
		$preInscrito->egresso = $this->egresso;
		$preInscrito->indicacao = $this->indicacao;
		$preInscrito->nm_responsavel = $this->nm_responsavel;


		$preInscrito->ref_cod_escola = $this->ref_cod_escola;
		$preInscrito->ano = $this->ano;

		$preInscrito->etapa_1 = $this->etapa_1;
		$preInscrito->etapa_2 = $this->etapa_2;
		$preInscrito->etapa_3 = $this->etapa_3;

		$preInscrito->guarda_mirim = isset($this->guarda_mirim) ? 1 : 0;
		$preInscrito->encaminhamento = isset($this->encaminhamento) ? 1 : 0;
		$preInscrito->copia_rg = isset($this->copia_rg) ? 1 : 0;
		$preInscrito->copia_cpf = isset($this->copia_cpf) ? 1 : 0;
		$preInscrito->copia_residencia = isset($this->copia_residencia) ? 1 : 0;
		$preInscrito->copia_historico = isset($this->copia_historico) ? 1 : 0;
		$preInscrito->copia_renda = isset($this->copia_renda) ? 1 : 0;

		$sql = "select 1 from pmieducar.selecao_inscrito WHERE cod_inscrito = $1 limit 1";

		if (!$pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
			$pessoaId = $preInscrito->cadastra();
		else
			$preInscrito->edita();

		return $pessoaId;
	}

	// inputs usados em Gerar,
	// implementado estes metodos para não duplicar código
	// uma vez que estes campos são usados várias vezes em Gerar.

	protected function inputTelefone($type, $typeLabel = '') {
		if (! $typeLabel)
		$typeLabel = "Telefone {$type}";

		// ddd

		$options = array(
			'required'    => false,
			'label'       => "(ddd) / {$typeLabel}",
			'placeholder' => 'ddd',
			'value'       => $this->{"ddd_telefone_{$type}"},
			'max_length'  => 3,
			'size'        => 3,
			'inline'      => true
		);

		$this->inputsHelper()->integer("ddd_telefone_{$type}", $options);


		// telefone

		$options = array(
			'required'    => false,
			'label'       => '',
			'placeholder' => $typeLabel,
			'value'       => $this->{"telefone_{$type}"},
			'max_length'  => 11
		);

		$this->inputsHelper()->integer("telefone_{$type}", $options);
	}
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
