<?php

require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');
require_once('include/localizacaoSistema.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Fun��o");
        $this->processoAp = 21455;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_vps_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_escola;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Função - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos(
            [
                'Função',
                'Instituição'
            ]
        );

        // Filtros de Foreign Keys
        $get_escola = false;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->campoTexto('nm_funcao', 'Função', $this->nm_funcao, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite: 0;

        $obj_vps_funcao = new clsPmieducarVPSFuncao();
        $obj_vps_funcao->setOrderby('nm_funcao ASC');
        $obj_vps_funcao->setLimite($this->limite, $this->offset);

        $lista = $obj_vps_funcao->lista(
            $this->cod_vps_funcao,
            null,
            null,
            $this->nm_funcao,
            $this->descricao,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_escola
        );

        $total = $obj_vps_funcao->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
                $det_escola = $obj_escola->detalhe();
                $idpes = $det_escola['ref_idpes'];

                if ($idpes) {
                    $obj_escola = new clsPessoaJuridica($idpes);
                    $obj_escola_det = $obj_escola->detalhe();
                    $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];
                } else {
                    $obj_escola = new clsPmieducarEscolaComplemento($registro['ref_cod_escola']);
                    $obj_escola_det = $obj_escola->detalhe();
                    $registro['ref_cod_escola'] = $obj_escola_det['nm_escola'];
                }

                $this->addLinhas([
                    "<a href=\"educar_vps_funcao_det.php?cod_vps_funcao={$registro['cod_vps_funcao']}\">{$registro['nm_funcao']}</a>",
                    "<a href=\"educar_vps_funcao_det.php?cod_vps_funcao={$registro['cod_vps_funcao']}\">{$registro['ref_cod_escola']}</a>"
                ]);
            }
        }

        $this->addPaginador2('educar_vps_funcao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(593, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_vps_funcao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem - VPS',
            ''                                    => 'Listagem de Funções'
        ]);

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
