<?php

require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} - Entrevistas");
        $this->processoAp = 21455;
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
    public $titulo;

    public $cod_vps_entrevista;
    public $ref_cod_exemplar_tipo;
    public $ref_cod_vps_entrevista;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_vps_funcao;
    public $ref_cod_vps_jornada_trabalho;
    public $ref_cod_tipo_contratacao;
    public $empresa_id;
    public $descricao;
    public $data_entrevista;
    public $hora_entrevista;
    public $ano;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $checked;

    public $vps_entrevista_responsavel;
    public $ref_cod_vps_responsavel_entrevista;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Entrevistas - Detalhe';

        $this->cod_vps_entrevista=$_GET['cod_vps_entrevista'];

        $tmp_obj = new clsPmieducarVPSEntrevista($this->cod_vps_entrevista);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            header('location: educar_entrevista_lst.php');
            die();
        }

        if (class_exists('clsPmieducarEscola')) {
            $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $idpes = $det_ref_cod_escola['ref_idpes'];
            if ($idpes) {
                $obj_escola = new clsPessoaJuridica($idpes);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['fantasia'];
            } else {
                $obj_escola = new clsPmieducarEscolaComplemento($registro['ref_cod_escola']);
                $obj_escola_det = $obj_escola->detalhe();
                $registro['ref_cod_escola'] = $obj_escola_det['nm_escola'];
            }
            if (class_exists('clsPmieducarInstituicao')) {
                $registro['ref_cod_instituicao'] = $det_ref_cod_escola['ref_cod_instituicao'];
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
            } else {
                $registro['ref_cod_instituicao'] = 'Erro na geracao';
                echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
            }
        } else {
            $registro['ref_cod_escola'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
        }

        if (class_exists('clsPmieducarCurso')) {
            $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
            $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
            $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
        } else {
            $registro['ref_cod_curso'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
        }

        if (class_exists('clsPmieducarVPSFuncao')) {
            $obj_ref_cod_vps_funcao = new clsPmieducarVPSFuncao($registro['ref_cod_vps_funcao']);
            $det_ref_cod_vps_funcao = $obj_ref_cod_vps_funcao->detalhe();
            $registro['ref_cod_vps_funcao'] = $det_ref_cod_vps_funcao['nm_funcao'];
        } else {
            $registro['ref_cod_vps_funcao'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSFuncao\n-->";
        }

        if (class_exists('clsPmieducarVPSJornadaTrabalho')) {
            $obj_ref_cod_vps_jornada_trabalho = new clsPmieducarVPSJornadaTrabalho($registro['ref_cod_vps_jornada_trabalho']);
            $det_ref_cod_vps_jornada_trabalho = $obj_ref_cod_vps_jornada_trabalho->detalhe();
            $registro['ref_cod_vps_jornada_trabalho'] = $det_ref_cod_vps_jornada_trabalho['nm_jornada_trabalho'];
        } else {
            $registro['ref_cod_vps_jornada_trabalho'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPmieducarVPSJornadaTrabalho\n-->";
        }

        if (class_exists('clsPessoaFj')) {
            $obj_ref_idpes = new clsPessoaFj($registro['ref_idpes']);
            $det_ref_idpes = $obj_ref_idpes->detalhe();
            $registro['ref_idpes'] = $det_ref_idpes['nome'];
        } else {
            $registro['ref_idpes'] = 'Erro na geracao';
            echo "<!--\nErro\nClasse nao existente: clsPessoaFj\n-->";
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(['Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Projeto', "{$registro['ref_cod_curso']}"]);
        }
        if ($registro['ano']) {
            $this->addDetalhe(['Ano', "{$registro['ano']}"]);
        }
        if ($registro['ref_idpes']) {
            $this->addDetalhe(['Empresa', "{$registro['ref_idpes']}"]);
        }
        if ($registro['ref_cod_vps_funcao']) {
            $this->addDetalhe(['Função', "{$registro['ref_cod_vps_funcao']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe(['Descrição', "{$registro['descricao']}"]);
        }
        if ($registro['salario']) {
            $valor = 'R$ ' . number_format($registro['salario'], 2, ',', '.');
            $this->addDetalhe(['Salário', "{$valor}"]);
        }
        if ($registro['numero_vagas']) {
            $valor = $registro['numero_vagas'];
            $this->addDetalhe(['Número de vagas', "{$valor} vagas"]);
        }
        if ($registro['numero_jovens']) {
            $valor = $registro['numero_jovens'];
            $this->addDetalhe(['Número de jovens por vaga', "{$valor} jovens"]);
        }
        if ($registro['data_entrevista']) {
            $data = Portabilis_Date_Utils::pgSQLToBr($registro['data_entrevista']);
            $this->addDetalhe(['Data da entrevista', "{$data}"]);
        }
        if ($registro['hora_entrevista']) {
            $this->addDetalhe(['Hora da entrevista', "{$registro['hora_entrevista']}"]);
        }
        if ($registro['ref_cod_vps_jornada_trabalho']) {
            $this->addDetalhe(['Jornada de trabalho', "{$registro['ref_cod_vps_jornada_trabalho']}"]);
        }

        $obj = new clsPmieducarVPSEntrevistaResponsavel();
        $obj->setOrderby('principal DESC');
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

            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=#E4E9ED ';
                } else {
                    $color = ' bgcolor=#FFFFFF ';
                }

                $obj = new clsPmieducarVPSResponsavelEntrevista($valor['ref_cod_vps_responsavel_entrevista']);
                $det = $obj->detalhe();
                $nm_autor = $det['nm_responsavel'];
                $principal = $valor['principal'];

                if ($principal == 1) {
                    $principal = 'sim';
                } else {
                    $principal = 'n�o';
                }

                $tabela .= "<tr>
								<td {$color} align=left>{$nm_autor}</td>
								<td {$color} align=left>{$principal}</td>
							</tr>";
                $cont++;
            }
            $tabela .= '</table>';
        }
        if ($tabela) {
            $this->addDetalhe(['Responsável', "{$tabela}"]);
        }

        $obj = new clsPmieducarVPSIdioma();
        $obj = $obj->listaIdiomasEntrevista($this->cod_vps_entrevista);

        if (count($obj)) {
            foreach ($obj as $reg) {
                $assuntos.= '<span style="background-color: #A1B3BD; padding: 2px;"><b>' . $reg['nome'] . '</b></span>&nbsp; ';
            }
            if (!empty($assuntos)) {
                $this->addDetalhe(['Idiomas necessários', "{$assuntos}"]);
            }
        }

        $entrevistas = new clsPmieducarVPSAlunoEntrevista(null, null, $this->cod_vps_entrevista);
        $todasEntrevistas = $entrevistas->lista();

        if (count($todasEntrevistas)) {
            $assuntos = '';

            $tabela =
                '<table class="table sub table-striped table-bordered" cellspacing="0" width="30%">
					<thead>
						<tr align=center>
							<td bgcolor=#A1B3BD><B>Nome</B></td>
						</tr>
					</thead>';
            $cont = 0;

            foreach ($todasEntrevistas as $valor) {
                $nm_jovem = strtoupper($valor['nome']);
                $id_jovem = $valor['ref_cod_aluno'];

                $tabela .= "<TR>
								<TD {$color} align=left><a href='/intranet/educar_aluno_det.php?cod_aluno={$id_jovem}' target ='_blank'>{$nm_jovem}</a></TD>
							</TR>";
                $cont++;
            }

            $tabela .= '</TABLE>';

            if ($tabela) {
                $this->addDetalhe(['Entrevistados', "{$tabela}"]);
            }
            if (!empty($assuntos)) {
                $this->addDetalhe(['Jovens entrevistados', "{$assuntos}"]);
            }
        }

        if ($todasEntrevistas) {
            $index = 1;

            foreach ($todasEntrevistas as $campo => $val) {
                $this->{'aluno' . $index . '_id'} = $val['ref_cod_aluno'];
                $index++;
            }
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(598, $this->pessoa_logada, 11)) {
            $this->url_novo = 'educar_entrevista_cad.php';
            $this->url_editar = "educar_entrevista_cad.php?cod_vps_entrevista={$registro['cod_vps_entrevista']}";
        }

        $this->url_cancelar = 'educar_entrevista_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'] . '/intranet' => 'Início',
            'educar_vps_index.php'                => 'Trilha Jovem Iguassu - VPS',
            ''                                    => 'Detalhe da entrevista'
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
