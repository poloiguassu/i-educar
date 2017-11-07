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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
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
		$this->SetTitulo('Processo Seletivo - Jovem');
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
			$this->addDetalhe(array('Gênero', $sexo));
		}

		if ($detalhe['nm_responsavel'])
		{
			$this->addDetalhe(array('Nome do Responsável', $detalhe['nm_responsavel']));
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
			$this->addDetalhe(array('Inscrito na Guarda Mirim?', ($detalhe['guarda_mirim'] == 1) ? 'sim' : 'não'));
		}

		$serie = array(
			'0' => 'Não definido',
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
		);

		if ($detalhe['serie'])
		{
			$this->addDetalhe(array('Serie', $serie[$detalhe['serie']]));
		}

		$turno = array(
			'0' => 'Não definido',
			'1' => 'Manhã',
			'2' => 'Tarde',
			'3' => 'Noite',
		);

		if ($detalhe['turno'])
		{
			$this->addDetalhe(array('Turno', $turno[$detalhe['turno']]));
		}

		if ($detalhe['egresso'])
		{
			$this->addDetalhe(array('Ano de Conclusão', $detalhe['egresso']));
		}

		if ($detalhe['copia_rg'])
		{
			$this->addDetalhe(array('Entregou Cópia do RG?', ($detalhe['copia_rg'] == 1) ? 'sim' : 'não'));
		}

		if ($detalhe['copia_cpf'])
		{
			$this->addDetalhe(array('Entregou Cópia do CPF?', ($detalhe['copia_cpf'] == 1) ? 'sim' : 'não'));
		}

		if ($detalhe['copia_residencia'])
		{
			$this->addDetalhe(array('Entregou Cópia do Comprovante de Residência?', ($detalhe['copia_residencia'] == 1) ? 'sim' : 'não'));
		}

		if ($detalhe['copia_historico'])
		{
			$this->addDetalhe(array('Entregou Histórico ou Declaração de Matrícula?', ($detalhe['copia_historico'] == 1) ? 'sim' : 'não'));
		}

		if ($detalhe['copia_renda'])
		{
			$this->addDetalhe(array('Entregou Comprovação de Renda Familiar?', ($detalhe['copia_renda'] == 1) ? 'sim' : 'não'));
		}

		$avaliacao = array(
			'1' => 'Não Adequado',
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
			$_SERVER['SERVER_NAME']."/intranet" => "Início",
			""                                  => "Detalhe do Jovem - Processo Seletivo"
		));

		$this->enviaLocalizacao($localizacao->montar());
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
