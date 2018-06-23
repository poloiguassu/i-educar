<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
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
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo($this->_instituicao . ' Inscritos no Processo Seletivo - Situa��o');
		$this->processoAp = 43;
		$this->addEstilo('localizacaoSistema');
	}
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
	var $cod_inscrito;
	var $nm_inscrito;
	var $data_nasc;
	var $id_federal;
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

	var $caminho_det;
	var $caminho_lst;

	var $lista_pessoas;

	function Inicializar()
	{
		//$this->cod_inscrito = @$_GET['cod_inscrito'];
		$this->retorno       = 'Novo';
		$this->etapa = @$_GET['etapa'];
		$aprovados = @$_GET['aprovados'];

		$objPessoa = new clsPreInscrito();
		$pessoas = $objPessoa->lista();


		if($aprovados >= 1)
		{
			foreach ($pessoas as $key => $pessoa)
			{
				if($pessoa['etapa_1'] == 1)
				{
					unset($pessoas[$key]);
				}
			}
		}

		$this->pessoas = $pessoas;

		if($pessoas)
		{

			foreach ($pessoas as $pessoa)
			{
				$pessoaId = $pessoa['cod_inscrito'];
				$objPessoa = new clsPreInscrito($pessoaId);
				$detalhe = $objPessoa->detalhe();
				$this->{"etapa_1_{$pessoaId}"} = $detalhe['etapa_1'];
				$this->{"etapa_2_{$pessoaId}"} = $detalhe['etapa_2'];
				$this->{"etapa_3_{$pessoaId}"} = $detalhe['etapa_3'];
			}
		}

		$this->nome_url_cancelar = 'Cancelar';

		$nomeMenu = $this->retorno == "Editar" ? $this->retorno : "Cadastrar";
		$localizacao = new LocalizacaoSistema();

		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME'] . "/intranet" => "In�cio",
			""                                    => "$nomeMenu Inscrito Processo Seletivo"
		));

		$this->enviaLocalizacao($localizacao->montar());

		return $this->retorno;
	}

	function Gerar()
	{
		$this->url_cancelar = $this->retorno == 'Editar' ?
			'selecao_inscritos_det.php?cod_pessoa=' . $this->cod_inscrito : 'selecao_inscritos_lst.php';

		$limite = 999;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;

		if($this->pessoas)
		{
			$index = 1;
			$lista_pessoas = array();
			foreach ($this->pessoas as $pessoa)
			{
				// nome
				$pessoaId = $pessoa['cod_inscrito'];
				//print($pessoaId);
				$options = array('label' => $index . '. Nome ',
								'disabled' => true,
								'required' => false,
								'size' => 50,
								'inline' => true,
								'value' => $pessoa['nome']);
				$this->inputsHelper()->text('nome_' . $pessoaId, $options);
				//$this->campoTexto('nm_inscrito' . $index, 'Nome', $pessoa['nome'], '50', '255', false);


				if(!$this->etapa || $this->etapa == 1)
				{

					$options = array(
						'required'	=> false,
						'label'     => null,
						'inline' => !$this->etapa,
						'value'     => $this->{"etapa_1_{$pessoaId}"},
						'resources' => array(
							'' => '1� Etapa',
							'1' => 'N�o Adequado',
							'2' => 'Parcialmente Adequado',
							'3' => 'Adequado'
						),
					);

					$this->inputsHelper()->select('etapa_1_' . $pessoaId, $options);
				}

				if(!$this->etapa || $this->etapa == 2)
				{
					$options = array(
						'required'	=> false,
						'label'     => null,
						'inline' => !$this->etapa,
						'value'     => $this->{"etapa_2_{$pessoaId}"},
						'resources' => array(
							'' => '2� Etapa',
							'1' => 'N�o Adequado',
							'2' => 'Parcialmente Adequado',
							'3' => 'Adequado'
						),
					);

					$this->inputsHelper()->select('etapa_2_' . $pessoaId, $options);
				}

				if(!$this->etapa || $this->etapa == 3)
				{
					$options = array(
						'required'	=> false,
						'label'     => null,
						'value'     => $this->{"etapa_3_{$pessoaId}"},
						'resources' => array(
							'' => '3� Etapa',
							'1' => 'N�o Adequado',
							'2' => 'Parcialmente Adequado',
							'3' => 'Adequado'
						),
					);

					$this->inputsHelper()->select('etapa_3_' . $pessoaId, $options);
				}

				array_push($lista_pessoas, $pessoaId);
				$index++;
			}
			$this->lista_pessoas = implode(",", $lista_pessoas);

			$this->campoOculto('lista_pessoas', $this->lista_pessoas);
		}
	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = +$_SESSION['id_pessoa'];
		@session_write_close();

		$this->updateState();

		$this->mensagem .= 'Cadastro efetuado com sucesso.';
		header('Location: selecao_situacao_cad.php');
		die();

		$this->mensagem = Portabilis_String_utils::toLatin1('Cadastro n�o realizado.');

		return false;
	}

	function Editar()
	{
		$this->updateState();
	}

	function Excluir()
	{
	}

	function afterChangePessoa($id)
	{
	}

	protected function updateState()
	{
		//print($this->lista_pessoas);
		$pessoas = explode("%2C", $this->lista_pessoas);
		//print_r($pessoas);

		if($pessoas)
		{
			foreach ($pessoas as $key => $pessoa)
			{

				$pessoaId = $pessoa;
				if($this->{"etapa_1_{$pessoaId}"} || $this->{"etapa_2_{$pessoaId}"} || $this->{"etapa_3_{$pessoaId}"})
				{

					$preInscrito = new clsPreInscrito();
					$preInscrito->cod_inscrito = $pessoaId;

					$preInscrito->etapa_1 = $this->{"etapa_1_{$pessoaId}"};
					$preInscrito->etapa_2 = $this->{"etapa_2_{$pessoaId}"};
					$preInscrito->etapa_3 = $this->{"etapa_3_{$pessoaId}"};

					$sql = "select 1 from pmieducar.selecao_inscrito WHERE cod_inscrito = $1 limit 1";

					if ($pessoaId /*&& Portabilis_Utils_Database::selectField($sql, $pessoaId) == 1*/)
					{
						////print_r("{$pessoaId},");
						//print_r($pessoa . " => " . $preInscrito->etapa_2 . ', ');
						$preInscrito->edita();
					}
				}
			}
		}
	}
}

// Instancia objeto de p�gina
$pagina = new clsIndex();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
