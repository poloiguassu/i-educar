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
        $this->SetTitulo("{$this->_instituicao} - Modelo Contratação");
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

    public $cod_vps_tipo_contratacao;
    public $ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Modelo Contratação - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos(
            [
                'Modelo Contratação',
                'Descrição',
                'Instituição'
            ]
        );

        // Filtros de Foreign Keys
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        // outros Filtros
        $this->campoTexto('nm_tipo', 'Modelo Contrata��o', $this->nm_tipo, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite-$this->limite: 0;

        $obj_vps_tipo_contratacao = new clsPmieducarVPSContratacaoTipo();
        $obj_vps_tipo_contratacao->setOrderby('nm_tipo ASC');
        $obj_vps_tipo_contratacao->setLimite($this->limite, $this->offset);

        $lista = $obj_vps_tipo_contratacao->lista(
            null,
            $this->ref_cod_instituicao,
            null,
            null,
            $this->nm_tipo,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_vps_tipo_contratacao->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_instituicao = $obj_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_instituicao['nm_instituicao'];

                $this->addLinhas([
                    "<a href=\"educar_vps_tipo_contratacao_det.php?cod_vps_tipo_contratacao={$registro['cod_vps_tipo_contratacao']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_vps_tipo_contratacao_det.php?cod_vps_tipo_contratacao={$registro['cod_vps_tipo_contratacao']}\">{$registro['descricao']}</a>",
                    "<a href=\"educar_vps_tipo_contratacao_det.php?cod_vps_tipo_contratacao={$registro['cod_vps_tipo_contratacao']}\">{$registro['ref_cod_instituicao']}</a>"
                ]);
            }
        }

        $this->addPaginador2('educar_vps_tipo_contratacao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(597, $this->pessoa_logada, 11)) {
            $this->acao = 'go("educar_vps_tipo_contratacao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
            'educar_vps_index.php'              => 'Trilha Jovem - VPS',
            ''                                  => 'Listagem de modelos de contrata��o'
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
