<?php
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pessoa/clsPreInscrito.inc.php';

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "Jovens Inscritos Processo Seletivo" );
		$this->processoAp = "43";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Jovens Inscritos Processo Seletivo";


		$this->addCabecalhos(array( "Nome", "CPF", "Turno", "Idade", "Telefone", "Etapa 1"));

		$this->campoTexto( "nm_inscrito", "Nome",  $_GET['nm_inscrito'], "50", "255", true );
		$this->campoCpf( "id_federal", "CPF",  $_GET['id_federal'], "50", "", true );

		$options = array(
			'required' => false,
			'label'    => "Avaliação Projeto Etapa 1",
			'value'     => $_GET['etapa_1'],
			'resources' => array(
				'' => '1ª Etapa',
				'1' => 'Não Adequado',
				'2' => 'Parcialmente Adequado',
				'3' => 'Adequado'
			),
		);

		$this->inputsHelper()->select('etapa_1', $options);

		$where = "";

		$par_nome = false;

		if ($_GET['nm_inscrito'])
		{
			$par_nome = $_GET['nm_inscrito'];
		}

		$par_id_federal = false;

		if ($_GET['id_federal'])
		{
			$par_id_federal = idFederal2Int($_GET['id_federal']);
		}

		$par_etapa_1 = null;

		if($_GET['etapa_1'])
		{
			$par_etapa_1 = $_GET['etapa_1'];
		}

		$dba = $db = new clsBanco();

		$objPessoa = new clsPreInscrito();

		// Paginador
		$limite = 40;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $limite-$limite: 0;

		$turno_campo = array(
			'0' => 'Não definido',
			'1' => 'Manhã',
			'2' => 'Tarde',
			'3' => 'Noite'
		);

		$avaliacao = array(
			'1' => 'Não Adequado',
			'2' => 'Parcialmente Adequado',
			'3' => 'Adequado'
		);

		$pessoas = $objPessoa->lista($par_etapa_1, null, $par_nome, $par_id_federal, null, $iniciolimit, $limite);

		if($pessoas)
		{
			foreach ($pessoas as $pessoa)
			{
				$cod = $pessoa['cod_inscrito'];
				$nome = $pessoa['nome'];
				$total = $pessoa['total'];
				$cpf = $pessoa['cpf'] ? int2CPF($pessoa['cpf']) : "";

				if($pessoa['egresso'] > 0)
				{
					$turno = "Egresso " . $pessoa['egresso'];
				} else {
					$turno = $turno_campo[$pessoa['turno']];
				}

				$data = $pessoa['data_nasc'];

				list($ano, $mes, $dia) = explode('-', $data);

				$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				$nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);

				$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);

				$telefone = "";
				if($pessoa['telefone_1'])
				{
					$telefone = "({$pessoa['ddd_telefone_1']}) {$pessoa['telefone_1']}";
				} else {
					$telefone = "({$pessoa['ddd_telefone_2']}) {$pessoa['telefone_2']}";
				}

				$etapa_1 = $avaliacao[$pessoa['etapa_1']];

				$this->addLinhas( array("<img src='imagens/noticia.jpg' border=0><a href='selecao_inscritos_det.php?cod_pessoa={$cod}'>$nome</a>", $cpf, $turno, $idade, $telefone, $etapa_1 ) );
			}
		}

		$this->acao = "go(\"selecao_inscritos_cad.php\")";
		$this->nome_acao = "Novo";

		$this->array_botao_url[] = 'selecao_exportar_selecionados.php';
		$this->array_botao[]     = 'Exportar Selecionados';

		$this->largura = "100%";
		$this->addPaginador2( "selecao_inscritos_lst.php", $total, $_GET, $this->nome, $limite );

		$localizacao = new LocalizacaoSistema();
		$localizacao->entradaCaminhos( array(
			$_SERVER['SERVER_NAME']."/intranet" => "Início",
			""                                  => "Listagem de Inscritos Processo Seletivo"
		));

		$this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
