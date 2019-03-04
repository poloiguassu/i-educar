<?php
require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');
require_once('lib/App/Model/SimNao.php');
require_once('lib/App/Model/VivenciaProfissionalSituacao.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Lista de Visitas");
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

    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Visitas - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Aluno',
            'Empresa',
            'Entrevista',
            'Responsável Visita',
            'Data da visita',
            'Avaliacao'
        ]);

        // outros Filtros
        $obj_visita = new clsPmieducarVPSVisita();

        $lista = $obj_visita->lista();

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $cod_vps_visita	= $registro['cod_vps_visita'];
                $data_visita	= '';
                $avaliacao		= '';
                $funcao			= '';
                $nm_aluno		= '';
                $nm_empresa		= '';
                $nm_usuario		= '';

                $alunoEntrevista = new clsPmieducarVPSAlunoEntrevista($registro['ref_cod_vps_aluno_entrevista']);
                $registroAlunoEntrevista = $alunoEntrevista->detalhe();

                $ref_cod_vps_entrevista = $registroAlunoEntrevista['ref_cod_vps_entrevista'];

                if ($registroAlunoEntrevista['ref_cod_aluno']) {
                    $alunoVPS = new clsPmieducarAluno($registroAlunoEntrevista['ref_cod_aluno']);

                    if ($alunoVPS && $alunoVPS->existe()) {
                        $registroAluno = $alunoVPS->detalhe();

                        if (class_exists('clsPessoaFj')) {
                            $obj_ref_idpes = new clsPessoaFj($registroAluno['ref_idpes']);
                            $det_ref_idpes = $obj_ref_idpes->detalhe();
                            $nm_aluno = $det_ref_idpes['nome'];
                        } else {
                            $registro['ref_idpes'] = 'Erro na geracao';
                            echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
                        }
                    }
                }

                if (class_exists('clsPessoa_')) {
                    $obj_cod_usuario = new clsPessoa_($registro['ref_cod_usuario']);
                    $obj_usuario_det = $obj_cod_usuario->detalhe();
                    $nm_usuario = $obj_usuario_det['nome'];
                }

                if ($ref_cod_vps_entrevista) {
                    $entrevista = new clsPmieducarVPSEntrevista($ref_cod_vps_entrevista);
                    $registroEntrevista = $entrevista->detalhe();

                    if (class_exists('clsPmieducarVPSFuncao')) {
                        $obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registroEntrevista['ref_cod_vps_funcao']);
                        $det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
                        $funcao = $det_ref_cod_vps_funcao['nm_funcao'];
                    } else {
                        $funcao = 'Erro na geracao';
                        echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
                    }

                    if (class_exists('clsPessoaFj')) {
                        $empresa_id = $registroEntrevista['ref_idpes'];
                        $obj_ref_idpes = new clsPessoaFj($empresa_id);
                        $det_ref_idpes = $obj_ref_idpes->detalhe();
                        $nm_empresa = $det_ref_idpes['nome'];
                    } else {
                        $registro['ref_idpes'] = 'Erro na geracao';
                        echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
                    }
                }

                if ($registro['data_visita']) {
                    $data_visita = Portabilis_Date_Utils::pgSQLToBr($registro['data_visita']);
                    if ($registr['hora_visita']) {
                        $data_visita .= " Às {$registr['hora_visita']}";
                    }
                }

                if (is_numeric($registro['avaliacao'])) {
                    $avaliacao = App_Model_SimNao::getInstance()->getValue($registro['avaliacao']);
                }

                $lista_busca = [
                    "<a href=\"educar_vps_visita_det.php?cod_vps_visita={$cod_vps_visita}\">{$nm_aluno}</a>",
                    "<a href=\"educar_vps_visita_det.php?cod_vps_visita={$cod_vps_visita}\">{$nm_empresa}</a>",
                    "<a href=\"educar_vps_visita_det.php?cod_vps_visita={$cod_vps_visita}\">{$funcao}</a>",
                    "<a href=\"educar_vps_visita_det.php?cod_vps_visita={$cod_vps_visita}\">{$nm_usuario}</a>",
                    "<a href=\"educar_vps_visita_det.php?cod_vps_visita={$cod_vps_visita}\">{$data_visita}</a>",
                    "<a href=\"educar_vps_visita_det.php?cod_vps_visita={$cod_vps_visita}\">{$avaliacao}</a>",
                ];

                $this->addLinhas($lista_busca);
            }
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11)) {
            $this->acao = sprintf(
                '
				go("educar_vps_aluno_lst.php?busca=S&situacao_vps=%d");',
                App_Model_VivenciaProfissionalSituacao::EM_CUMPRIMENTO
            );
            $this->nome_acao = 'Agendar Visita';
        }

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
            'educar_index.php'                  => 'Trilha Jovem Iguassu - VPS',
            ''                                  => 'Listagem de Visitas'
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
