<?php

use iEducar\Modules\Inscritos\Model\SerieEstudo;
use iEducar\Modules\Inscritos\Model\TurnoEstudo;
use iEducar\Modules\Inscritos\Model\AvaliacaoEtapa;

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesFichaMedicaAluno.inc.php';
require_once 'include/modules/clsModulesMoradiaAluno.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once 'Transporte/Model/AlunoDataMapper.php';

require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/CustomLabel.php';
require_once 'lib/Portabilis/Date/Utils.php';
class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Candidato Processo Seletivo');
        $this->processoAp = 21469;
    }
}
class indice extends clsDetalhe
{
    public $titulo;
    public $cod_aluno;
    public $cod_inscrito;
    public $ref_idpes_responsavel;
    public $idpes_pai;
    public $idpes_mae;
    public $ref_cod_pessoa_educ;
    public $ref_cod_aluno_beneficio;
    public $ref_cod_religiao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idpes;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_pai;
    public $nm_mae;
    public $ref_cod_raca;
    public $sus;
    public $url_laudo_medico;
    public $url_documento;

    public function Gerar()
    {
        @session_start();
        unset($_SESSION['reload_faixa_etaria']);
        unset($_SESSION['reload_reserva_vaga']);
        session_write_close();

        // Verificação de permissão para cadastro.
        $this->obj_permissao = new clsPermissoes();

        if ($_GET['cod_inscrito']) {
            $this->cod_inscrito = $_GET['cod_inscrito'];
            $objInscrito = new clsPmieducarInscrito();
            $objInscrito->cod_inscrito = $this->cod_inscrito;
            $registroInscrito = $objInscrito->detalhe();
            $this->cod_aluno = $registroInscrito['ref_cod_aluno'];
        } elseif ($_GET['cod_aluno']) {
            $this->cod_aluno = $_GET['cod_aluno'];
            $objInscrito = new clsPmieducarInscrito();
            $objInscrito->cod_aluno = $this->cod_aluno;
            $processoSeletivo = $objInscrito->getUltimoProcessoSeletivo();
            if ($processoSeletivo) {
                $objInscrito->ref_cod_selecao_processo
                    = $processoSeletivo['ref_cod_selecao_processo'];
            }
            $registroAluno = $objInscrito->detalhe();
            $this->cod_inscrito = $registroAluno['cod_inscrito'];
        }

        $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);
        $this->titulo = 'Inscrito - Detalhe';
        $tmp_obj = new clsPmieducarAluno($this->cod_aluno);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            header('Location: selecao_inscritos_lst.php');
            die();
        } else {
            foreach ($registro as $key => $value) {
                $this->$key = $value;
            }
        }

        if ($this->ref_idpes) {
            $obj_pessoa_fj = new clsPessoaFj($this->ref_idpes);
            $det_pessoa_fj = $obj_pessoa_fj->detalhe();

            $obj_fisica = new clsFisica($this->ref_idpes);
            $det_fisica = $obj_fisica->detalhe();

            $obj_fisica_raca = new clsCadastroFisicaRaca();
            $lst_fisica_raca = $obj_fisica_raca->lista($this->ref_idpes);

            if ($lst_fisica_raca) {
                $det_fisica_raca = array_shift($lst_fisica_raca);
                $obj_raca = new clsCadastroRaca($det_fisica_raca['ref_cod_raca']);
                $det_raca = $obj_raca->detalhe();
            }

            $objFoto = new clsCadastroFisicaFoto($this->ref_idpes);
            $detalheFoto = $objFoto->detalhe();

            if ($detalheFoto) {
                $caminhoFoto = $detalheFoto['caminho'];
            }

            $registro['nome_aluno'] = strtoupper($det_pessoa_fj['nome']);
            $registro['cpf'] = int2IdFederal($det_fisica['cpf']);
            $registro['data_nasc'] = Portabilis_Date_Utils::pgSQLToBr($det_fisica['data_nasc']);

            $opcoes = [
                'F' => 'Feminino',
                'M' => 'Masculino',
            ];

            $registro['sexo'] = $det_fisica['sexo'] ? $opcoes[$det_fisica['sexo']] : '';

            $obj_estado_civil = new clsEstadoCivil();
            $obj_estado_civil_lista = $obj_estado_civil->lista();

            $lista_estado_civil = [];

            if ($obj_estado_civil_lista) {
                foreach ($obj_estado_civil_lista as $estado_civil) {
                    $lista_estado_civil[$estado_civil['ideciv']] = $estado_civil['descricao'];
                }
            }

            $registro['ideciv'] = $lista_estado_civil[$det_fisica['ideciv']->ideciv];
            $registro['email'] = $det_pessoa_fj['email'];
            $registro['url'] = $det_pessoa_fj['url'];

            $registro['nacionalidade'] = $det_fisica['nacionalidade'];
            $registro['nis_pis_pasep'] = $det_fisica['nis_pis_pasep'];

            $registro['naturalidade'] = $det_fisica['idmun_nascimento']->detalhe();
            $registro['naturalidade'] = $registro['naturalidade']['nome'];

            $registro['pais_origem'] = $det_fisica['idpais_estrangeiro']->detalhe();
            $registro['pais_origem'] = $registro['pais_origem']['nome'];

            $registro['ref_idpes_responsavel'] = $det_fisica['idpes_responsavel'];

            $this->idpes_pai = $det_fisica['idpes_pai'];
            $this->idpes_mae = $det_fisica['idpes_mae'];

            $this->sus = $det_fisica['sus'];

            $this->nm_pai = $registro['nm_pai'];
            $this->nm_mae = $registro['nm_mae'];

            if ($this->idpes_pai) {
                $obj_pessoa_pai = new clsPessoaFj($this->idpes_pai);
                $det_pessoa_pai = $obj_pessoa_pai->detalhe();

                if ($det_pessoa_pai) {
                    $registro['nm_pai'] = $det_pessoa_pai['nome'];

                    // CPF
                    $obj_cpf = new clsFisica($this->idpes_pai);
                    $det_cpf = $obj_cpf->detalhe();

                    if ($det_cpf['cpf']) {
                        $this->cpf_pai = int2CPF($det_cpf['cpf']);
                    }
                }
            }

            if ($this->idpes_mae) {
                $obj_pessoa_mae = new clsPessoaFj($this->idpes_mae);
                $det_pessoa_mae = $obj_pessoa_mae->detalhe();

                if ($det_pessoa_mae) {
                    $registro['nm_mae'] = $det_pessoa_mae['nome'];

                    // CPF
                    $obj_cpf = new clsFisica($this->idpes_mae);
                    $det_cpf = $obj_cpf->detalhe();

                    if ($det_cpf['cpf']) {
                        $this->cpf_mae = int2CPF($det_cpf['cpf']);
                    }
                }
            }

            $registro['ddd_fone_1'] = $det_pessoa_fj['ddd_1'];
            $registro['fone_1'] = $det_pessoa_fj['fone_1'];

            $registro['ddd_fone_2'] = $det_pessoa_fj['ddd_2'];
            $registro['fone_2'] = $det_pessoa_fj['fone_2'];

            $registro['ddd_fax'] = $det_pessoa_fj['ddd_fax'] ?? null;
            $registro['fone_fax'] = $det_pessoa_fj['fone_fax'] ?? null;

            $registro['ddd_mov'] = $det_pessoa_fj['ddd_mov'] ?? null;
            $registro['fone_mov'] = $det_pessoa_fj['fone_mov'] ?? null;

            $obj_deficiencia_pessoa = new clsCadastroFisicaDeficiencia();
            $obj_deficiencia_pessoa_lista = $obj_deficiencia_pessoa->lista($this->ref_idpes);

            $obj_beneficios = new clsPmieducarAlunoBeneficio();
            $obj_beneficios_lista = $obj_beneficios->lista(null, null, null, null, null, null, null, null, null, null, $this->cod_aluno);

            if ($obj_deficiencia_pessoa_lista) {
                $deficiencia_pessoa = [];

                foreach ($obj_deficiencia_pessoa_lista as $deficiencia) {
                    $obj_def = new clsCadastroDeficiencia($deficiencia['ref_cod_deficiencia']);
                    $det_def = $obj_def->detalhe();

                    $deficiencia_pessoa[$deficiencia['ref_cod_deficiencia']] = $det_def['nm_deficiencia'];
                }
            }

            $ObjDocumento = new clsDocumento($this->ref_idpes);
            $detalheDocumento = $ObjDocumento->detalhe();

            $registro['rg'] = $detalheDocumento['rg'];

            if ($detalheDocumento['data_exp_rg']) {
                $registro['data_exp_rg'] = date(
                    'd/m/Y',
                    strtotime(substr($detalheDocumento['data_exp_rg'], 0, 19))
                );
            }

            $registro['sigla_uf_exp_rg'] = $detalheDocumento['sigla_uf_exp_rg'];
            $registro['tipo_cert_civil'] = $detalheDocumento['tipo_cert_civil'];
            $registro['certidao_nascimento'] = $detalheDocumento['certidao_nascimento'];
            $registro['certidao_casamento'] = $detalheDocumento['certidao_casamento'];
            $registro['num_termo'] = $detalheDocumento['num_termo'];
            $registro['num_livro'] = $detalheDocumento['num_livro'];
            $registro['num_folha'] = $detalheDocumento['num_folha'];

            if ($detalheDocumento['data_emissao_cert_civil']) {
                $registro['data_emissao_cert_civil'] = date(
                    'd/m/Y',
                    strtotime(substr($detalheDocumento['data_emissao_cert_civil'], 0, 19))
                );
            }

            $registro['sigla_uf_cert_civil'] = $detalheDocumento['sigla_uf_cert_civil'];
            $registro['cartorio_cert_civil'] = $detalheDocumento['cartorio_cert_civil'];
            $registro['num_cart_trabalho'] = $detalheDocumento['num_cart_trabalho'];
            $registro['serie_cart_trabalho'] = $detalheDocumento['serie_cart_trabalho'];

            if ($detalheDocumento['data_emissao_cart_trabalho']) {
                $registro['data_emissao_cart_trabalho'] = date(
                    'd/m/Y',
                    strtotime(substr($detalheDocumento['data_emissao_cart_trabalho'], 0, 19))
                );
            }

            $registro['sigla_uf_cart_trabalho'] = $detalheDocumento['sigla_uf_cart_trabalho'];
            $registro['num_tit_eleitor'] = $detalheDocumento['num_titulo_eleitor'] ?? null;
            $registro['zona_tit_eleitor'] = $detalheDocumento['zona_titulo_eleitor'] ?? null;
            $registro['secao_tit_eleitor'] = $detalheDocumento['secao_titulo_eleitor'] ?? null;
            $registro['idorg_exp_rg'] = $detalheDocumento['ref_idorg_rg'] ?? null;

            $obj_endereco = new clsPessoaEndereco($this->ref_idpes);

            if ($obj_endereco_det = $obj_endereco->detalhe()) {
                $registro['id_cep'] = $obj_endereco_det['cep']->cep;
                $registro['id_bairro'] = $obj_endereco_det['idbai'];
                $registro['id_logradouro'] = $obj_endereco_det['idlog'];
                $registro['numero'] = $obj_endereco_det['numero'];
                $registro['letra'] = $obj_endereco_det['letra'];
                $registro['complemento'] = $obj_endereco_det['complemento'];
                $registro['andar'] = $obj_endereco_det['andar'];
                $registro['apartamento'] = $obj_endereco_det['apartamento'];
                $registro['bloco'] = $obj_endereco_det['bloco'];
                $registro['nm_logradouro'] = $obj_endereco_det['logradouro'] ?? null;
                $registro['cep_'] = int2CEP($registro['id_cep']);

                $obj_bairro = new clsBairro($registro['id_bairro']);
                $obj_bairro_det = $obj_bairro->detalhe();

                if ($obj_bairro_det) {
                    $registro['nm_bairro'] = $obj_bairro_det['nome'];
                }

                $obj_log = new clsLogradouro($registro['id_logradouro']);
                $obj_log_det = $obj_log->detalhe();

                if ($obj_log_det) {
                    $registro['nm_logradouro'] = $obj_log_det['nome'];
                    $registro['idtlog'] = $obj_log_det['idtlog']->detalhe();
                    $registro['idtlog'] = $registro['idtlog']['descricao'];

                    $obj_mun = new clsMunicipio($obj_log_det['idmun']);
                    $det_mun = $obj_mun->detalhe();

                    if ($det_mun) {
                        $registro['cidade'] = ucfirst(strtolower($det_mun['nome']));
                    }
                }

                $obj_bairro = new clsBairro($registro['id_bairro']);
                $obj_bairro_det = $obj_bairro->detalhe();

                if ($obj_bairro_det) {
                    $registro['nm_bairro'] = $obj_bairro_det['nome'];
                }
            } else {
                $obj_endereco = new clsEnderecoExterno($this->ref_idpes);

                if ($obj_endereco_det = $obj_endereco->detalhe()) {
                    $registro['id_cep'] = $obj_endereco_det['cep'];
                    $registro['cidade'] = $obj_endereco_det['cidade'];
                    $registro['nm_bairro'] = $obj_endereco_det['bairro'];
                    $registro['nm_logradouro'] = $obj_endereco_det['logradouro'];
                    $registro['numero'] = $obj_endereco_det['numero'];
                    $registro['letra'] = $obj_endereco_det['letra'];
                    $registro['complemento'] = $obj_endereco_det['complemento'];
                    $registro['andar'] = $obj_endereco_det['andar'];
                    $registro['apartamento'] = $obj_endereco_det['apartamento'];
                    $registro['bloco'] = $obj_endereco_det['bloco'];
                    $registro['idtlog'] = $obj_endereco_det['idtlog']->detalhe();
                    $registro['idtlog'] = $registro['idtlog']['descricao'];

                    $det_uf = $obj_endereco_det['sigla_uf']->detalhe();
                    $registro['ref_sigla_uf'] = $det_uf['nome'] ?? null;

                    $registro['cep_'] = int2CEP($registro['id_cep']);
                }
            }
        }

        if ($registro['cod_aluno']) {
            $this->addDetalhe([_cl('aluno.detalhe.codigo_aluno'), $registro['cod_aluno']]);
        }

        // código inep
        $alunoMapper = new Educacenso_Model_AlunoDataMapper();
        $alunoInep = null;

        try {
            $alunoInep = $alunoMapper->find(['aluno' => $this->cod_aluno]);

            $configuracoes = new clsPmieducarConfiguracoesGerais();
            $configuracoes = $configuracoes->detalhe();

            if ($configuracoes['mostrar_codigo_inep_aluno']) {
                $this->addDetalhe(['Código inep', $alunoInep->alunoInep]);
            }
        } catch (Exception $e) {
        }

        // código estado
        $this->addDetalhe([_cl('aluno.detalhe.codigo_estado'), $registro['aluno_estado_id']]);

        if ($registro['caminho_foto']) {
            $this->addDetalhe([
                'Foto',
                sprintf(
                    '<img src="arquivos/educar/aluno/small/%s" border="0">',
                    $registro['caminho_foto']
                )
            ]);
        }

        if ($registro['nome_aluno']) {
            if ($caminhoFoto != null and $caminhoFoto != '') {
                $this->addDetalhe([
                    'Nome Aluno',
                    $registro['nome_aluno'] . '<p><img height="117" src="' . $caminhoFoto . '"/></p>'
                ]);
            } else {
                $this->addDetalhe(['Nome Aluno', $registro['nome_aluno']]);
            }
        }

        if ($det_fisica['nome_social']) {
            $this->addDetalhe(['Nome Social', strtoupper($det_fisica['nome_social'])]);
        }

        if (idFederal2int($registro['cpf'])) {
            $this->addDetalhe(['CPF', $registro['cpf']]);
        }

        if ($registro['data_nasc']) {
            $this->addDetalhe(['Data de Nascimento', $registro['data_nasc']]);
        }

        /**
         * Analfabeto.
         */
        $this->addDetalhe(['Analfabeto', $registro['analfabeto'] == 0 ? 'Não' : 'Sim']);

        if ($registro['sexo']) {
            $this->addDetalhe(['Sexo', $registro['sexo']]);
        }

        if ($registro['ideciv']) {
            $this->addDetalhe(['Estado Civil', $registro['ideciv']]);
        }

        if ($registro['id_cep']) {
            $this->addDetalhe(['CEP', $registro['cep_']]);
        }

        if (isset($registro['ref_sigla_uf']) && !empty($registro['ref_sigla_uf'])) {
            $this->addDetalhe(['UF', $registro['ref_sigla_uf'] ?? null]);
        }

        if ($registro['cidade']) {
            $this->addDetalhe(['Cidade', $registro['cidade']]);
        }

        if ($registro['nm_bairro']) {
            $this->addDetalhe(['Bairro', $registro['nm_bairro']]);
        }

        if ($registro['nm_logradouro']) {
            $logradouro = '';

            if ($registro['idtlog']) {
                $logradouro .= $registro['idtlog'] . ' ';
            }

            $logradouro .= $registro['nm_logradouro'];
            $this->addDetalhe(['Logradouro', $logradouro]);
        }

        if ($registro['numero']) {
            $this->addDetalhe(['Número', $registro['numero']]);
        }

        if ($registro['letra']) {
            $this->addDetalhe(['Letra', $registro['letra']]);
        }

        if ($registro['complemento']) {
            $this->addDetalhe(['Complemento', $registro['complemento']]);
        }

        if ($registro['bloco']) {
            $this->addDetalhe(['Bloco', $registro['bloco']]);
        }

        if ($registro['andar']) {
            $this->addDetalhe(['Andar', $registro['andar']]);
        }

        if ($registro['apartamento']) {
            $this->addDetalhe(['Apartamento', $registro['apartamento']]);
        }

        if ($registro['naturalidade']) {
            $this->addDetalhe(['Naturalidade', $registro['naturalidade']]);
        }

        if ($registro['nacionalidade']) {
            $lista_nacionalidade = [
                'NULL' => 'Selecione',
                1 => 'Brasileiro',
                2 => 'Naturalizado Brasileiro',
                3 => 'Estrangeiro'
            ];

            $registro['nacionalidade'] = $lista_nacionalidade[$registro['nacionalidade']];
            $this->addDetalhe(['Nacionalidade', $registro['nacionalidade']]);
        }

        if ($registro['pais_origem']) {
            $this->addDetalhe(['País de Origem', $registro['pais_origem']]);
        }

        $responsavel = $tmp_obj->getResponsavelAluno();

        if ($responsavel && is_null($registro['ref_idpes_responsavel'])) {
            $this->addDetalhe(['Nome do Responsável', $responsavel['nome_responsavel']]);
        }

        if ($registro['ref_idpes_responsavel']) {
            $obj_pessoa_resp = new clsPessoaFj($registro['ref_idpes_responsavel']);
            $det_pessoa_resp = $obj_pessoa_resp->detalhe();

            if ($det_pessoa_resp) {
                $registro['ref_idpes_responsavel'] = $det_pessoa_resp['nome'];
            }

            $this->addDetalhe(['Responsável', $registro['ref_idpes_responsavel']]);
        }

        if ($registro['nm_pai']) {
            $this->addDetalhe(['Pai', $registro['nm_pai']]);
        }

        if ($registro['nm_mae']) {
            $this->addDetalhe(['Mãe', $registro['nm_mae']]);
        }

        if ($registro['fone_1']) {
            if ($registro['ddd_fone_1']) {
                $registro['ddd_fone_1'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_1']);
            }

            $this->addDetalhe(['Telefone 1', $registro['ddd_fone_1'] . $registro['fone_1']]);
        }

        if ($registro['fone_2']) {
            if ($registro['ddd_fone_2']) {
                $registro['ddd_fone_2'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_2']);
            }

            $this->addDetalhe(['Telefone 2', $registro['ddd_fone_2'] . $registro['fone_2']]);
        }

        if ($registro['fone_mov']) {
            if ($registro['ddd_mov']) {
                $registro['ddd_mov'] = sprintf('(%s)&nbsp;', $registro['ddd_mov']);
            }

            $this->addDetalhe(['Celular', $registro['ddd_mov'] . $registro['fone_mov']]);
        }

        if ($registro['fone_fax']) {
            if ($registro['ddd_fax']) {
                $registro['ddd_fax'] = sprintf('(%s)&nbsp;', $registro['ddd_fax']);
            }

            $this->addDetalhe(['Fax', $registro['ddd_fax'] . $registro['fone_fax']]);
        }

        if ($registro['email']) {
            $this->addDetalhe(['E-mail', $registro['email']]);
        }

        if ($registro['url']) {
            $this->addDetalhe(['Página Pessoal', $registro['url']]);
        }

        if ($registro['ref_cod_religiao']) {
            $obj_religiao = new clsPmieducarReligiao($registro['ref_cod_religiao']);
            $obj_religiao_det = $obj_religiao->detalhe();

            $this->addDetalhe(['Religião', $obj_religiao_det['nm_religiao']]);
        }

        if ($det_raca['nm_raca']) {
            $this->addDetalhe(['Raça', $det_raca['nm_raca']]);
        }

        if ($obj_beneficios_lista) {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Benefícios</td></tr>';
            $cor = '#D1DADF';

            foreach ($obj_beneficios_lista as $reg) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';

                $tabela .= sprintf(
                    '<tr bgcolor="%s" align="center"><td>%s</td></tr>',
                    $cor,
                    $reg['nm_beneficio']
                );
            }

            $tabela .= '</table>';

            $this->addDetalhe(['Benefícios', $tabela]);
        }

        if ($deficiencia_pessoa) {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Deficiências</td></tr>';
            $cor = '#D1DADF';

            foreach ($deficiencia_pessoa as $indice => $valor) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';

                $tabela .= sprintf(
                    '<tr bgcolor="%s" align="center"><td>%s</td></tr>',
                    $cor,
                    $valor
                );
            }

            $tabela .= '</table>';

            $this->addDetalhe(['Deficiências', $tabela]);
        }

        if ($registro['url_documento'] && $registro['url_documento'] != '') {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Documentos</td></tr>';
            $cor = '#e9f0f8';

            $arrayDocumentos = json_decode($registro['url_documento']);
            foreach ($arrayDocumentos as $key => $documento) {
                $cor = $cor == '#e9f0f8' ? '#f5f9fd' : '#e9f0f8';

                $tabela .= '<tr bgcolor=\'' . $cor . '\'
                        align=\'center\'>
                          <td>
                            <a href=\'' . $documento->url . '\'
                               target=\'_blank\' > Visualizar documento ' . (count($documento) > 1 ? ($key + 1) : '') . '
                            </a>
                          </td>
                    </tr>';
            }

            $tabela .= '</table>';
            $this->addDetalhe(['Documentos do aluno', $tabela]);
        }

        if ($registro['url_laudo_medico'] && $registro['url_laudo_medico'] != '') {
            $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#ccdce6" align="center"><td>Laudo médico</td></tr>';

            $cor = '#D1DADF';

            $arrayLaudoMedico = json_decode($registro['url_laudo_medico']);
            foreach ($arrayLaudoMedico as $key => $laudoMedico) {
                $cor = $cor == '#D1DADF' ? '#f5f9fd' : '#D1DADF';

                $tabela .= "<tr bgcolor='{$cor}' align='center'><td><a href='{$laudoMedico->url}' target='_blank' > Visualizar laudo " . (count($arrayLaudoMedico) > 1 ? ($key + 1) : '') . ' </a></td></tr>';
            }

            $tabela .= '</table>';
            $this->addDetalhe(['Laudo médico do aluno', $tabela]);
        }

        if ($registro['rg']) {
            $this->addDetalhe(['RG', $registro['rg']]);
        }

        if ($registro['data_exp_rg']) {
            $this->addDetalhe(['Data de Expedição RG', $registro['data_exp_rg']]);
        }

        if ($registro['idorg_exp_rg']) {
            $this->addDetalhe(['Órgão Expedição RG', $registro['idorg_exp_rg']]);
        }

        if ($registro['sigla_uf_exp_rg']) {
            $this->addDetalhe(['Estado Expedidor', $registro['sigla_uf_exp_rg']]);
        }

        /**
         * @todo CoreExt_Enum?
         */
        if (!$registro['tipo_cert_civil'] && $registro['certidao_nascimento']) {
            $this->addDetalhe(['Tipo Certidão Civil', 'Nascimento (novo formato)']);
            $this->addDetalhe(['Número Certidão Civil', $registro['certidao_nascimento']]);
        } else {
            if (!$registro['tipo_cert_civil'] && $registro['certidao_casamento']) {
                $this->addDetalhe(['Tipo Certidão Civil', 'Casamento (novo formato)']);
                $this->addDetalhe(['Número Certidão Civil', $registro['certidao_casamento']]);
            } else {
                $lista_tipo_cert_civil = [];
                $lista_tipo_cert_civil['0'] = 'Selecione';
                $lista_tipo_cert_civil[91] = 'Nascimento (antigo formato)';
                $lista_tipo_cert_civil[92] = 'Casamento (antigo formato)';

                $this->addDetalhe(['Tipo Certidão Civil', $lista_tipo_cert_civil[$registro['tipo_cert_civil']]]);

                if ($registro['num_termo']) {
                    $this->addDetalhe(['Termo', $registro['num_termo']]);
                }

                if ($registro['num_livro']) {
                    $this->addDetalhe(['Livro', $registro['num_livro']]);
                }

                if ($registro['num_folha']) {
                    $this->addDetalhe(['Folha', $registro['num_folha']]);
                }
            }
        }

        if ($registro['data_emissao_cert_civil']) {
            $this->addDetalhe(['Emissão Certidão Civil', $registro['data_emissao_cert_civil']]);
        }

        if ($registro['sigla_uf_cert_civil']) {
            $this->addDetalhe(['Sigla Certidão Civil', $registro['sigla_uf_cert_civil']]);
        }

        if ($registro['cartorio_cert_civil']) {
            $this->addDetalhe(['Cartório', $registro['cartorio_cert_civil']]);
        }

        if ($registro['num_tit_eleitor']) {
            $this->addDetalhe(['Título de Eleitor', $registro['num_tit_eleitor']]);
        }

        if ($registro['zona_tit_eleitor']) {
            $this->addDetalhe(['Zona', $registro['zona_tit_eleitor']]);
        }

        if ($registro['secao_tit_eleitor']) {
            $this->addDetalhe(['Seção', $registro['secao_tit_eleitor']]);
        }

        if (trim($reg['grupo_sanguineo']) != '') {
            $this->addDetalhe(['Grupo sanguíneo', $reg['grupo_sanguineo']]);
        }

        if (trim($reg['fator_rh']) != '') {
            $this->addDetalhe(['Fator RH', $reg['fator_rh']]);
        }

        $objInscrito = new clsPmieducarInscrito();
        $objInscrito->ref_cod_aluno = $this->cod_aluno;
        $processoSeletivo = $objInscrito->getUltimoProcessoSeletivo();

        if ($processoSeletivo) {
            $objInscrito->ref_cod_aluno = $this->cod_aluno;
            $objInscrito->ref_cod_selecao_processo = $processoSeletivo['ref_cod_selecao_processo'];
            $reg = $objInscrito->detalhe();

            if ($reg) {

                $this->addDetalhe(
                    [
                        '<span id=\'fselecao\'></span>Proceso Seletivo',
                        $processoSeletivo['ref_ano']
                    ]
                );

                if ($reg['estudando_serie']) {
                    $resources = SerieEstudo::getDescriptiveValues();

                    $this->addDetalhe(
                        ['Série em que estuda', $resources[$reg['estudando_serie']]]
                    );
                }

                if ($reg['estudando_turno']) {
                    $resources = TurnoEstudo::getDescriptiveValues();

                    $this->addDetalhe(
                        ['Turno em que estuda', $resources[$reg['estudando_turno']]]
                    );
                }

                if ($reg['egresso']) {
                    $this->addDetalhe(
                        ['Ano de conclusão estudo', $reg['egresso']]
                    );
                }

                $objEtapa = new clsPmieducarInscritoEtapa();
                $registroEtapa = $objEtapa->lista($this->cod_inscrito);

                $avaliacao = AvaliacaoEtapa::getDescriptiveValues();

                foreach ($registroEtapa as $etapa) {
                    if ($etapa['etapa'] && $etapa['situacao']) {
                        $this->addDetalhe(
                            [
                                'Situação Etapa ' . $etapa['etapa'],
                                $avaliacao[$etapa['situacao']]
                            ]
                        );
                    }
                }

                $area_selecionado = array(
                    ''  => 'Selecione uma turma',
                    3   => 'T&A - Manhã',
                    4   => 'T&A - Tarde',
                    5   => 'Comércio - Manhã',
                    6   => 'Comércio - Tarde',
                    7   => 'Hospedagem - Manhã',
                    8   => 'Eventos - Tarde'
                );

                if (!empty($reg['area_selecionado'])) {
                    $this->addDetalhe(
                        [
                            'Turma',
                            $area_selecionado[$reg['area_selecionado']]
                        ]
                    );
                }

                $this->addDetalhe(
                    [
                        'Inscrito na Guarda Mirim',
                        ($reg['guarda_mirim'] ? 'Sim' : 'Não')
                    ]
                );
                $this->addDetalhe(
                    [
                        'Encaminhado pela rede de Proteção',
                        ($reg['encaminhamento'] ? 'Sim' : 'Não')
                    ]
                );
                $this->addDetalhe(
                    [
                        'Entregou cópia RG',
                        ($reg['copia_rg'] == 2 ? 'Sim' : 'Não')
                    ]
                );
                $this->addDetalhe(
                    [
                        'Entregou cópia CPF',
                        ($reg['copia_cpf'] == 2 ? 'Sim' : 'Não')
                    ]
                );
                $this->addDetalhe(
                    [
                        'Entregou cópia Comprovante de residência',
                        ($reg['copia_residencia'] == 2 ? 'Sim' : 'Não')
                    ]
                );
                $this->addDetalhe(
                    [
                        'Entregou cópia histórico / comprovante matrícula',
                        ($reg['copia_historico'] == 2 ? 'Sim' : 'Não')
                    ]
                );
                $this->addDetalhe(
                    [
                        'Entregou comprovante de renda',
                        ($reg['copia_renda'] == 2 ? 'Sim' : 'Não')
                    ]
                );
            }
        }

        $this->url_cancelar = 'selecao_inscritos_lst.php';

        $this->url_editar = '/module/Cadastro/Inscrito?id=' . $this->cod_inscrito;

        $this->largura = '100%';
        $this->addDetalhe("<input type='hidden' id='escola_id' name='aluno_id' value='{$registro['ref_cod_escola']}' />");
        $this->addDetalhe("<input type='hidden' id='aluno_id' name='aluno_id' value='{$registro['cod_aluno']}' />");
        $mostraDependencia = $GLOBALS['coreExt']['Config']->app->matricula->dependencia;
        $this->addDetalhe("<input type='hidden' id='can_show_dependencia' name='can_show_dependencia' value='{$mostraDependencia}' />");

        $this->breadcrumb('Aluno', ['/intranet/educar_index.php' => 'Escola']);
        // js
        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Cadastro/Assets/Javascripts/InscritoShow.js?version=3'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $styles = ['/modules/Cadastro/Assets/Stylesheets/Aluno.css'];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }
}

// Instancia o objeto da página
$pagina = new clsIndexBase();

// Instancia o objeto de conteúdo
$miolo = new indice();

// Passa o conteúdo para a página
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
