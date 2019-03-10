<?php

require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'image_check.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'include/modules/clsModulesPessoaTransporte.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'Transporte/Model/Responsavel.php';

class InscritoController extends ApiCoreController
{
    protected $_processoAp = 578;
    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    // validators
    protected function validatesPessoaId()
    {
        $existenceOptions = ['schema_name' => 'cadastro', 'field_name' => 'idpes'];

        return (
            $this->validatesPresenceOf('pessoa_id') &&
            $this->validatesExistenceOf(
                'fisica',
                $this->getRequest()->pessoa_id,
                $existenceOptions
            )
        );
    }

    protected function validatesAlunoId()
    {
        $existenceOptions = [
            'schema_name' => 'pmieducar',
            'field_name' => 'cod_aluno'
        ];

        return (
            $this->validatesPresenceOf('aluno_id') &&
            $this->validatesExistenceOf(
                'aluno',
                $this->getRequest()->ref_cod_aluno,
                $existenceOptions
            )
        );
    }

    protected function validatesResponsavelId()
    {
        $isValid = true;

        if ($this->getRequest()->tipo_responsavel == 'outra_pessoa') {
            $existenceOptions = ['schema_name' => 'cadastro', 'field_name' => 'idpes'];

            $isValid = (
                $this->validatesPresenceOf('responsavel_id') &&
                $this->validatesExistenceOf(
                    'fisica',
                    $this->getRequest()->responsavel_id,
                    $existenceOptions
                )
            );
        }

        return $isValid;
    }

    protected function validatesResponsavelTipo()
    {
        $expectedValues = ['mae', 'pai', 'outra_pessoa', 'pai_mae'];

        return (
            $this->validatesPresenceOf('tipo_responsavel') &&
            $this->validator->validatesValueInSetOf(
                $this->getRequest()->tipo_responsavel,
                $expectedValues,
                'tipo_responsavel'
            )
        );
    }

    protected function validatesResponsavel()
    {
        return (
            $this->validatesResponsavelTipo() &&
            $this->validatesResponsavelId()
        );
    }

    protected function validatesUniquenessOfInscritoByAlunoId()
    {
        $alunoId = $this->getAlunoId();
        //$selecaoId = $this->getRequest()->selecao_id;
        $selecaoId = 1;

        if ($alunoId) {
            $sql = "SELECT 1 FROM
                        pmieducar.inscrito
                    WHERE
                        ref_cod_aluno = {$alunoId}
                    AND
                        ref_cod_selecao_processo = {$selecaoId}
                    LIMIT 1";

            $checkUnique = $this->fetchPreparedQuery($sql);

            if ($checkUnique) {
                $this->messenger->append(
                    "Já existe um inscrito cadastrado para o aluno {$alunoId}."
                );

                return false;
            }
        }

        return true;
    }

    protected function canGetTodosInscritos()
    {
        return $this->validatesPresenceOf('ref_cod_selecao_processo');
    }

    protected function canGetInscritosByGuardianCpf()
    {
        return $this->validatesPresenceOf('inscrito_id') && $this->validatesPresenceOf('cpf');
    }

    protected function canChange()
    {
        return (
            $this->validatesPessoaId() &&
            $this->validatesResponsavel()
        );
    }

    protected function canPost()
    {
        return (
            parent::canPost() &&
            $this->validatesPessoaId() &&
            $this->validatesUniquenessOfInscritoByAlunoId()
        );
    }

    protected function getAlunoId()
    {
        $alunoId = $this->getRequest()->aluno_id;

        if (!$alunoId) {
            $pessoaId = $this->getRequest()->pessoa_id;

            $db = new clsBanco();
            $alunoId = $db->CampoUnico(
                "SELECT
                    cod_aluno
                FROM
                    pmieducar.aluno
                WHERE
                    ref_idpes = {$pessoaId} LIMIT 1"
            );
        }

        return $alunoId;
    }

    // load resources
    protected function loadNomeInscrito($inscritoId)
    {
        $sql = 'SELECT
                    nome
                FROM
                    cadastro.pessoa, pmieducar.aluno, pmieducar.inscrito
                WHERE
                    idpes = ref_idpes AND cod_aluno = ref_cod_aluno
                AND
                    cod_inscrito = $1';

        $nome = $this->fetchPreparedQuery($sql, $inscritoId, false, 'first-field');

        return $this->toUtf8($nome, ['transform' => true]);
    }

    // #TODO mover updateResponsavel e updateDeficiencias para API pessoa ?
    protected function updateResponsavel()
    {
        $pessoa = new clsFisica();
        $pessoa->idpes = $this->getRequest()->pessoa_id;
        $pessoa->nome_responsavel = '';

        $_pessoa = $pessoa->detalhe();

        if ($this->getRequest()->tipo_responsavel == 'outra_pessoa') {
            $pessoa->idpes_responsavel = $this->getRequest()->responsavel_id;
        } elseif ($this->getRequest()->tipo_responsavel == 'pai' && $_pessoa['idpes_pai']) {
            $pessoa->idpes_responsavel = $_pessoa['idpes_pai'];
        } elseif ($this->getRequest()->tipo_responsavel == 'mae' && $_pessoa['idpes_mae']) {
            $pessoa->idpes_responsavel = $_pessoa['idpes_mae'];
        } else {
            $pessoa->idpes_responsavel = 'NULL';
        }

        return $pessoa->edita();
    }

    protected function updateDeficiencias()
    {
        $sql = 'delete from cadastro.fisica_deficiencia where ref_idpes = $1';
        $this->fetchPreparedQuery($sql, $this->getRequest()->pessoa_id, false);

        foreach ($this->getRequest()->deficiencias as $id) {
            if (!empty($id)) {
                $deficiencia = new clsCadastroFisicaDeficiencia($this->getRequest()->pessoa_id, $id);
                $deficiencia->cadastra();
            }
        }
    }

    protected function createOrUpdateAluno($alunoId = null)
    {
        $tiposResponsavel = ['pai' => 'p', 'mae' => 'm', 'outra_pessoa' => 'r', 'pai_mae' => 'a'];

        $aluno = new clsPmieducarAluno();
        $aluno->cod_aluno = $alunoId;

        $aluno->autorizado_um = Portabilis_String_Utils::toLatin1($this->getRequest()->autorizado_um);
        $aluno->parentesco_um = Portabilis_String_Utils::toLatin1($this->getRequest()->parentesco_um);
        $aluno->autorizado_dois = Portabilis_String_Utils::toLatin1($this->getRequest()->autorizado_dois);
        $aluno->parentesco_dois = Portabilis_String_Utils::toLatin1($this->getRequest()->parentesco_dois);

        // após cadastro não muda mais id pessoa
        if (!is_numeric($alunoId)) {
            $aluno->ref_idpes = $this->getRequest()->pessoa_id;
            $aluno->ref_usuario_cad = $this->getSession()->id_pessoa;
        } else {
            $aluno->ref_usuario_exc = $this->getSession()->id_pessoa;
        }

        $aluno->tipo_responsavel = $tiposResponsavel[$this->getRequest()->tipo_responsavel];

        // INFORAMÇÕES PROVA INEP
        $this->file_foto = $_FILES['file'];
        $this->del_foto = $_POST['file_delete'];

        if (!$this->validatePhoto()) {
            $this->mensagem = 'Foto inválida';

            return false;
        }

        $pessoaId = $this->getRequest()->pessoa_id;
        $this->savePhoto($pessoaId);

        if (!is_numeric($alunoId)) {
            $alunoId = $aluno->cadastra();
            $aluno->cod_aluno = $alunoId;
            $auditoria = new clsModulesAuditoriaGeral('aluno', $this->getSession()->id_pessoa, $id);
            $auditoria->inclusao($aluno->detalhe());
        } else {
            $detalheAntigo = $aluno->detalhe();
            $alunoId = $aluno->edita();
            $auditoria = new clsModulesAuditoriaGeral('aluno', $this->getSession()->id_pessoa, $alunoId);
            $auditoria->alteracao($detalheAntigo, $aluno->detalhe());
        }

        return $alunoId;
    }

    protected function createOrUpdateFichaMedica($alunoId)
    {
        $obj = new clsModulesFichaMedicaAluno();

        $obj->ref_cod_aluno = $alunoId;
        $obj->grupo_sanguineo = Portabilis_String_Utils::toLatin1($this->getRequest()->grupo_sanguineo);
        $obj->grupo_sanguineo = trim($obj->grupo_sanguineo);
        $obj->fator_rh = Portabilis_String_Utils::toLatin1($this->getRequest()->fator_rh);

        return ($obj->existe() ? $obj->edita() : $obj->cadastra());
    }

    protected function createOrUpdateInscrito($id = null)
    {
        $inscrito = new clsPmieducarInscrito();
        $inscrito->cod_inscrito = $id;
        $inscrito->ref_cod_selecao_processo = $this->getRequest()->processo_seletivo_id;

        // após cadastro não muda mais id pessoa
        if (is_null($id)) {
            $inscrito->ref_cod_aluno = $this->getAlunoId();
            $inscrito->ref_usuario_cad = $this->getSession()->id_pessoa;
        } else {
            $inscrito->ref_usuario_exc = $this->getSession()->id_pessoa;
        }

        $inscrito->estudando_serie = $this->getRequest()->serie;
        $inscrito->estudando_turno = $this->getRequest()->turno;
        $inscrito->egresso = $this->getRequest()->egresso;
        $inscrito->guarda_mirim = $this->getRequest()->guarda_mirim;
        $inscrito->encaminhamento = $this->getRequest()->encaminhamento;
        $inscrito->area_interesse = $this->getRequest()->area_interesse;
        $inscrito->copia_rg = $this->getRequest()->copia_rg;
        $inscrito->copia_cpf = $this->getRequest()->copia_cpf;
        $inscrito->copia_residencia = $this->getRequest()->copia_residencia;
        $inscrito->copia_historico = $this->getRequest()->copia_historico;
        $inscrito->copia_renda = $this->getRequest()->copia_renda;

        if (is_null($id)) {
            $id = $inscrito->cadastra();
            $inscrito->cod_inscrito = $id;
            $auditoria = new clsModulesAuditoriaGeral('inscrito', $this->getSession()->id_pessoa, $id);
            $auditoria->inclusao($inscrito->detalhe());
        } else {
            $detalheAntigo = $inscrito->detalhe();
            $id = $inscrito->edita();
            $auditoria = new clsModulesAuditoriaGeral('inscrito', $this->getSession()->id_pessoa, $id);
            $auditoria->alteracao($detalheAntigo, $inscrito->detalhe());
        }

        $sql = "SELECT
                    total_etapas
                FROM
                    pmieducar.selecao_processo
                WHERE
                    cod_selecao_processo = $1";

        $total_etapas = Portabilis_Utils_Database::selectField(
            $sql,
            $inscrito->ref_cod_selecao_processo
        );

        for ($i = 1; $i <= $total_etapas; $i++) {
            $situacao = $this->getRequest()->{'etapa_' . $i};

            if ($situacao) {
                $this->createOrUpdateEtapa($inscrito, $i, $situacao);
            }
        }

        return $id;
    }

    protected function createOrUpdateEtapa($id, $etapa, $situacao)
    {
        $id = null;

        if (is_numeric($id) && is_numeric($etapa)
            && is_numeric($situacao)
        ) {
            $sql = "INSERT INTO
                    pmieducar.inscrito_etapa
                VALUES
                    ({$id}, {$etapa}, {$situacao})
                ON CONFLICT
                    (ref_cod_inscrito, etapa)
                DO UPDATE SET
                    situacao = {$situacao}";

            $id = $this->fetchPreparedQuery($sql);
        }

        return $id;
    }

    // search options
    protected function searchOptions()
    {
        $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;

        return [
            'sqlParams' => [$escolaId],
            'selectFields' => ['matricula_id']
        ];
    }

    protected function sqlsForNumericSearch()
    {
        $sqls = [];

        // caso nao receba id da escola, pesquisa por codigo aluno em todas as escolas,
        // alunos com e sem matricula são selecionados.
        if (!$this->getRequest()->escola_id) {
            $sqls[] = '
                select
                    distinct aluno.cod_aluno as id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = 1
                    and aluno.cod_aluno::varchar(255) like $1||\'%\'
                    and $2 = $2
                order by
                    cod_aluno
                limit 15
            ';
        }

        $sqls[] = '
            select
                *
            from (
                select
                    distinct ON (aluno.cod_aluno) aluno.cod_aluno as id,
                    matricula.cod_matricula as matricula_id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.matricula,
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and aluno.cod_aluno = matricula.ref_cod_aluno
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = matricula.ativo
                    and matricula.ativo = 1
                    and (
                        select
                            case
                                when $2 != 0 then matricula.ref_ref_cod_escola = $2
                                else 1=1
                            end
                    )
                    and (matricula.ref_cod_aluno::varchar(255) like $1||\'%\')
                    and matricula.aprovado in (1, 2, 3, 4, 7, 8, 9)
                limit 15
            ) as alunos
            order by
                id
        ';

        return $sqls;
    }

    protected function sqlsForStringSearch()
    {
        $sqls = [];

        // caso nao receba id da escola, pesquisa por nome aluno em todas as escolas,
        // alunos com e sem matricula são selecionados.
        if (!$this->getRequest()->escola_id) {
            $sqls[] = '
                select
                    distinct aluno.cod_aluno as id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = 1
                    and lower(coalesce(fisica.nome_social, \'\') || pessoa.nome) like \'%\'||lower(($1))||\'%\'
                    and $2 = $2
                order by
                    name
                limit 15
            ';
        }

        // seleciona por nome aluno e e opcionalmente por codigo escola,
        // apenas alunos com matricula são selecionados.
        $sqls[] = '
            select
                *
            from (
                select
                    distinct ON (aluno.cod_aluno) aluno.cod_aluno as id,
                    matricula.cod_matricula as matricula_id,
                    (case
                        when fisica.nome_social not like \'\' then
                            fisica.nome_social || \' - Nome de registro: \' || pessoa.nome
                        else
                            pessoa.nome
                    end) as name
                from
                    pmieducar.matricula,
                    pmieducar.aluno,
                    cadastro.pessoa,
                    cadastro.fisica
                where true
                    and aluno.cod_aluno = matricula.ref_cod_aluno
                    and pessoa.idpes = aluno.ref_idpes
                    and fisica.idpes = aluno.ref_idpes
                    and aluno.ativo = matricula.ativo
                    and matricula.ativo = 1 and (
                        select
                            case
                                when $2 != 0 then matricula.ref_ref_cod_escola = $2
                                else 1=1
                            end
                    )
                    and translate(upper(coalesce(fisica.nome_social, \'\') || pessoa.nome),\'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ\',\'AAAAAAEEEEIIIIOOOOOUUUUCYN\') like translate(upper(\'%\'|| $1 ||\'%\'),\'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ\',\'AAAAAAEEEEIIIIOOOOOUUUUCYN\')
                    and matricula.aprovado in (1, 2, 3, 4, 7, 8, 9)
                limit 15
            ) as alunos
            order by
                name
        ';

        return $sqls;
    }

    // api
    protected function tipoResponsavel($inscrito)
    {
        $tipos = ['p' => 'pai', 'm' => 'mae', 'r' => 'outra_pessoa', 'a' => 'pai_mae'];
        $tipo = $tipos[$inscrito['tipo_responsavel']];

        // no antigo cadastro de inscrito, caso não fosse encontrado um tipo de responsavel
        // verificava se a pessoa possua responsavel, pai ou mãe, considerando como
        // responsavel um destes, na respectiva ordem, sendo assim esta api mantem
        // compatibilidade com o antigo cadastro.
        if (!$tipo) {
            $pessoa = new clsFisica();
            $pessoa->idpes = $inscrito['pessoa_id'];
            $pessoa = $pessoa->detalhe();

            if ($pessoa['idpes_responsavel'] || $pessoa['nome_responsavel']) {
                $tipo = $tipos['r'];
            } elseif ($pessoa['idpes_pai'] || $pessoa['nome_pai']) {
                $tipo = $tipos['p'];
            } elseif ($pessoa['idpes_mae'] || $pessoa['nome_mae']) {
                $tipo = $tipos['m'];
            }
        }

        return $tipo;
    }

    protected function loadBeneficios($alunoId)
    {
        $sql = "SELECT
                    aluno_beneficio_id as id, nm_beneficio as nome
                FROM
                    pmieducar.aluno_aluno_beneficio,
                    pmieducar.aluno_beneficio
                WHERE
                    aluno_beneficio_id = cod_aluno_beneficio
                AND
                    aluno_id = $1";

        $beneficios = $this->fetchPreparedQuery($sql, $alunoId, false);

        // transforma array de arrays em array chave valor
        $_beneficios = [];

        foreach ($beneficios as $beneficio) {
            $nome = $this->toUtf8($beneficio['nome'], ['transform' => true]);
            $_beneficios[$beneficio['id']] = $nome;
        }

        return $_beneficios;
    }

    protected function get()
    {
        if ($this->canGet()) {
            $id = $this->getRequest()->id;

            $inscrito = new clsPmieducarInscrito();
            $inscrito->cod_inscrito = $id;
            $inscrito = $inscrito->detalhe();

            $attrs = [
                'cod_inscrito' => 'id',
                'ref_cod_aluno' => 'aluno_id',
                'ref_usuario_exc' => 'destroyed_by',
                'data_exclusao' => 'destroyed_at',
                'ref_cod_selecao_processo',
                'estudando_serie',
                'egresso',
                'estudando_turno',
                'guarda_mirim',
                'area_interesse',
                'copia_rg',
                'copia_cpf',
                'copia_residencia',
                'copia_historico',
                'copia_renda',
                'encaminhamento',
                'ativo',
            ];

            $inscrito = Portabilis_Array_Utils::filter($inscrito, $attrs);

            $objEtapa = new clsPmieducarInscritoEtapa();
            $registroEtapa = $objEtapa->lista($id);

            foreach ($registroEtapa as $registro) {
                if (is_numeric($registro['situacao'])) {
                    $inscrito['etapas'][$registro['etapa']]
                        = $registro['situacao'];
                }
            }

            $alunoId = $inscrito['aluno_id'];
            $aluno = new clsPmieducarAluno();
            $aluno->cod_aluno = $alunoId;
            $aluno = $aluno->detalhe();

            $inscrito['nome'] = $this->loadNomeInscrito($id);
            $inscrito['tipo_responsavel'] = $this->tipoResponsavel($aluno);
            $inscrito['ativo'] = $inscrito['ativo'] == 1;
            $inscrito['autorizado_um'] = Portabilis_String_Utils::toUtf8($aluno['autorizado_um']);
            $inscrito['parentesco_um'] = Portabilis_String_Utils::toUtf8($aluno['parentesco_um']);
            $inscrito['autorizado_dois'] = Portabilis_String_Utils::toUtf8($aluno['autorizado_dois']);
            $inscrito['parentesco_dois'] = Portabilis_String_Utils::toUtf8($aluno['parentesco_dois']);

            // destroyed_by username
            $dataMapper = $this->getDataMapperFor('usuario', 'funcionario');
            $entity = $this->tryGetEntityOf($dataMapper, $inscrito['destroyed_by']);

            $inscrito['destroyed_by'] = is_null($entity) ? null : $entity->get('matricula');
            $inscrito['destroyed_at'] = Portabilis_Date_Utils::pgSQLToBr($inscrito['destroyed_at']);

            $objFichaMedica = new clsModulesFichaMedicaAluno($alunoId);

            if ($objFichaMedica->existe()) {
                $objFichaMedica = $objFichaMedica->detalhe();
                $inscrito['grupo_sanguineo']= Portabilis_String_Utils::toUtf8($objFichaMedica['grupo_sanguineo']);
                $inscrito['fator_rh'] = Portabilis_String_Utils::toUtf8($objFichaMedica['fator_rh']);
            }

            $sql = "SELECT sus, ref_cod_religiao FROM cadastro.fisica WHERE idpes = $1";
            $camposFisica = $this->fetchPreparedQuery($sql, $aluno['ref_idpes'], false, 'first-row');

            $inscrito['pessoa_id'] = $aluno['ref_idpes'];
            $inscrito['sus'] = $camposFisica['sus'];
            $inscrito['religiao_id'] = $camposFisica['ref_cod_religiao'];
            $inscrito['beneficios'] = $this->loadBeneficios($inscrito['ref_cod_aluno']);

            $objFoto = new clsCadastroFisicaFoto($aluno['ref_idpes']);
            $detalheFoto = $objFoto->detalhe();

            if ($detalheFoto) {
                $inscrito['url_foto_inscrito'] = $detalheFoto['caminho'];
            }

            return $inscrito;
        }
    }

    protected function getTodosInscritos()
    {
        if ($this->canGetTodosInscritos()) {
            $sql = 'SELECT a.cod_inscrito AS inscrito_id,
                p.nome as nome_inscrito,
                f.nome_social,
                f.data_nasc as data_nascimento,
                ff.caminho as foto_inscrito
                FROM pmieducar.inscrito a
                INNER JOIN pmieducar.aluno b ON b.cod_aluno = a.ref_cod_aluno
                INNER JOIN cadastro.pessoa p ON p.idpes = b.ref_idpes
                INNER JOIN cadastro.fisica f ON f.idpes = p.idpes
                LEFT JOIN cadastro.fisica_foto ff ON p.idpes = ff.idpes
                WHERE a.ativo = 1
                ORDER BY nome_inscrito ASC';

            $inscritos = $this->fetchPreparedQuery($sql);

            $attrs = ['inscrito_id', 'nome_inscrito', 'nome_social', 'foto_inscrito', 'data_nascimento'];
            $inscritos = Portabilis_Array_Utils::filterSet($inscritos, $attrs);

            return ['inscritos' => $inscritos];
        }
    }

    protected function getIdpesFromCpf($cpf)
    {
        $sql = 'SELECT idpes FROM cadastro.fisica WHERE cpf = $1';

        return $this->fetchPreparedQuery($sql, $cpf, true, 'first-field');
    }

    protected function checkInscritoIdpesGuardian($idpesGuardian, $inscritoId)
    {
        $sql = "SELECT 1
                FROM
                    pmieducar.inscrito as a
                INNER JOIN
                    pmieducar.aluno ON (a.ref_cod_aluno = aluno.cod_aluno)
                INNER JOIN
                    cadastro.fisica ON (aluno.ref_idpes = fisica.idpes)
                WHERE
                    cod_inscrito = $2
                AND
                    (idpes_pai = $1 OR idpes_mae = $1 OR idpes_responsavel = $1)
                LIMIT 1";

        return $this->fetchPreparedQuery($sql, [$idpesGuardian, $inscritoId], true, 'first-field') == 1;
    }

    protected function getInscritosByGuardianCpf()
    {
        if ($this->canGetInscritosByGuardianCpf()) {
            $cpf = $this->getRequest()->cpf;
            $inscritoId = $this->getRequest()->inscrito_id;

            $idpesGuardian = $this->getIdpesFromCpf($cpf);

            if (is_numeric($idpesGuardian) && $this->checkInscritoIdpesGuardian($idpesGuardian, $inscritoId)) {
                $sql = "SELECT
                            cod_inscrito as inscrito_id, pessoa.nome as nome_inscrito
                        FROM
                            pmieducar.inscrito
                        INNER JOIN
                            pmieducar.aluno
                        ON
                            (inscrito.ref_cod_aluno = aluno.cod_aluno)
                        INNER JOIN
                            cadastro.fisica
                        ON
                            (aluno.ref_idpes = fisica.idpes)
                        INNER JOIN
                            cadastro.pessoa
                        ON
                            (pessoa.idpes = fisica.idpes)
                        WHERE
                            idpes_pai = $1
                        OR
                            idpes_mae = $1
                        OR
                            idpes_responsavel = $1";

                $inscritos = $this->fetchPreparedQuery($sql, [$idpesGuardian]);
                $attrs = ['inscrito_id', 'nome_inscrito'];
                $inscritos = Portabilis_Array_Utils::filterSet($inscritos, $attrs);

                foreach ($inscritos as &$inscrito) {
                    $inscrito['nome_inscrito'] = Portabilis_String_Utils::toUtf8($inscrito['nome_inscrito']);
                }

                return ['inscritos' => $inscritos];
            } else {
                $this->messenger->append('Não foi encontrado nenhum vínculos entre esse inscrito e cpf.');
            }
        }
    }

    protected function saveParents()
    {
        $maeId = $this->getRequest()->mae_id;
        $paiId = $this->getRequest()->pai_id;
        $pessoaId = $this->getRequest()->pessoa_id;

        $sql = 'UPDATE cadastro.fisica set ';

        $virgulaOuNada = '';

        if ($maeId) {
            $sql .= " idpes_mae = {$maeId} ";
            $virgulaOuNada = ', ';
        } elseif ($maeId == '') {
            $sql .= ' idpes_mae = NULL ';
            $virgulaOuNada = ', ';
        }

        if ($paiId) {
            $sql .= "{$virgulaOuNada} idpes_pai = {$paiId} ";
            $virgulaOuNada = ', ';
        } elseif ($paiId == '') {
            $sql .= "{$virgulaOuNada} idpes_pai = NULL ";
            $virgulaOuNada = ', ';
        }

        $sql .= " WHERE idpes = {$pessoaId}";
        Portabilis_Utils_Database::fetchPreparedQuery($sql);
    }

    protected function retornaCodigo($palavra)
    {
        return substr($palavra, 0, strpos($palavra, ' -'));
    }

    protected function postEtapa()
    {
        $id = $this->getRequest()->id;

        $etapa = $this->getRequest()->etapa_id;
        $situacao = $this->getRequest()->situacao;

        $id = $this->createOrUpdateEtapa($id, $etapa, $situacao);

        if ($id) {
            $this->messenger->append('Cadastrado realizado com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente o inscrito não pode ser cadastrado, por favor, verifique.');
        }

        return ['id' => $id];
    }

    protected function putCopiaDocumento()
    {
        $id = $this->getRequest()->id;

        $documento = $this->getRequest()->documento;
        $situacao = $this->getRequest()->situacao;

        if (is_numeric($id) && is_string($documento)
            && is_numeric($situacao)
        ) {
            $sql = "UPDATE
                    pmieducar.inscrito
                SET
                    copia_{$documento} = {$situacao}
                WHERE
                    cod_inscrito = {$id}";

            $id = $this->fetchPreparedQuery($sql);

            $this->messenger->append('Cadastrado realizado com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente o inscrito não pode ser cadastrado, por favor, verifique.');
        }

        return ['id' => $id];
    }

    protected function post()
    {
        if ($this->canPost()) {
            $alunoId = $this->getAlunoId();

            $alunoId = $this->createOrUpdateAluno($alunoId);
            $pessoaId = $this->getRequest()->pessoa_id;

            $this->saveParents();

            if (is_numeric($alunoId)) {
                $this->createOrUpdateFichaMedica($alunoId);

                $id = $this->createOrUpdateInscrito();

                $this->updateResponsavel();
                $this->updateDeficiencias();
                $this->createOrUpdateDocumentos($pessoaId);
                $this->createOrUpdatePessoa($pessoaId);

                $this->messenger->append('Cadastrado realizado com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente o inscrito não pode ser cadastrado, por favor, verifique.');
            }
        }

        return ['id' => $id];
    }

    protected function put()
    {
        $id = $this->getRequest()->id;

        if ($this->canPut() && $this->createOrUpdateInscrito($id)) {
            $alunoId = $this->getRequest()->aluno_id;
            $pessoaId = $this->getRequest()->pessoa_id;

            $this->createOrUpdateAluno($alunoId);
            $this->createOrUpdateFichaMedica($alunoId);

            $this->saveParents();

            $this->updateResponsavel();
            $this->updateDeficiencias();
            $this->createOrUpdateDocumentos($pessoaId);
            $this->createOrUpdatePessoa($pessoaId);

            $this->messenger->append('Cadastro alterado com sucesso', 'success', false, 'error');
        } else {
            $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.', 'error', false, 'error');
        }

        return ['id' => $id];
    }

    protected function enable()
    {
        $id = $this->getRequest()->id;

        if ($this->canEnable()) {
            $inscrito = new clsPmieducarInscrito();
            $inscrito->cod_inscrito = $id;
            $inscrito->ref_usuario_exc = $this->getSession()->id_pessoa;
            $inscrito->ativo = 1;

            if ($inscrito->edita()) {
                $this->messenger->append('Cadastro ativado com sucesso', 'success', false, 'error');
            } else {
                $this->messenger->append('Aparentemente o cadastro não pode ser ativado, por favor, verifique.', 'error', false, 'error');
            }
        }

        return ['id' => $id];
    }

    protected function delete()
    {
        $id = $this->getRequest()->id;
        $matriculaAtiva = dbBool($this->possuiMatriculaAtiva($id));

        if (!$matriculaAtiva) {
            if ($this->canDelete()) {
                $inscrito = new clsPmieducarInscrito();
                $inscrito->cod_inscrito = $id;
                $inscrito->ref_usuario_exc = $this->getSession()->id_pessoa;

                $detalheInscrito = $inscrito->detalhe();

                if ($inscrito->excluir()) {
                    $auditoria = new clsModulesAuditoriaGeral('inscrito', $this->getSession()->id_pessoa, $id);
                    $auditoria->exclusao($detalheInscrito);
                    $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
                } else {
                    $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.', 'error', false, 'error');
                }
            }
        } else {
            $this->messenger->append('O cadastro não pode ser removido, pois existem matrículas vinculadas.', 'error', false, 'error');
        }

        return ['id' => $id];
    }

    //envia foto e salva caminha no banco
    protected function savePhoto($id)
    {
        if ($this->objPhoto != null) {
            //salva foto com data, para evitar problemas com o cache do navegador
            $caminhoFoto = $this->objPhoto->sendPicture($id) . '?' . date('Y-m-d-H:i:s');

            if ($caminhoFoto != '') {
                //new clsCadastroFisicaFoto($id)->exclui();
                $obj = new clsCadastroFisicaFoto($id, $caminhoFoto);
                $detalheFoto = $obj->detalhe();

                if (is_array($detalheFoto) && count($detalheFoto) > 0) {
                    $obj->edita();
                } else {
                    $obj->cadastra();
                }

                return true;
            } else {
                echo '<script>alert(\'Foto não salva.\')</script>';

                return false;
            }
        } elseif ($this->del_foto == 'on') {
            $obj = new clsCadastroFisicaFoto($id);
            $obj->excluir();
        }
    }

    // Retorna true caso a foto seja válida
    protected function validatePhoto()
    {
        $this->arquivoFoto = $this->file_foto;

        if (!empty($this->arquivoFoto['name'])) {
            $this->arquivoFoto['name'] = mb_strtolower($this->arquivoFoto['name'], 'UTF-8');
            $this->objPhoto = new PictureController($this->arquivoFoto);

            if ($this->objPhoto->validatePicture()) {
                return true;
            } else {
                $this->messenger->append($this->objPhoto->getErrorMessage());

                return false;
            }

            return false;
        } else {
            $this->objPhoto = null;

            return true;
        }
    }

    protected function createOrUpdateDocumentos($pessoaId)
    {
        $documentos = new clsDocumento();
        $documentos->idpes = $pessoaId;

        $documentos->rg = $this->getRequest()->rg;
        $documentos->data_exp_rg = Portabilis_Date_Utils::brToPgSQL(
            $this->getRequest()->data_emissao_rg
        );
        $documentos->sigla_uf_exp_rg = $this->getRequest()->uf_emissao_rg;
        $documentos->idorg_exp_rg = $this->getRequest()->orgao_emissao_rg;

        $documentos->passaporte = addslashes($this->getRequest()->passaporte);

        // Alteração de documentos compativel com a versão anterior do cadastro,
        // onde era possivel criar uma pessoa, não informando os documentos,
        // o que não criaria o registro do documento, sendo assim, ao editar uma pessoa,
        // o registro do documento será criado, caso não exista.

        $sql = 'select 1 from cadastro.documento WHERE idpes = $1 limit 1';

        if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
            $documentos->cadastra();
        } else {
            $documentos->edita_aluno();
        }
    }

    protected function createOrUpdatePessoa($idPessoa)
    {
        $fisica = new clsFisica($idPessoa);
        $fisica->cpf = idFederal2int($this->getRequest()->id_federal);
        $fisica->ref_cod_religiao = $this->getRequest()->religiao_id;
        $fisica = $fisica->edita();
    }

    protected function loadAcessoDataEntradaSaida()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        $acesso = new clsPermissoes();
        session_write_close();

        return $acesso->permissao_cadastra(626, $this->pessoa_logada, 7, null, true);
    }

    protected function isUsuarioAdmin()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        $isAdmin = ($this->pessoa_logada == 1);
        session_write_close();

        return $isAdmin;
    }

    protected function getNomeBairro()
    {
        $var1 = $this->getRequest()->id;

        $sql = "SELECT relatorio.get_texto_sem_caracter_especial(bairro.nome) as nome
                  FROM pmieducar.inscrito as a
            INNER JOIN cadastro.fisica ON (a.ref_idpes = fisica.idpes)
            INNER JOIN cadastro.endereco_pessoa ON (fisica.idpes = endereco_pessoa.idpes)
            INNER JOIN public.bairro ON (endereco_pessoa.idbai = bairro.idbai)
                 WHERE cod_inscrito = $var1";

        $bairro = $this->fetchPreparedQuery($sql);

        return $bairro;
    }

    protected function getEstatistica()
    {
        $sql = "SELECT
                    COUNT(CASE WHEN area_interesse ='1' then 1 end) as tea,
                    COUNT(CASE WHEN area_interesse ='2' then 1 end) as hospedagem,
                    COUNT(CASE WHEN area_interesse ='3' then 1 end) as eventos,
                    COUNT(CASE WHEN area_interesse ='4' then 1 end) as comercio
                FROM
                    pmieducar.inscrito
                WHERE
                    1";


    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'inscrito')) {
            $this->appendResponse($this->get());
        } elseif ($this->isRequestFor('get', 'inscrito-search')) {
            $this->appendResponse($this->search());
        } elseif ($this->isRequestFor('get', 'todos-inscritos')) {
            $this->appendResponse($this->getTodosInscritos());
        } elseif ($this->isRequestFor('get', 'ocorrencias_disciplinares')) {
            $this->appendResponse($this->getOcorrenciasDisciplinares());
        } elseif ($this->isRequestFor('get', 'inscritos_by_guardian_cpf')) {
            $this->appendResponse($this->getInscritosByGuardianCpf());
        } elseif ($this->isRequestFor('get', 'estatisticas')) {
            $this->appendResponse($this->getEstatisticas());
        } elseif ($this->isRequestFor('post', 'inscrito-etapa')) {
            $this->appendResponse($this->postEtapa());
        } elseif ($this->isRequestFor('put', 'inscrito-documento')) {
            $this->appendResponse($this->putCopiaDocumento());
        } elseif ($this->isRequestFor('post', 'inscrito')) {
            $this->appendResponse($this->post());
        } elseif ($this->isRequestFor('put', 'inscrito')) {
            $this->appendResponse($this->put());
        } elseif ($this->isRequestFor('enable', 'inscrito')) {
            $this->appendResponse($this->enable());
        } elseif ($this->isRequestFor('delete', 'inscrito')) {
            $this->appendResponse($this->delete());
        } elseif ($this->isRequestFor('get', 'get-nome-bairro')) {
            $this->appendResponse($this->getNomeBairro());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
