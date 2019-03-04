<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pmieducar/clsPmieducarInscrito.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'image_check.php';

class clsIndex extends clsBase
{
    function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Processo Seletivo - Cadastro');
        $this->processoAp = 43;
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    var $cod_selecao_processo;
    var $ref_cod_escola;
    var $ano_letivo;
    var $ref_cod_curso;
    var $numero_selecionados;
    var $total_etapas;
    var $status;
    var $ativo;

    var $caminho_det;
    var $caminho_lst;

    function Inicializar()
    {
        $this->cod_selecao_processo = @$_GET['cod_selecao_processo'];
        $this->retorno       = 'Novo';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $this->retorno == "Editar" ? $this->retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();

        $localizacao->entradaCaminhos(
            array(
                $_SERVER['SERVER_NAME'] . "/intranet" => "Início",
                ""                                    => "Processo Seletivo"
            )
        );

        $this->enviaLocalizacao($localizacao->montar());

        return $this->retorno;
    }

    function Gerar()
    {
        $this->url_cancelar = $this->retorno == 'Editar' ?
            'selecao_processo_det.php?cod_selecao_processo=' . $this->cod_selecao_processo : 'selecao_processo_lst.php';

        $this->campoArquivo(
            'documento', 'Arquivo', $this->documento, 40, 
            Portabilis_String_Utils::toLatin1(
                "<br/> <span id='span-documento' style='font-style: italic; font-size= 10px;''> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 250KB</span>",
                array('escape' => false)
            )
        );
    }

    function Novo()
    {
        @session_start();
            $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();


        print_r($this->documento);
        $csv = array_map("str_getcsv", file($this->documento['tmp_name'], FILE_SKIP_EMPTY_LINES));
        $keys = array_shift($csv);

        $db = new clsBanco();
        $cadastrados = 0;

        foreach ($csv as $i => $row) {
            $csv[$i] = array_combine($keys, $row);
            if (!empty($csv[$i])) {
                $cpf = idFederal2Int($csv[$i]['cpf']);

                if(is_numeric($cpf)) {
                    $sql = 'SELECT idpes FROM cadastro.fisica WHERE cpf = $1 LIMIT 1';
                    $pessoaId = Portabilis_Utils_Database::selectField($sql, $cpf);
                } else {
                    $rg = idFederal2Int($csv[$i]['rg']);
                    $sql = 'SELECT idpes FROM cadastro.documento WHERE rg = $1 LIMIT 1';
                    $pessoaId = Portabilis_Utils_Database::selectField($sql, $rg);
                }

                $pessoaId = $this->createOrUpdatePessoa($csv[$i], $pessoaId);

                if (is_numeric($pessoaId)) {
                    $this->createOrUpdatePessoaFisica($pessoaId, $csv[$i]);
                    $this->createOrUpdateDocumentos($pessoaId, $csv[$i]);
                    $this->createOrUpdatePessoaEndereco(
                        $pessoaId,
                        $csv[$i]['cpf'],
                        $csv[$i]['numero']
                    );

                    $sql = 'SELECT cod_aluno FROM pmieducar.aluno WHERE ref_idpes = $1 LIMIT 1';

                    $alunoId = Portabilis_Utils_Database::selectField($sql, $pessoaId);

                    $alunoId = $this->createOrUpdateAluno($pessoaId, $alunoId);

                    echo "Cadastrou pessoa {$pessoaId}";

                    if (is_numeric($alunoId)) {
                        $sql = 'SELECT cod_inscrito FROM pmieducar.inscrito WHERE ref_cod_aluno = $1 LIMIT 1';

                        $inscritoId = Portabilis_Utils_Database::selectField($sql, $alunoId);
    
                        $inscritoId = $this->createOrUpdateInscrito($csv[$i], $alunoId, $inscritoId);

                        echo "Cadastrou inscrito {$inscritoId}";
                        $cadastrados++;
                    }
                }



                /*if ($csv[$i]['cep']) {
                    echo "checando CEP {$csv[$i]['cep']} - {$csv[$i]['bairro']} - {$csv[$i]['endereco']}";
                    $objCepLogBairro = new clsCepLogradouroBairro();
                    $listaCepLogBairro = $objCepLogBairro->lista(false, $csv[$i]['cep']);
                    //print("<pre>".print_r($listaCepLogBairro,true)."</pre>");
                    if(!$listaCepLogBairro[0][0]) {
                        echo "não possui idlog {$csv[$i]['cep']}";
                    }
                    //$teste = $db->CampoUnico("SELECT cep FROM urbano.cep_logradouro_bairro  WHERE cep = {$csv[$i]['cep']}");
                    if (is_null($listaCepLogBairro)) {
                        echo "{$csv[$i]['cep']} não cadastrado";
                    }
                } else {
                    echo "CEP inexistente para o usuário {$csv[$i]['nome']}";
                }
                if ($csv[$i]['bairro']) {
                    $teste = $db->CampoUnico("SELECT idbai FROM public.bairro  WHERE nome = '{$csv[$i]['bairro']}'");
                    if (is_null($teste)) {
                        echo "{$csv[$i]['bairro']} não cadastrado";
                    }
                } else {
                    echo "CEP inexistente para o usuário {$csv[$i]['nome']}";
                }*/
            }
        }

        //print("<pre>".print_r($csv,true)."</pre>");

        if ($cadastrados > 0) {
            $this->mensagem .= "{$cadastrados} pré inscritos Cadastrados";
            //header('Location: selecao_inscritos_lst.php');
            //die();
            return false;
        }

        $this->mensagem = Portabilis_String_utils::toLatin1('Cadastro não realizado.');

        return false;
    }

    protected function createOrUpdatePessoa($dados, $pessoaId = null)
    {
        if (!empty($dados)) {

            echo "entrou aqui";
            $pessoa = new clsPessoa_();
            $pessoa->idpes = $pessoaId;
            $pessoa->nome = $dados['nome'];
            $pessoa->email = addslashes($dados['email']);

            $sql = "SELECT 1 FROM cadastro.pessoa WHERE idpes = $1 LIMIT 1";

            if (!$pessoaId 
                || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1
            ) {
                $pessoa->tipo = 'F';
                $pessoa->idpes_cad = $this->currentUserId();
                $pessoaId = $pessoa->cadastra();
            } else {
                $pessoa->idpes_rev = $this->currentUserId();
                $pessoa->data_rev = date('Y-m-d H:i:s', time());
                $pessoa->edita();
            }
        }

        return $pessoaId;
    }

    protected function createOrUpdatePessoaFisica($pessoaId, $dados)
    {
        $fisica = new clsFisica();
        $fisica->idpes = $pessoaId;
        $fisica->data_nasc = $dados['dataNasc'];
        $fisica->sexo = $dados['sexo'];
        $fisica->ref_cod_sistema = 'NULL';
        $fisica->cpf = $dados['cpf'] ? idFederal2int($dados['cpf']) : 'NULL';

        $sql = "SELECT 1 FROM cadastro.fisica WHERE idpes = $1 LIMIT 1";

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $fisica->cadastra();
        } else {
            $fisica->edita();
        }
    }

    protected function createOrUpdateDocumentos($pessoaId, $dados)
    {
        $documentos = new clsDocumento();
        $documentos->idpes = $pessoaId;

        $documentos->rg = $dados['rg'];

        $sql = 'SELECT 1 FROM cadastro.documento WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $documentos->cadastra();
        } else {
            $documentos->edita();
        }
    }

    protected function createOrUpdatePessoaEndereco($pessoaId, $cep, $numero)
    {
        $cep = idFederal2Int($cep);

        $objCepLogBairro = new clsCepLogradouroBairro();
        $listaCepLogBairro = $objCepLogBairro->lista(false, $cep);

        if (!empty($listaCepLogBairro) 
            && is_numeric($listaCepLogBairro[0][0])
            && is_numeric($listaCepLogBairro[0][2])
        ) {
            $endereco = new clsPessoaEndereco(
                $pessoaId,
                $cep,
                $listaCepLogBairro[0][0],
                $listaCepLogBairro[0][2],
                $numero
            );

            // forçado exclusão, assim ao cadastrar endereco_pessoa novamente,
            // será excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
            $endereco->exclui();
            $endereco->cadastra();
        }
    }

    protected function createOrUpdateAluno($pessoaId, $alunoId = null)
    {
        $aluno = new clsPmieducarAluno();
        $aluno->cod_aluno = $alunoId;

        // após cadastro não muda mais id pessoa
        if (!is_numeric($alunoId)) {
            $aluno->ref_idpes = $pessoaId;
            $aluno->ref_usuario_cad = $this->currentUserId();
        } else {
            $aluno->ref_usuario_exc = $this->currentUserId();
        }

        if (!is_numeric($alunoId)) {
            $alunoId = $aluno->cadastra();
            $aluno->cod_aluno = $alunoId;
            $auditoria = new clsModulesAuditoriaGeral('aluno', $this->currentUserId(), $id);
            $auditoria->inclusao($aluno->detalhe());
        } else {
            $detalheAntigo = $aluno->detalhe();
            $alunoId = $aluno->edita();
            $auditoria = new clsModulesAuditoriaGeral('aluno', $this->currentUserId(), $alunoId);
            $auditoria->alteracao($detalheAntigo, $aluno->detalhe());
        }

        return $alunoId;
    }

    protected function createOrUpdateInscrito($dados, $alunoId, $inscritoId = null)
    {
        $inscrito = new clsPmieducarInscrito();
        $inscrito->cod_inscrito = $inscritoId;
        $inscrito->ref_cod_selecao_processo = @$_GET['cod_selecao_processo'];

        if ($dados['estudando'] === 'A1') {
            $inscrito->estudando_serie = $this->getSerieIdFromCSV($dados['serie']);
            $inscrito->estudando_turno = $this->getTurnoIdFromCSV($dados['turno']);
            //$inscrito->escola = $this->getEscolaIdFromCSV($dados['escola']);
        } else {
            $inscrito->egresso = $dados['anoConclusao'];
        }

        //$inscrito->estudando_serie = $this->getAreaInteresseIdFromCSV($dados['interesse']);

        // após cadastro não muda mais id pessoa
        if (!is_numeric($inscritoId)) {
            $inscrito->ref_cod_aluno = $alunoId;
            $inscrito->ref_usuario_cad = $this->currentUserId();
        } else {
            $inscrito->ref_usuario_exc = $this->currentUserId();
        }

        if (!is_numeric($inscritoId)) {
            $inscritoId = $inscrito->cadastra();
            $inscrito->cod_aluno = $inscritoId;
            $auditoria = new clsModulesAuditoriaGeral('inscrito', $this->currentUserId(), $inscritoId);
            $auditoria->inclusao($inscrito->detalhe());
        } else {
            $detalheAntigo = $inscrito->detalhe();
            $inscritoId = $inscrito->edita();
            $auditoria = new clsModulesAuditoriaGeral('inscrito', $this->currentUserId(), $alunoId);
            $auditoria->alteracao($detalheAntigo, $inscrito->detalhe());
        }

        return $alunoId;
    }

    protected function createOrUpdateTelefones($pessoaId)
    {
        $telefones = [];

        $telefones[] = new clsPessoaTelefone($pessoaId, 1, $this->telefone_1, $this->ddd_telefone_1);
        $telefones[] = new clsPessoaTelefone($pessoaId, 2, $this->telefone_2, $this->ddd_telefone_2);
        $telefones[] = new clsPessoaTelefone($pessoaId, 3, $this->telefone_mov, $this->ddd_telefone_mov);
        $telefones[] = new clsPessoaTelefone($pessoaId, 4, $this->telefone_fax, $this->ddd_telefone_fax);

        foreach ($telefones as $telefone) {
            $telefone->cadastra();
        }
    }

    protected function getSerieIdFromCSV($valor)
    {
        $serie = array(
            'A5' => 5, // 9º série
            'A6' => 6,
            'A7' => 7,
            'A8' => 8,
            'A9' => 9 // EJA
        );

        return $serie[$valor];
    }

    protected function getTurnoIdFromCSV($valor)
    {
        $turno = array(
            'A1' => 1, // manhã
            'A2' => 2, // tarde
            'A3' => 3, // noite
        );

        return $turno[$valor];
    }

    protected function getAreaInteresseIdFromCSV($valor)
    {
        $areaInteresse = array(
            'A1' => 1, // Eventos
            'A2' => 2, // Turismo e Atendimento
            'A3' => 3, // Comércio e Atendimento
            'A4' => 4, // Hospedagem
        );

        return $areaInteresse[$valor];
    }
}

$pagina = new clsIndex();

$miolo = new indice();

$pagina->addForm($miolo);

$pagina->MakeAll();

?>