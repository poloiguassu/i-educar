<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/Geral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Institui&ccedil;&atilde;o");
        $this->processoAp = "559";
        $this->addEstilo("localizacaoSistema");
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_instituicao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idtlog;
    var $ref_sigla_uf;
    var $cep;
    var $cidade;
    var $bairro;
    var $logradouro;
    var $numero;
    var $complemento;
    var $nm_responsavel;
    var $ddd_telefone;
    var $telefone;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $nm_instituicao;
    var $data_base_transferencia;
    var $data_base_remanejamento;
    var $exigir_vinculo_turma_professor;
    var $exigir_dados_socioeconomicos;
    var $controlar_espaco_utilizacao_aluno;
    var $percentagem_maxima_ocupacao_salas;
    var $quantidade_alunos_metro_quadrado;
    var $gerar_historico_transferencia;
    var $controlar_posicao_historicos;
    var $matricula_apenas_bairro_escola;
    var $restringir_historico_escolar;
    var $restringir_multiplas_enturmacoes;
    var $permissao_filtro_abandono_transferencia;
    var $multiplas_reserva_vaga;
    var $permitir_carga_horaria;
    var $reserva_integral_somente_com_renda;
    var $data_base_matricula;
    var $data_expiracao_reserva_vaga;
    var $data_fechamento;
    var $componente_curricular_turma;
    var $reprova_dependencia_ano_concluinte;
    var $bloqueia_matricula_serie_nao_seguinte;
    var $data_educacenso;
    var $altera_atestado_para_declaracao;
    var $obrigar_campos_censo;
    var $orgao_regional;

    function Inicializar()
    {
        $retorno = "Novo";
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(559, $this->pessoa_logada, 3, "educar_instituicao_lst.php");

        $this->cod_instituicao = $_GET["cod_instituicao"];

        if (is_numeric($this->cod_instituicao)) {

            $obj = new clsPmieducarInstituicao($this->cod_instituicao);
            $registro = $obj->detalhe();
            if ($registro) {
                foreach ($registro AS $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $this->fexcluir = $obj_permissoes->permissao_excluir(559, $this->pessoa_logada, 3);
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_instituicao_det.php?cod_instituicao={$registro["cod_instituicao"]}" : "educar_instituicao_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
             ""        => "{$nomeMenu} institui&ccedil;&atilde;o"
        ));
        $this->enviaLocalizacao($localizacao->montar());

		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_instituicao", $this->cod_instituicao );

		// text
		$this->campoTexto( "nm_instituicao", "Nome da Institui��o", $this->nm_instituicao, 30, 255, true );
		$this->campoCep( "cep", "CEP", int2CEP( $this->cep ), true, "-", false, false );
		$this->campoTexto( "logradouro", "Logradouro", $this->logradouro, 30, 255, true );
		$this->campoTexto( "bairro", "Bairro", $this->bairro, 30, 40, true );
		$this->campoTexto( "cidade", "Cidade", $this->cidade, 30, 60, true );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsTipoLogradouro" ) )
		{
			$objTemp = new clsTipoLogradouro();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUrbanoTipoLogradouro nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_idtlog", "Tipo do Logradouro", $opcoes, $this->ref_idtlog, "", false, "", "", false, true );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUf" ) )
		{
			$objTemp = new clsUf();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				asort($lista);
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['sigla_uf']}"] = "{$registro['sigla_uf']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_sigla_uf", "UF", $opcoes, $this->ref_sigla_uf, "", false, "", "", false, true );

		$this->campoNumero( "numero", "N�mero", $this->numero, 6, 6 );
		$this->campoTexto( "complemento", "Complemento", $this->complemento, 30, 50, false );
		$this->campoTexto( "nm_responsavel", "Nome do Respons�vel", $this->nm_responsavel, 30, 255, true );
		$this->campoNumero( "ddd_telefone", "DDD Telefone", $this->ddd_telefone, 2, 2 );
		$this->campoNumero( "telefone", "Telefone", $this->telefone, 11, 11 );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInstituicao( null, $this->ref_usuario_exc, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, str_replace( "-", "", $this->cep ), $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, 1, $this->nm_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarInstituicao\nvalores obrigatorios\nis_numeric( $ref_usuario_cad ) && is_string( $ref_idtlog ) && is_string( $ref_sigla_uf ) && is_numeric( $cep ) && is_string( $cidade ) && is_string( $bairro ) && is_string( $logradouro ) && is_string( $nm_responsavel ) && is_string( $data_cadastro ) && is_numeric( $ativo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPmieducarInstituicao( $this->cod_instituicao, $this->ref_usuario_exc, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, str_replace( "-", "", $this->cep ), $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, 1, $this->nm_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarInstituicao\nvalores obrigatorios\nif( is_numeric( $this->cod_instituicao ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInstituicao($this->cod_instituicao, $this->pessoa_logada, $this->ref_usuario_cad, $this->ref_idtlog, $this->ref_sigla_uf, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->complemento, $this->nm_responsavel, $this->ddd_telefone, $this->telefone, $this->data_cadastro, $this->data_exclusao, $this->ativo);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_instituicao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarInstituicao\nvalores obrigatorios\nif( is_numeric( $this->cod_instituicao ) )\n-->";
		return false;
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
<script type="text/javascript">

    $j('#controlar_espaco_utilizacao_aluno').click(onControlarEspacoUtilizadoClick);

    if (!$j('#controlar_espaco_utilizacao_aluno').prop('checked')) {
        $j('#percentagem_maxima_ocupacao_salas').closest('tr').hide();
        $j('#quantidade_alunos_metro_quadrado').closest('tr').hide();
    }

    function onControlarEspacoUtilizadoClick() {
        if (!$j('#controlar_espaco_utilizacao_aluno').prop('checked')) {
            $j('#percentagem_maxima_ocupacao_salas').val('');
            $j('#quantidade_alunos_metro_quadrado').val('');
            $j('#percentagem_maxima_ocupacao_salas').closest('tr').hide();
            $j('#quantidade_alunos_metro_quadrado').closest('tr').hide();
        } else {
            $j('#percentagem_maxima_ocupacao_salas').closest('tr').show();
            $j('#quantidade_alunos_metro_quadrado').closest('tr').show();
        }
    }

    let populaOrgaoRegional = data => {
        $j('#orgao_regional').append(
            $j('<option/>').text('Selecione').val('')
        );
        if (data.orgaos) {
            $j.each(data.orgaos, function(){
                $j('#orgao_regional').append(
                    $j('<option/>').text(this.codigo).val(this.codigo)
                );
            });
        }
    }

    $j('#ref_sigla_uf').on('change', function(){
        let sigla_uf = this.value;
        $j('#orgao_regional').html('');
        if (sigla_uf) {
            let parametros = {
                oper: 'get',
                resource: 'orgaos_regionais',
                sigla_uf: sigla_uf
            };
            let link = '../module/Api/EducacensoOrgaoRegional';
            $j.getJSON(link, parametros)
            .done(populaOrgaoRegional);
        } else {
            $j('#orgao_regional').html('<option value="" selected>Selecione uma UF</option>');
        }
    });

    $j('#data_base').mask("99/99");
    $j('#data_fechamento').mask("99/99");

</script>
