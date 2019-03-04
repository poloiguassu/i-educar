<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

use iEducar\Modules\Inscritos\Model\SerieEstudo;
use iEducar\Modules\Inscritos\Model\TurnoEstudo;
use iEducar\Modules\Inscritos\Model\AvaliacaoEtapa;

require_once 'include/clsCadastro.inc.php';
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/clsPmieducarInstituicao.inc.php";
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'image_check.php';
require_once 'App/Model/ZonaLocalizacao.php';
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'lib/Portabilis/Utils/CustomLabel.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'Portabilis/String/Utils.php';

class InscritoController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Cadastro de Pré Inscrito';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    protected $_processoAp = 43;

    protected $_deleteOption = true;

    protected $cod_selcao_inscrito;

    // Variáveis para controle da foto
    var $objPhoto;

    var $arquivoFoto;

    var $file_delete;

    var $caminho_det;

    var $caminho_lst;

    protected $_formMap = array(
        'pessoa' => array(
            'label' => 'Pessoa',
            'help' => '',
        ),

        'pai' => array(
            'label' => 'Pai',
            'help' => '',
        ),

        'mae' => array(
            'label' => 'Mãe',
            'help' => '',
        ),

        'responsavel' => array(
            'label' => 'Responsável',
            'help' => '',
        ),

        'documento' => array(
            'label' => 'Documentos',
            'help' => '',
        ),

        /* *******************
           ** Dados médicos **
           ******************* */
        'grupo_sanguineo' => array('label' => 'Grupo sanguíneo'),

        'fator_rh' => array('label' => 'Fator RH'),

        'deficiencias' => array(
            'label' => 'Deficiências / habilidades especiais',
            'help' => '',
        ),
    );


    protected function _preConstruct()
    {
        $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos(array(
            $_SERVER['SERVER_NAME'] . "/intranet" => "In&iacute;cio",
            "educar_index.php" => "Escola",
            "" => "$nomeMenu aluno"
        ));

        $this->enviaLocalizacao($localizacao->montar());
    }


    protected function _initNovo()
    {
        return false;
    }


    protected function _initEditar()
    {
        return false;
    }


    public function Gerar()
    {
        $this->url_cancelar = '/intranet/selecao_inscritos_lst.php';

        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $configuracoes = $configuracoes->detalhe();

        $labels_botucatu = $GLOBALS['coreExt']['Config']->app->mostrar_aplicacao == 'botucatu';

        if ($configuracoes["justificativa_falta_documentacao_obrigatorio"]) {
            $this->inputsHelper()->hidden('justificativa_falta_documentacao_obrigatorio');
        }

        $cod_inscrito = $_GET['id'];

        if ($cod_inscrito or $_GET['person']) {
            if ($_GET['person']) {
                $this->cod_pessoa_fj = $_GET['person'];
                $this->inputsHelper()->hidden(
                    'person',
                    array('value' => $this->cod_pessoa_fj)
                );

                $db = new clsBanco();
                $this->ref_cod_aluno = $db->CampoUnico(
                    "SELECT
                        cod_aluno
                    FROM
                        pmieducar.aluno
                    WHERE
                        ref_idpes = {$this->cod_pessoa_fj}"
                );

            } else {
                $db = new clsBanco();
                $this->ref_cod_aluno = $db->CampoUnico(
                    "SELECT
                        ref_cod_aluno
                    FROM
                        pmieducar.inscrito
                    WHERE
                        cod_inscrito = {$cod_inscrito}"
                );
            }

            $documentos = new clsDocumento();
            $documentos->idpes = $this->cod_pessoa_fj;
            $documentos = $documentos->detalhe();
        }

        $this->inputsHelper()->processoSeletivo(
            array('required' => false, 'label' => 'Processo Seletivo')
        );

        $this->inputsHelper()->hidden(
            'aluno_id', array('value' => $this->ref_cod_aluno)
        );

        $foto = false;

        if (is_numeric($this->cod_pessoa_fj)) {
            $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
            $detalheFoto = $objFoto->detalhe();
            if (count($detalheFoto)) {
                $foto = $detalheFoto['caminho'];
            }
        } else {
            $foto = false;
        }

        if ($foto) {
            $this->campoRotulo('fotoAtual_', 'Foto atual', '<img height="117" src="' . $foto . '"/>');
            $this->inputsHelper()->checkbox('file_delete', array('label' => 'Excluir a foto'));
            $this->campoArquivo('file', 'Trocar foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 150KB</span>');
        } else {
            $this->campoArquivo('file', 'Foto', $this->arquivoFoto, 40, '<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m&aacute;ximo: 150KB</span>');
        }


        // nome
        $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);

        // data nascimento
        $options = array(
            'label' => 'Data de nascimento',
            'disabled' => true,
            'required' => false,
            'size' => 25,
            'placeholder' => ''
        );

        $this->inputsHelper()->date('data_nascimento', $options);

        $options = array(
            'required' => $required,
            'label' => 'RG / Data emissão',
            'placeholder' => 'Documento identidade',
            'value' => $documentos['rg'],
            'max_length' => 25,
            'size' => 27,
            'inline' => true
        );

        $this->inputsHelper()->text('rg', $options);

        // data emissão rg
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emiss\u00e3o',
            'value' => $documentos['data_exp_rg'],
            'size' => 19
        );

        $this->inputsHelper()->date('data_emissao_rg', $options);

        $selectOptions = array( null => 'Órgão emissor' );
        $orgaos        = new clsOrgaoEmissorRg();
        $orgaos        = $orgaos->lista();

        foreach ($orgaos as $orgao)
          $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];

        $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

        $options = array(
          'required'  => false,
          'label'     => '',
          'value'     => $documentos['idorg_exp_rg'],
          'resources' => $selectOptions,
          'inline'    => true
        );

        $this->inputsHelper()->select('orgao_emissao_rg', $options);


        // uf emissão rg

        $options = array(
          'required' => false,
          'label'    => '',
          'value'    => $documentos['sigla_uf_exp_rg']
        );

        $helperOptions = array(
          'attrName' => 'uf_emissao_rg'
        );

        $this->inputsHelper()->uf($options, $helperOptions);

        // cpf
        if (is_numeric($this->cod_pessoa_fj)) {
            $fisica = new clsFisica($this->cod_pessoa_fj);
            $fisica = $fisica->detalhe();
            $valorCpf = is_numeric($fisica['cpf']) ? int2CPF($fisica['cpf']) : '';
        }
        $this->campoCpf("id_federal", "CPF", $valorCpf);

        // pai
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',

            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_um', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_um', $options);

        //dois
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_dois', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_dois', $options);

        // pai
        $this->inputPai();

        // mãe
        $this->inputMae();

        $label = Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel'));

        $tiposResponsavel = array(null => 'Informe uma Pessoa primeiro');
        $options = array(
            'label' => Portabilis_String_Utils::toLatin1('Responsável'),
            'resources' => $tiposResponsavel,
            'required' => true,
            'inline' => true
        );

        $this->inputsHelper()->select('tipo_responsavel', $options);

        // nome
        $helperOptions = array('objectName' => 'responsavel');
        $options = array('label' => '', 'size' => 50, 'required' => true);

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);

        // religião
        $this->inputsHelper()->religiao(array('required' => false, 'label' => Portabilis_String_Utils::toLatin1('Religião')));

        // Benefícios
        $helperOptions = array('objectName' => 'beneficios');
        $options = array(
            'label' => Portabilis_String_Utils::toLatin1('Benefícios'),
            'size' => 250,
            'required' => false,
            'options' => array('value' => null)
        );

        $this->inputsHelper()->multipleSearchBeneficios('', $options, $helperOptions);

        // Deficiências / habilidades especiais
        $helperOptions = array('objectName' => 'deficiencias');
        $options = array(
            'label' => $this->_getLabel('deficiencias'),
            'size' => 50,
            'required' => false,
            'options' => array('value' => null)
        );

        $this->inputsHelper()->multipleSearchDeficiencias('', $options, $helperOptions);

        $this->campoArquivo('documento', Portabilis_String_Utils::toLatin1($this->_getLabel('documento')), $this->documento, 40, Portabilis_String_Utils::toLatin1("<br/> <span id='span-documento' style='font-style: italic; font-size= 10px;''> São aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho máximo: 250KB</span>", array('escape' => false)));

        $this->inputsHelper()->hidden('url_documento');

        // grupo_sanguineo
        $options = array('label' => $this->_getLabel('grupo_sanguineo'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('grupo_sanguineo', $options);

        // fator_rh
        $options = array('label' => $this->_getLabel('fator_rh'), 'size' => 5, 'max_length' => 1, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('fator_rh', $options);

        $enderecamentoObrigatorio = false;
        $desativarCamposDefinidosViaCep = true;

        $this->campoCep(
            'cep_',
            'CEP',
            '',
            $enderecamentoObrigatorio,
            '-',
            "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'/intranet/educar_pesquisa_cep_log_bairro2.php?campo1=bairro_bairro&campo2=bairro_id&campo3=cep&campo4=logradouro_logradouro&campo5=logradouro_id&campo6=distrito_id&campo7=distrito_distrito&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
            false
        );

        $options = array('label' => Portabilis_String_Utils::toLatin1('Município'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $helperOptions = array(
            'objectName' => 'municipio',
            'hiddenInputOptions' => array('options' => array('value' => $this->municipio_id))
        );

        $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

        $options = array('label' => Portabilis_String_Utils::toLatin1('Distrito'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $helperOptions = array(
            'objectName' => 'distrito',
            'hiddenInputOptions' => array('options' => array('value' => $this->distrito_id))
        );

        $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

        $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->bairro_id)));

        $options = array('label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localização - <b>Buscar</b>'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

        $options = array(
            'label' => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
            'placeholder' => 'Bairro',
            'value' => $this->bairro,
            'max_length' => 40,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        );

        $this->inputsHelper()->text('bairro', $options);

        // zona localização
        $zonas = App_Model_ZonaLocalizacao::getInstance();
        $zonas = $zonas->getEnums();
        $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

        $options = array(
            'label' => '',
            'placeholder' => 'Zona localização',
            'value' => $this->zona_localizacao,
            'disabled' => $desativarCamposDefinidosViaCep,
            'resources' => $zonas,
            'required' => $enderecamentoObrigatorio
        );

        $this->inputsHelper()->select('zona_localizacao', $options);

        $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->logradouro_id)));

        $options = array('label' => 'Tipo / Logradouro - <b>Buscar</b>', 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

        // tipo logradouro

        $options = array(
            'label' => 'Tipo / Logradouro - <b>Cadastrar</b>',
            'value' => $this->idtlog,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        );

        $helperOptions = array(
            'attrName' => 'idtlog'
        );

        $this->inputsHelper()->tipoLogradouro($options, $helperOptions);

        // logradouro
        $options = array(
            'label' => '',
            'placeholder' => 'Logradouro',
            'value' => '',
            'max_length' => 150,
            'disabled' => $desativarCamposDefinidosViaCep,
            'required' => $enderecamentoObrigatorio
        );

        $this->inputsHelper()->text('logradouro', $options);

        // complemento
        $options = array(
            'required' => false,
            'value' => '',
            'max_length' => 20
        );

        $this->inputsHelper()->text('complemento', $options);

        // numero
        $options = array(
            'required' => false,
            'label' => 'Número / Letra',
            'placeholder' => Portabilis_String_Utils::toLatin1('Número'),
            'value' => '',
            'max_length' => 6,
            'inline' => true
        );

        $this->inputsHelper()->integer('numero', $options);

        // letra
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Letra',
            'value' => $this->letra,
            'max_length' => 1,
            'size' => 15
        );

        $this->inputsHelper()->text('letra', $options);

        // apartamento
        $options = array(
            'required' => false,
            'label' => 'Nº apartamento / Bloco / Andar',
            'placeholder' => 'Apartamento',
            'value' => $this->apartamento,
            'max_length' => 6,
            'inline' => true
        );

        $this->inputsHelper()->integer('apartamento', $options);

        // bloco
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Bloco',
            'value' => $this->bloco,
            'max_length' => 20,
            'size' => 15,
            'inline' => true
        );

        $this->inputsHelper()->text('bloco', $options);

        // andar
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Andar',
            'value' => $this->andar,
            'max_length' => 2
        );

        $this->inputsHelper()->integer('andar', $options);

        $script = '/modules/Cadastro/Assets/Javascripts/Endereco.js';

        Portabilis_View_Helper_Application::loadJavascript($this, $script);

        $this->loadResourceAssets($this->getDispatcher());

        $clsInstituicao = new clsPmieducarInstituicao();
        $instituicao = $clsInstituicao->primeiraAtiva();

        $zonas = array(
            '' => 'Selecione',
            1  => 'Urbana',
            2  => 'Rural',
        );

        $options = array(
          'label'       => 'Zona Localização',
          'value'       => $this->zona_localizacao_censo,
          'resources'   => $zonas,
          'required'    => $obrigarCamposCenso,
        );

        $this->inputsHelper()->select('zona_localizacao_censo', $options);

        $resources = AvaliacaoEtapa::getDescriptiveValues();
        $resources = array_replace([null => '1ª Etapa'], $resources);

        $options = array(
            'required' => false,
            'label'    => "Avaliação Projeto Etapa 1",
            'inline'   => false,
            'value'     => $this->etapa_1,
            'resources' => $resources
        );

        $this->inputsHelper()->select('etapa_1', $options);

        $resources = SerieEstudo::getDescriptiveValues();
        $resources = array_replace([null => 'Série'], $resources);

        $options = array(
            'required'  => false,
            'label'     => 'Série',
            'value'     => $this->serie,
            'resources' => $resources
        );

        $this->inputsHelper()->select('serie', $options);

        $resources = TurnoEstudo::getDescriptiveValues();
        $resources = array_replace([null => 'Turno'], $resources);

        $options = array(
            'required'  => false,
            'label'     => 'Turno em que estuda',
            'value'     => $this->turno,
            'resources' => $resources
        );

        $this->inputsHelper()->select('turno', $options);

        $options = array(
            'required'    => $required,
            'label'       => 'Ano de Conclusão (egresso)',
            'placeholder' => 'Ano',
            'value'       => $this->egresso,
            'max_length'  => 4,
            'size'        => 6,
        );

        $this->inputsHelper()->integer('egresso', $options);

        $this->campoCheck('guarda_mirim', 'Guarda Mirim', $this->guarda_mirim, '', FALSE, FALSE);

        $this->campoCheck('encaminhamento', 'Encaminhado pela rede de proteção', $this->encaminhamento, '', FALSE, FALSE);

        $this->campoCheck(
            'copia_rg', 'Cópia do RG', $this->copia_rg, '', false, false
        );

        $this->campoCheck(
            'copia_cpf', 'Cópia do CPF', $this->copia_cpf, '', false, false
        );

        $this->campoCheck(
            'copia_residencia',
            'Cópia do Comprovante de Residencia',
            $this->copia_residencia, 
            '',
            false,
            false
        );

        $this->campoCheck(
            'copia_historico',
            'Cópia do Histório / Declaração',
            $this->copia_historico,
            '',
            false,
            false
        );

        $this->campoCheck(
            'copia_renda',
            'Comprovante de renda',
            $this->copia_renda,
            '',
            false,
            false
        );
    }


    protected function inputPai()
    {
        $this->addParentsInput('pai');
    }

    protected function inputMae()
    {
        $this->addParentsInput('mae', 'mãe');
    }


    protected function addParentsInput($parentType, $parentTypeLabel = '')
    {
        if (!$parentTypeLabel) {
            $parentTypeLabel = $parentType;
        }

        $parentId = $this->{$parentType . '_id'};

        // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
        //pela antiga interface do cadastro de alunos.

        $hiddenInputOptions = array('options' => array('value' => $parentId));
        $helperOptions = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

        $options = array(
            'label' => "Pessoa {$parentTypeLabel}",
            'size' => 69,
            'required' => false
        );

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
    }
}

?>
