<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo(
            $this->_instituicao . ' Trilha Jovem - Carometro por Turma'
        );
        $this->processoAp = 659;
    }
}

class indice extends clsCadastro
{
    var $pessoa_logada;

    var $ref_cod_matricula;

    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $sequencial;

    var $ref_cod_instituicao;
    var $ref_ref_cod_escola;
    var $ref_cod_curso;
    var $ref_ref_cod_serie;
    var $ref_cod_turma;

    var $matriculas_turma;
    var $incluir_matricula;

    function Inicializar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->ref_cod_turma = $_GET['ref_cod_turma'];

        $obj_permissoes = new clsPermissoes();
        //$obj_permissoes->permissao_cadastra(659, $this->pessoa_logada, 7, 'educar_carometro_turma_lst.php');

        if (is_numeric($this->ref_cod_turma)) {
            $obj_turma = new clsPmieducarTurma();
            $lst_turma = $obj_turma->lista($this->ref_cod_turma);

            if (is_array($lst_turma)) {
                $registro = array_shift($lst_turma);
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }

            $this->url_cancelar = $retorno == 'Editar' ?
                sprintf(
                    'educar_carometro_turma_det.php?ref_cod_matricula=%d&ref_cod_turma=%d',
                    $this->ref_cod_matricula, $this->ref_cod_turma
                ) :
                'educar_carometro_turma_lst.php';

            $this->nome_url_cancelar = 'Cancelar';
            return $retorno;
        }

        header('Location: educar_carometro_turma_lst.php');
        die;
    }

    function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = $this->$campo ? $this->$campo : $val;
            }
        }

        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);
        $this->campoOculto('ref_ref_cod_escola', $this->ref_ref_cod_escola);
        $this->campoOculto('ref_ref_cod_serie', $this->ref_ref_cod_serie);
        $this->campoOculto('ref_cod_curso', $this->ref_cod_curso);

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            $obj_cod_instituicao = new clsPmieducarInstituicao(
                $this->ref_cod_instituicao
            );
            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $nm_instituicao = $obj_cod_instituicao_det['nm_instituicao'];
            $this->campoRotulo(
                'nm_instituicao',
                'Instituição Executora',
                $nm_instituicao
            );
        }

        if ($nivel_usuario == 1 || $nivel_usuario == 2) {
            if ($this->ref_ref_cod_escola) {
                $obj_ref_cod_escola = new clsPmieducarEscola(
                    $this->ref_ref_cod_escola
                );
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola['nome'];
                $this->campoRotulo('nm_escola', 'Instituição', $nm_escola);
            }
        }

        if ($this->ref_cod_curso) {
            $obj_ref_cod_curso = new clsPmieducarCurso($this->ref_cod_curso);
            $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
            $nm_curso = $det_ref_cod_curso['nm_curso'];
            $this->campoRotulo('nm_curso', 'Projeto', $nm_curso);
        }

        if ($this->ref_ref_cod_serie) {
            $obj_ref_cod_serie = new clsPmieducarSerie($this->ref_ref_cod_serie);
            $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
            $nm_serie = $det_ref_cod_serie["nm_serie"];
            $this->campoRotulo('nm_serie', 'Eixo', $nm_serie);

            // busca o ano em q a escola esta em andamento
            $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
            $lst_ano_letivo = $obj_ano_letivo->lista(
                $this->ref_ref_cod_escola, null, null,
                null, 1, null, null, null, null, 1
            );

            if (is_array($lst_ano_letivo)) {
                $det_ano_letivo = array_shift($lst_ano_letivo);
                $ano_letivo = $det_ano_letivo['ano'];
            } else {
                $this->mensagem = 'Não foi possível encontrar o ano letivo em andamento na instituição.';
                return false;
            }
        }

        if ($this->ref_cod_turma) {
            $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
            $det_turma = $obj_turma->detalhe();
            $nm_turma = $det_turma['nm_turma'];
            $this->campoRotulo('nm_turma', 'Turma', $nm_turma);
        }

        // Inlui o aluno
        $this->campoQuebra();

        $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
        $obj_matriculas_turma->setOrderby('nome_aluno');
        $this->matriculas_turma = $obj_matriculas_turma->lista(
            null, $this->ref_cod_turma, null, null, null, null,
            null, null, 1, null, null, null, null, null, null,
            array(1, 2, 3), null, null, $ano_letivo, null, true,
            null, 1, true
        );

        $count = 0;
        $campo_teste = array();
        $bloco_foto = "<table style='border-spacing:7px;'><tr>";

        foreach ($this->matriculas_turma as $matricula) {
            $obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_aluno']);
            $det_matricula = $obj_matricula->detalhe();

            $tmp_obj = new clsPmieducarAluno($det_matricula['ref_cod_aluno']);
            $cod_pessoa = $tmp_obj->detalhe();
    
            $obj_aluno = new clsPmieducarAluno();
            $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
            $det_aluno = array_shift($lst_aluno);
            $nm_aluno = $det_aluno['nome_aluno'];

            $objFoto = new clsCadastroFisicaFoto($cod_pessoa['ref_idpes']);
            $caminhoFoto = $objFoto->detalhe();
    
            if ($count >= 5) {
                $count = 0;
                $bloco_foto .= "</tr><tr>";
            }

            if ($caminhoFoto ==false) {
                $caminhoFoto['caminho'] = "/upload/foto_perfil/default.png";
            }
                
            $bloco_foto .= '
                <td>
                    <table style="width:120px; background:#faf2ed; border-style:solid; border-color:#e48238; border-width:1px;">
                        <tr>
                            <td><a href=educar_aluno_det.php?cod_aluno='.$det_aluno["cod_aluno"].'><img height="120"src="'.$caminhoFoto['caminho'].'"/></a></td>
                        </tr>
                        <tr>
                            <th align="center" style="height:50px;"><a href=educar_aluno_det.php?cod_aluno='.$det_aluno["cod_aluno"].'>'.$nm_aluno.'</a></th>
                        </tr>
                    </table>
                </td>';
            $count++;
        }

        $bloco_foto.= '</tr></table>';
        $campo_teste[] = $this->campoRotulo('ref_cod_matriculados', '', $bloco_foto);

        if (is_array($alunos)) {
            for ($i = 0; $i < count($alunos); $i++) {
                $obj_matricula = new clsPmieducarMatricula($alunos[$i]);
                $det_matricula = $obj_matricula->detalhe();

                $obj_aluno = new clsPmieducarAluno();
                $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
                $det_aluno = array_shift($lst_aluno);

                $opcoes[$alunos[$i]] = $det_aluno['nome_aluno'];
            }
        }

        $this->campoQuebra();
    }

    function Novo()
    {
    }

    function Editar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        if ($this->matriculas_turma) {
            foreach ($this->ref_cod_matricula as $matricula => $campo) {
                $obj = new clsPmieducarMatriculaTurma(
                    $matricula, $this->ref_cod_turma, null,
                    $this->pessoa_logada, null, null, 1, null, $campo['sequencial_']
                );

                $existe = $obj->existe();

                if (!$existe) {
                    $cadastrou = $obj->cadastra();

                    if (!$cadastrou) {
                        $this->mensagem = 'Cadastro não realizado.<br>';
                        return false;
                    }
                }
            }

            $this->mensagem .= 'Cadastro efetuada com sucesso.<br>';
            header('Location: educar_carometro_turma_lst.php');
            die();
        }

        header('Location: educar_carometro_turma_lst.php');
        die();
    }

    function Excluir()
    {
    }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
