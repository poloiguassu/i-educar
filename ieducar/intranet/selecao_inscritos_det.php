<?php

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
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

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
		$this->SetTitulo('Processo Seletivo - Jovem');
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
class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = 'Detalhe da Jovem - Processo Seletivo';

		$cod_inscrito = @$_GET['cod_pessoa'];

		echo "pq? " . $cod_inscrito;
		$objPessoa = new clsPreInscrito($cod_inscrito);
		$db        = new clsBanco();

		$detalhe = $objPessoa->detalhe();

		$this->addDetalhe(array('Nome', $detalhe['nome']));

		if ($detalhe['cpf'])
		{
			$this->addDetalhe(array('CPF', int2cpf($detalhe['cpf'])));
		}

		if ($detalhe['rg'])
		{
			$this->addDetalhe(array('RG', $detalhe['rg']));
		}

		if ($detalhe['data_nasc'])
		{
			$this->addDetalhe(array('Data de Nascimento', dataFromPgToBr($detalhe['data_nasc'])));
		}

		if ($detalhe['sexo'])
		{
			$sexo = $detalhe['sexo'] == 'M' ? 'Masculino' : 'Feminino';
			$this->addDetalhe(array('G�nero', $sexo));
		}

		if ($detalhe['nm_responsavel'])
		{
			$this->addDetalhe(array('Nome do Respons�vel', $detalhe['nm_responsavel']));
		}

		if ($detalhe['ddd_telefone_1'] && $detalhe['telefone_1'])
		{
			$telefone = '(' . $detalhe['ddd_telefone_1'] . ') ' . $detalhe['telefone_1'];
			$this->addDetalhe(array('Telefone 1', $telefone));
		}

		if ($detalhe['ddd_telefone_2'] && $detalhe['telefone_2'])
		{
			$telefone = '(' . $detalhe['ddd_telefone_2'] . ') '  . $detalhe['telefone_2'];
			$this->addDetalhe(array('Telefone 2', $telefone));
		}

		if ($detalhe['ddd_telefone_mov'] && $detalhe['telefone_mov'])
		{
			$telefone = '(' . $detalhe['ddd_telefone_mov'] . ') '  . $detalhe['telefone_mov'];
			$this->addDetalhe(array('Telefone Celular', $telefone));
		}

		if ($detalhe['email'])
		{
			$this->addDetalhe(array('E-mail', $detalhe['email']));
		}

		if ($detalhe['indicacao'])
		{
			$this->addDetalhe(array('Como ficou sabendo do projeto?', $detalhe['indicacao']));
		}

		if ($detalhe['guarda_mirim'])
		{
			$this->addDetalhe(array('Inscrito na Guarda Mirim?', ($detalhe['guarda_mirim'] == 1) ? 'sim' : 'n�o'));
		}

		$serie = array(
			'0' => 'N�o definido',
			'1' => '5� s�rie',
			'2' => '6� s�rie',
			'3' => '7� s�rie',
			'4' => '8� s�rie',
			'5' => '9� s�rie',
			'6' => '1� ano Ensino M�dio',
			'7' => '2� ano Ensino M�dio',
			'8' => '3� ano Ensino M�dio',
			'9' => 'Concluido',
			'10' => 'EJA'
		);

		if ($detalhe['serie'])
		{
			$this->addDetalhe(array('Serie', $serie[$detalhe['serie']]));
		}

		$turno = array(
			'0' => 'N�o definido',
			'1' => 'Manh�',
			'2' => 'Tarde',
			'3' => 'Noite',
		);

		if ($detalhe['turno'])
		{
			$this->addDetalhe(array('Turno', $turno[$detalhe['turno']]));
		}

		if ($detalhe['egresso'])
		{
			$this->addDetalhe(array('Ano de Conclus�o', $detalhe['egresso']));
		}

		if ($detalhe['copia_rg'])
		{
			$this->addDetalhe(array('Entregou C�pia do RG?', ($detalhe['copia_rg'] == 1) ? 'sim' : 'n�o'));
		}

		if ($detalhe['copia_cpf'])
		{
			$this->addDetalhe(array('Entregou C�pia do CPF?', ($detalhe['copia_cpf'] == 1) ? 'sim' : 'n�o'));
		}

		if ($detalhe['copia_residencia'])
		{
			$this->addDetalhe(array('Entregou C�pia do Comprovante de Resid�ncia?', ($detalhe['copia_residencia'] == 1) ? 'sim' : 'n�o'));
		}

		if ($detalhe['copia_historico'])
		{
			$this->addDetalhe(array('Entregou Hist�rico ou Declara��o de Matr�cula?', ($detalhe['copia_historico'] == 1) ? 'sim' : 'n�o'));
		}

		if ($detalhe['copia_renda'])
		{
			$this->addDetalhe(array('Entregou Comprova��o de Renda Familiar?', ($detalhe['copia_renda'] == 1) ? 'sim' : 'n�o'));
		}

		$avaliacao = array(
			'1' => 'N�o Adequado',
			'2' => 'Parcialmente Adequado',
			'3' => 'Adequado'
		);

		if ($detalhe['etapa_1'])
		{
			$this->addDetalhe(array('Etapa 1', $avaliacao[$detalhe['etapa_1']]));
		}

		$this->url_novo     = 'selecao_inscritos_cad.php';
		$this->url_editar   = 'selecao_inscritos_cad.php?cod_inscrito=' . $cod_inscrito;
		$this->url_cancelar = 'selecao_inscritos_lst.php';

		$this->largura = '100%';

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME']."/intranet" => "In�cio",
			""                                  => "Detalhe do Jovem - Processo Seletivo"
		));

		$this->enviaLocalizacao($localizacao->montar());
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
