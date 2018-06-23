<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gest„o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÌ≠
 *           <ctima@itajai.sc.gov.br>
 *
 * Este programa È software livre; vocÍ pode redistribuÌ-lo e/ou modific·-lo
 * sob os termos da LicenÁa P˙blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers„o 2 da LicenÁa, como (a seu critÈrio)
 * qualquer vers„o posterior.
 *
 * Este programa È distribuÌ≠do na expectativa de que seja ˙til, porÈm, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÌ≠cita de COMERCIABILIDADE OU
 * ADEQUA«√O A UMA FINALIDADE ESPECÕçFICA. Consulte a LicenÁa P˙blica Geral
 * do GNU para mais detalhes.
 *
 * VocÍ deve ter recebido uma cÛpia da LicenÁa P˙blica Geral do GNU junto
 * com este programa; se n„o, escreva para a Free Software Foundation, Inc., no
 * endereÁo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponÌvel desde a vers„o 1.1.0
 * @version   $Id$
 */

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

class AlunoController extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';

    protected $_titulo = 'Cadastro de aluno';

    protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

    protected $_processoAp = 578;

    protected $_deleteOption = true;

    'mae' => array(
      'label'  => 'M„e',
      'help'   => '',
    ),

    'responsavel' => array(
      'label'  => 'Respons·vel',
      'help'   => '',
    ),

    'transporte' => array(
      'label'  => 'Transporte p˙blico',
      'help'   => '',
    ),

    'id' => array(
      'label'  => 'CÛdigo aluno',
      'help'   => '',
    ),

    'deficiencias' => array(
      'label'  => 'DeficiÍncias / habilidades especiais',
      'help'   => '',
      ),

      /* *******************
         ** Dados mÈdicos **
         ******************* */

        'certidao_nascimento' => array(
            'label' => 'Certid√£o de Nascimento',
            'help' => '',
        ),

        'certidao_casamento' => array(
            'label' => 'Certid√£o de Casamento',
            'help' => '',
        ),

      'grupo_sanguineo' => array('label' => 'Grupo sanguÌ≠neo'),

        'mae' => array(
            'label' => 'M√£e',
            'help' => '',
        ),

      'alergia_medicamento' => array('label' => 'O aluno È alÈrgico a algum medicamento?'),


      'alergia_alimento' => array('label' => 'O aluno È alÈrgico a algum alimento?'),

        'transporte' => array(
            'label' => 'Transporte escolar p√∫blico',
            'help' => '',
        ),

      'doenca_congenita' => array('label' => 'O aluno possui doenÁa congÍnita?'),

        'aluno_inep_id' => array(
            'label' => 'C√≥digo INEP',
            'help' => '',
        ),

      'fumante' => array('label' => 'O aluno È fumante?'),

      'doenca_caxumba' => array('label' => 'O aluno j· contraiu caxumba?'),

      'doenca_sarampo' => array('label' => 'O aluno j· contraiu sarampo?'),

      'doenca_rubeola' => array('label' => 'O aluno j· contraiu rubeola?'),

      'doenca_catapora' => array('label' => 'O aluno j· contraiu catapora?'),

      'doenca_escarlatina' => array('label' => 'O aluno j· contraiu escarlatina?'),

      'doenca_coqueluche' => array('label' => 'O aluno j· contraiu coqueluche?'),

      'doenca_outras' => array('label' => 'Outras doenÁas que o aluno j· contraiu'),

      'epiletico' => array('label' => 'O aluno È epilÈtico?'),

      'epiletico_tratamento' => array('label' => 'Est· em tratamento?'),

      'hemofilico' => array('label' => 'O aluno È hemofÌ≠lico?'),

      'hipertenso' => array('label' => 'O aluno tem hipertens„o?'),

      'asmatico' => array('label' => 'O aluno È asm·tico?'),

      'diabetico' => array('label' => 'O aluno È diabÈtico?'),

        'doenca_congenita' => array('label' => 'O aluno possui doen√ßa cong√™nita?'),

      'tratamento_medico' => array('label' => 'O aluno faz algum tratamento mÈdico?'),

        'fumante' => array('label' => 'O aluno √© fumante?'),

      'medicacao_especifica' => array('label' => 'O aluno est· ingerindo medicaÁ„o especÌ≠fica?'),

        'doenca_sarampo' => array('label' => 'O aluno j√° contraiu sarampo?'),

      'acomp_medico_psicologico' => array('label' => 'O aluno tem acompanhamento mÈdico ou psicolÛgico?'),

        'doenca_catapora' => array('label' => 'O aluno j√° contraiu catapora?'),

      'restricao_atividade_fisica' => array('label' => 'O aluno tem restriÁ„o a alguma atividade fÌ≠sica?'),
      
      'desc_restricao_atividade_fisica' => array('label' => 'Qual?'),

        'doenca_coqueluche' => array('label' => 'O aluno j√° contraiu coqueluche?'),

        'doenca_outras' => array('label' => 'Outras doen√ßas que o aluno j√° contraiu'),

      'plano_saude' => array('label' => 'O aluno possui algum plano de sa˙de?'),

        'epiletico_tratamento' => array('label' => 'Est√° em tratamento?'),

        'hemofilico' => array('label' => 'O aluno √© hemof√≠lico?'),

      'hospital_clinica_endereco' => array('label' => 'EndereÁo'),

        'asmatico' => array('label' => 'O aluno √© asm√°tico?'),

        'diabetico' => array('label' => 'O aluno √© diab√©tico?'),

        'insulina' => array('label' => 'Depende de insulina?'),

        'tratamento_medico' => array('label' => 'O aluno faz algum tratamento m√©dico?'),

        'desc_tratamento_medico' => array('label' => 'Qual?'),

        'medicacao_especifica' => array('label' => 'O aluno est√° ingerindo medica√ß√£o espec√≠fica?'),

      'recebeu_uniforme' => array('label' => 'Recebeu uniforme?'),

        'acomp_medico_psicologico' => array('label' => 'O aluno tem acompanhamento m√©dico ou psicol√≥gico?'),

        'desc_acomp_medico_psicologico' => array('label' => 'Motivo?'),

        'restricao_atividade_fisica' => array('label' => 'O aluno tem restri√ß√£o a alguma atividade f√≠sica?'),

    /************
      MORADIA    
    ************/

        'copa' => array('label' => 'N√∫mero de copas'),

        'banheiro' => array('label' => 'N√∫mero de banheiros'),

        'garagem' => array('label' => 'N√∫mero de garagens'),

      'moradia_situacao' => array('label' => 'SituaÁ„o'),

      'quartos' => array('label' => 'N˙mero de quartos'),

      'sala' => array('label' => 'N˙mero de salas'),

      'copa' => array('label' => 'N˙mero de copas'),

      'banheiro' => array('label' => 'N˙mero de banheiros'),

      'garagem' => array('label' => 'N˙mero de garagens'),

      'empregada_domestica' => array('label' => 'Possui empregada domÈstica?'),

      'automovel' => array('label' => 'Possui automÛvel?'),

        'video_dvd' => array('label' => 'Possui v√≠deo/DVD?'),

        'televisao' => array('label' => 'Possui televis√£o?'),

        'celular' => array('label' => 'Possui celular?'),

      'fogao' => array('label' => 'Possui fog„o?'),

      'maquina_lavar' => array('label' => 'Possui m·quina de lavar?'),

        'renda' => array('label' => 'Renda familiar em R$'),

      'video_dvd' => array('label' => 'Possui vÌ≠deo/DVD?'),

      'televisao' => array('label' => 'Possui televis„o?'),

        'energia' => array('label' => 'Possui energia?'),

        'esgoto' => array('label' => 'Possui esgoto?'),

        'fossa' => array('label' => 'Possui fossa?'),

        'lixo' => array('label' => 'Possui lixo?'),

      'agua_encanada' => array('label' => 'Possui ·gua encanada?'),

      'poco' => array('label' => 'Possui poÁo?'),

        'transporte_rota' => array(
            'label' => 'Rota',
            'help' => '',
        ),

        'transporte_ponto' => array(
            'label' => 'Ponto de embarque',
            'help' => '',
        ),

      'fossa' => array('label' => 'Possui fossa?'),

      'lixo' => array('label' => 'Possui lixo?'),

  );


  protected function _preConstruct()
  {
    $nomeMenu = $this->getRequest()->id == null ? "Cadastrar" : "Editar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "InÌcio",
         "educar_index.php"                  => "Trilha Jovem Iguassu - Escola",
         ""        => "$nomeMenu aluno"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {
    $this->url_cancelar = '/intranet/educar_aluno_lst.php';

    // cÛdigo aluno
    $options = array('label'    => $this->_getLabel('id'), 'disabled' => true,
                     'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('id', $options);

    // nome
    $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
    $this->inputsHelper()->simpleSearchPessoa('nome', $options);

    // data nascimento
    $options = array('label' => 'Data de nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => '');
    $this->inputsHelper()->date('data_nascimento', $options);

    // rg
    $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
    $this->inputsHelper()->integer('rg', $options);

    $this->inputPai();
    $this->inputMae();

/*    // pai
    $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('pai', $options);


    // m„e
    $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
    $this->inputsHelper()->text('mae', $options);*/


    // respons·vel

    // tipo

    $label = Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel'));

    /*$tiposResponsavel = array(null           => $label,
                              'pai'          => 'Pai',
                              'mae'          => 'M&atilde;e',
                              'outra_pessoa' => 'Outra pessoa');*/
    $tiposResponsavel = array(null           => 'Informe uma Pessoa primeiro');

    $options = array('label'     => $this->_getLabel('responsavel'),
                     'resources' => $tiposResponsavel,
                     'required'  => true,
                     'inline'    => true);

    $this->inputsHelper()->select('tipo_responsavel', $options);


    // nome
    $helperOptions = array('objectName' => 'responsavel');
    $options       = array('label' => '', 'size' => 50, 'required' => true);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);

        'transporte_observacao' => array(
            'label' => 'Observa√ß√µes',
            'help' => '',
        )
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
        $this->url_cancelar = '/intranet/educar_aluno_lst.php';

        $configuracoes = new clsPmieducarConfiguracoesGerais();
        $configuracoes = $configuracoes->detalhe();

        $labels_botucatu = $GLOBALS['coreExt']['Config']->app->mostrar_aplicacao == 'botucatu';

        if ($configuracoes["justificativa_falta_documentacao_obrigatorio"]) {
            $this->inputsHelper()->hidden('justificativa_falta_documentacao_obrigatorio');
        }

        $cod_aluno = $_GET['id'];

        if ($cod_aluno or $_GET['person']) {
            if ($_GET['person']) {
                $this->cod_pessoa_fj = $_GET['person'];
                $this->inputsHelper()->hidden('person', array('value' => $this->cod_pessoa_fj));
            } else {
                $db = new clsBanco();
                $this->cod_pessoa_fj = $db->CampoUnico("select ref_idpes from pmieducar.aluno where cod_aluno = '$cod_aluno'");
            }

            $documentos = new clsDocumento();
            $documentos->idpes = $this->cod_pessoa_fj;
            $documentos = $documentos->detalhe();
        }

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


        // c√≥digo aluno
        $options = array('label' => _cl('aluno.detalhe.codigo_aluno'), 'disabled' => true, 'required' => false, 'size' => 25);
        $this->inputsHelper()->integer('id', $options);

        // c√≥digo aluno inep
        $options = array('label' => $this->_getLabel('aluno_inep_id'), 'required' => false, 'size' => 25, 'max_length' => 12);

        if (!$configuracoes['mostrar_codigo_inep_aluno']) {
            $this->inputsHelper()->hidden('aluno_inep_id', array('value' => null));
        } else {
            $this->inputsHelper()->integer('aluno_inep_id', $options);
        }

        // c√≥digo aluno rede estadual
        $this->campoRA(
            "aluno_estado_id",
            Portabilis_String_Utils::toLatin1("C√≥digo rede estadual do aluno (RA)"),
            $this->aluno_estado_id,
            FALSE
        );

        // c√≥digo aluno sistema
        if ($GLOBALS['coreExt']['Config']->app->alunos->mostrar_codigo_sistema) {
            $options = array(
                'label' => Portabilis_String_Utils::toLatin1($GLOBALS['coreExt']['Config']->app->alunos->codigo_sistema),
                'required' => false,
                'size' => 25,
                'max_length' => 30
            );
            $this->inputsHelper()->text('codigo_sistema', $options);
        }

        // nome
        $options = array('label' => $this->_getLabel('pessoa'), 'size' => 68);
        $this->inputsHelper()->simpleSearchPessoa('nome', $options);

        // data nascimento
        $options = array('label' => 'Data de nascimento', 'disabled' => true, 'required' => false, 'size' => 25, 'placeholder' => '');
        $this->inputsHelper()->date('data_nascimento', $options);

        // rg
        // $options = array('label' => $this->_getLabel('rg'), 'disabled' => true, 'required' => false, 'size' => 25);
        // $this->inputsHelper()->integer('rg', $options);

        $options = array(
            'required' => $required,
            'label' => 'RG / Data emiss√£o',
            'placeholder' => 'Documento identidade',
            'value' => $documentos['rg'],
            'max_length' => 25,
            'size' => 27,
            'inline' => true
        );

        $this->inputsHelper()->text('rg', $options);

        // data emiss√£o rg
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Data emiss\u00e3o',
            'value' => $documentos['data_exp_rg'],
            'size' => 19
        );

        $this->inputsHelper()->date('data_emissao_rg', $options);

        $selectOptions = array( null => '√ìrg√£o emissor' );
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


        // uf emiss√£o rg

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

        // justificativa_falta_documentacao
        $resources = array(null => 'Selecione',
            1 => Portabilis_String_Utils::toLatin1('Aluno n√£o possui documenta√ß√£o'),
            2 => Portabilis_String_Utils::toLatin1('Escola n√£o possui informa√ß√£o'));

        $options = array('label' => $this->_getLabel('justificativa_falta_documentacao'),
            'resources' => $resources,
            'required' => false,
            'disabled' => true);

        $this->inputsHelper()->select('justificativa_falta_documentacao', $options);

        // tipo de certidao civil
        $escolha_certidao = Portabilis_String_Utils::toLatin1('Tipo certid√£o civil');
        $selectOptions = array(
            null => $escolha_certidao,
            'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
            91 => 'Nascimento (antigo formato)',
            'certidao_casamento_novo_formato' => 'Casamento (novo formato)',
            92 => 'Casamento (antigo formato)'
        );


        // caso certidao nascimento novo formato tenha sido informado,
        // considera este o tipo da certid√£o
        if (!empty($documentos['certidao_nascimento'])) {
            $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
        } else if (!empty($documentos['certidao_casamento'])) {
            $tipoCertidaoCivil = 'certidao_casamento_novo_formato';
        } else {
            $tipoCertidaoCivil = $documentos['tipo_cert_civil'];
        }

        $options = array(
            'required' => false,
            'label' => 'Tipo certid√£o civil',
            'value' => $tipoCertidaoCivil,
            'resources' => $selectOptions,
            'inline' => true
        );

        $this->inputsHelper()->select('tipo_certidao_civil', $options);

        // termo certidao civil
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Termo',
            'value' => $documentos['num_termo'],
            'max_length' => 8,
            'inline' => true
        );

        $this->inputsHelper()->integer('termo_certidao_civil', $options);

        // livro certidao civil
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Livro',
            'value' => $documentos['num_livro'],
            'max_length' => 8,
            'size' => 15,
            'inline' => true
        );

        $this->inputsHelper()->text('livro_certidao_civil', $options);

        // folha certidao civil
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Folha',
            'value' => $documentos['num_folha'],
            'max_length' => 4,
            'inline' => true
        );

        $this->inputsHelper()->integer('folha_certidao_civil', $options);

        // certidao nascimento (novo padr√£o)
        $placeholderCertidao = Portabilis_String_Utils::toLatin1('Certid√£o nascimento');
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_nascimento'],
            'max_length' => 32,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('certidao_nascimento', $options);

        // certidao casamento (novo padr√£o)
        $placeholderCertidao = Portabilis_String_Utils::toLatin1('Certid√£o casamento');
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderCertidao,
            'value' => $documentos['certidao_casamento'],
            'max_length' => 32,
            'size' => 50,
        );

        $this->inputsHelper()->text('certidao_casamento', $options);

        // uf emiss√£o certid√£o civil
        $options = array(
            'required' => false,
            'label' => 'Estado emiss√£o / Data emiss√£o',
            'value' => $documentos['sigla_uf_cert_civil'],
            'inline' => true
        );

        $helperOptions = array(
            'attrName' => 'uf_emissao_certidao_civil'
        );

        $this->inputsHelper()->uf($options, $helperOptions);

        // data emiss√£o certid√£o civil
        $placeholderEmissao = Portabilis_String_Utils::toLatin1('Data emiss√£o');
        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => $placeholderEmissao,
            'value' => $documentos['data_emissao_cert_civil'],
            'inline' => true
        );

        $this->inputsHelper()->date('data_emissao_certidao_civil', $options);

        $options = array(
            'label' => '',
            'required' => false
          );

          $helperOptions = array(
            'objectName' => 'cartorio_cert_civil_inep',
            'hiddenInputOptions' => array(
              'options' => array('value' => $documentos['cartorio_cert_civil_inep'])
            )
          );

          $this->inputsHelper()->simpleSearchCartorioInep(null, $options, $helperOptions);

        // cart√≥rio emiss√£o certid√£o civil
        $labelCartorio = Portabilis_String_Utils::toLatin1('Cart√≥rio emiss√£o');
        $options = array(
            'required' => false,
            'label' => $labelCartorio,
            'value' => $documentos['cartorio_cert_civil'],
            'cols' => 45,
            'max_length' => 200,
        );

        $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);

        // Passaporte
        $labelPassaporte = Portabilis_String_Utils::toLatin1('Passaporte');
        $options = array(
            'required' => false,
            'label' => $labelPassaporte,
            'value' => $documentos['passaporte'],
            'cols' => 45,
            'max_length' => 20
        );

        $this->inputsHelper()->text('passaporte', $options);

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

        //tres
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_tres', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_tres', $options);

        //quatro
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',
            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_quatro', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_quatro', $options);

        //cinco
        $options = array(
            'required' => false,
            'label' => 'Nome autorizado a buscar o aluno / Parentesco',
            'placeholder' => 'Nome autorizado',

            'max_length' => 150,
            'size' => 50,
            'inline' => true
        );

        $this->inputsHelper()->text('autorizado_cinco', $options);

        $options = array(
            'required' => false,
            'label' => '',
            'placeholder' => 'Parentesco',
            'max_length' => 150,
            'size' => 15
        );

        $this->inputsHelper()->text('parentesco_cinco', $options);

        $this->inputPai();

        // m√£e
        $this->inputMae();
        /*    // pai
            $options = array('label' => $this->_getLabel('pai'), 'disabled' => true, 'required' => false, 'size' => 68);
            $this->inputsHelper()->text('pai', $options);


            // m√£e
            $options = array('label' => $this->_getLabel('mae'), 'disabled' => true, 'required' => false, 'size' => 68);
            $this->inputsHelper()->text('mae', $options);*/

        // respons√°vel

        // tipo

        $label = Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel'));

        /*$tiposResponsavel = array(null           => $label,
                                  'pai'          => 'Pai',
                                  'mae'          => 'M&atilde;e',
                                  'outra_pessoa' => 'Outra pessoa');*/
        $tiposResponsavel = array(null => 'Informe uma Pessoa primeiro');
        $options = array(
            'label' => Portabilis_String_Utils::toLatin1('Respons√°vel'),
            'resources' => $tiposResponsavel,
            'required' => true,
            'inline' => true
        );

        $this->inputsHelper()->select('tipo_responsavel', $options);

        // nome
        $helperOptions = array('objectName' => 'responsavel');
        $options = array('label' => '', 'size' => 50, 'required' => true);

        $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);

        // transporte publico

        $tiposTransporte = array(
            null => 'Selecione',
            'nenhum' => 'N&atilde;o utiliza',
            'municipal' => 'Municipal',
            'estadual' => 'Estadual'
        );

        $options = array(
            'label' => $this->_getLabel('transporte'),
            'resources' => $tiposTransporte,
            'required' => true
        );

        $this->inputsHelper()->select('tipo_transporte', $options);

        $veiculos = array(null => 'Nenhum',
            1 => Portabilis_String_Utils::toLatin1('Rodovi√°rio - Vans/Kombis'),
            2 => Portabilis_String_Utils::toLatin1('Rodovi√°rio - Micro√¥nibus'),
            3 => Portabilis_String_Utils::toLatin1('Rodovi√°rio - √înibus'),
            4 => Portabilis_String_Utils::toLatin1('Rodovi√°rio - Bicicleta'),
            5 => Portabilis_String_Utils::toLatin1('Rodovi√°rio - Tra√ß√£o animal'),
            6 => Portabilis_String_Utils::toLatin1('Rodovi√°rio - Outro'),
            7 => Portabilis_String_Utils::toLatin1('Aquavi√°rio/Embarca√ß√£o - Capacidade de at√© 5 alunos'),
            8 => Portabilis_String_Utils::toLatin1('Aquavi√°rio/Embarca√ß√£o - Capacidade entre 5 a 15 alunos'),
            9 => Portabilis_String_Utils::toLatin1('Aquavi√°rio/Embarca√ß√£o - Capacidade entre 15 a 35 alunos'),
            10 => Portabilis_String_Utils::toLatin1('Aquavi√°rio/Embarca√ß√£o - Capacidade acima de 35 alunos'),
            11 => Portabilis_String_Utils::toLatin1('Ferrovi√°rio - Trem/Metr√¥'));

        $options = array(
            'label' => 'Ve&iacute;culo utilizado',
            'resources' => $veiculos,
            'required' => false
        );

        $this->inputsHelper()->select('veiculo_transporte_escolar', $options);

        if ($this->getClsPermissoes()->permissao_cadastra(21240, $this->getOption('id_usuario'), 7)) {
            // Cria lista de rotas
            $obj_rota = new clsModulesRotaTransporteEscolar();
            $obj_rota->setOrderBy(' descricao asc ');
            $lista_rota = $obj_rota->lista();
            $rota_resources = array("" => "Selecione uma rota");
            foreach ($lista_rota as $reg) {
                $rota_resources["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']}";
            }

            // Transporte Rota
            $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('transporte_rota')), 'required' => false, 'resources' => $rota_resources);
            $this->inputsHelper()->select('transporte_rota', $options);

    // religi„o
    $this->inputsHelper()->religiao(array('required' => false, 'label' => Portabilis_String_Utils::toLatin1('Religi„o')));

    // beneficio
    $this->inputsHelper()->beneficio(array('required' => false, 'label' => Portabilis_String_Utils::toLatin1('BenefÌ≠cio')));

            // Transporte observacoes
            $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('transporte_observacao')), 'required' => false, 'size' => 50, 'max_length' => 255);
            $this->inputsHelper()->textArea('transporte_observacao', $options);
        }

    // DeficiÍncias / habilidades especiais
    $helperOptions = array('objectName' => 'deficiencias');
    $options       = array('label' => $this->_getLabel('deficiencias'), 'size' => 50, 'required' => false,
                           'options' => array('value' => null));

        // Benef√≠cios
        $helperOptions = array('objectName' => 'beneficios');
        $options = array(
            'label' => Portabilis_String_Utils::toLatin1('Benef√≠cios'),
            'size' => 250,
            'required' => false,
            'options' => array('value' => null)
        );

        $this->inputsHelper()->multipleSearchBeneficios('', $options, $helperOptions);

        // Defici√™ncias / habilidades especiais
        $helperOptions = array('objectName' => 'deficiencias');
        $options = array(
            'label' => $this->_getLabel('deficiencias'),
            'size' => 50,
            'required' => false,
            'options' => array('value' => null)
        );

    /* *************************************
       ** Dados para a Aba 'Ficha mÈdica' **
       ************************************* */

        $this->campoArquivo('documento', Portabilis_String_Utils::toLatin1($this->_getLabel('documento')), $this->documento, 40, Portabilis_String_Utils::toLatin1("<br/> <span id='span-documento' style='font-style: italic; font-size= 10px;''> S√£o aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho m√°ximo: 250KB</span>", array('escape' => false)));

        $this->inputsHelper()->hidden('url_documento');

        $this->campoArquivo('laudo_medico', Portabilis_String_Utils::toLatin1($this->_getLabel('laudo_medico')), $this->laudo_medico, 40, Portabilis_String_Utils::toLatin1("<br/> <span id='span-laudo_medico' style='font-style: italic; font-size= 10px;''> S√£o aceitos arquivos nos formatos jpg, png, pdf e gif. Tamanho m√°ximo: 250KB</span>", array('escape' => false)));

        $this->inputsHelper()->hidden('url_laudo_medico');

        if ($GLOBALS['coreExt']['Config']->app->alunos->laudo_medico_obrigatorio == 1) {
            $this->inputsHelper()->hidden('url_laudo_medico_obrigatorio');
        }

        /* *************************************
           ** Dados para a Aba 'Ficha m√©dica' **
           ************************************* */

        // Hist√≥rico de altura e peso

        $this->campoTabelaInicio("historico_altura_peso", "Hist√≥rico de altura e peso", array('Data', 'Altura (m)', 'Peso (kg)'));

        $this->inputsHelper()->date('data_historico');

        $this->inputsHelper()->numeric('historico_altura');

        $this->inputsHelper()->numeric('historico_peso');

        $this->campoTabelaFim();

        // Fim hist√≥rico de altura e peso

        // altura
        $options = array('label' => $this->_getLabel('altura'), 'size' => 5, 'max_length' => 4, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->numeric('altura', $options);

        // peso
        $options = array('label' => $this->_getLabel('peso'), 'size' => 5, 'max_length' => 6, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->numeric('peso', $options);

        // grupo_sanguineo
        $options = array('label' => $this->_getLabel('grupo_sanguineo'), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('grupo_sanguineo', $options);

        // fator_rh
        $options = array('label' => $this->_getLabel('fator_rh'), 'size' => 5, 'max_length' => 1, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('fator_rh', $options);

        // sus
        $options = array('label' => $this->_getLabel('sus'), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('sus', $options);

        // alergia_medicamento
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('alergia_medicamento')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('alergia_medicamento', $options);

        // desc_alergia_medicamento
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_alergia_medicamento')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_alergia_medicamento', $options);

        // alergia_alimento
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('alergia_alimento')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('alergia_alimento', $options);

        // desc_alergia_alimento
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_alergia_alimento')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_alergia_alimento', $options);

        // doenca_congenita
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_congenita')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_congenita', $options);

        // desc_doenca_congenita
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_doenca_congenita')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_doenca_congenita', $options);

        // fumante
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fumante')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fumante', $options);

        // doenca_caxumba
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_caxumba')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_caxumba', $options);

        // doenca_sarampo
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_sarampo')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_sarampo', $options);

        // doenca_rubeola
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_rubeola')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_rubeola', $options);

        // doenca_catapora
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_catapora')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_catapora', $options);

        // doenca_escarlatina
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_escarlatina')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_escarlatina', $options);

        // doenca_coqueluche
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_coqueluche')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('doenca_coqueluche', $options);

        // doenca_outras
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('doenca_outras')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('doenca_outras', $options);

        // epiletico
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('epiletico')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('epiletico', $options);

        // epiletico_tratamento
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('epiletico_tratamento')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('epiletico_tratamento', $options);

        // hemofilico
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hemofilico')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('hemofilico', $options);

        // hipertenso
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hipertenso')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('hipertenso', $options);

        // asmatico
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('asmatico')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('asmatico', $options);

        // diabetico
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('diabetico')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('diabetico', $options);

    $this->campoRotulo('tit_dados_hospital',Portabilis_String_Utils::toLatin1('Em caso de emergÍncia, levar para hospital ou clÌ≠nica')); 

        // tratamento_medico
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('tratamento_medico')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('tratamento_medico', $options);

        // desc_tratamento_medico
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_tratamento_medico')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_tratamento_medico', $options);

        // medicacao_especifica
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('medicacao_especifica')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('medicacao_especifica', $options);

        // desc_medicacao_especifica
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_medicacao_especifica')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_medicacao_especifica', $options);

    $this->campoRotulo('tit_dados_responsavel',Portabilis_String_Utils::toLatin1('Em caso de emergÍncia, caso n„o seja encontrado pais ou respons·veis, avisar')); 

        // plano_saude
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('plano_saude')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('plano_saude', $options);

        // desc_plano_saude
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('desc_plano_saude')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('desc_plano_saude', $options);

        $this->campoRotulo('tit_dados_hospital', Portabilis_String_Utils::toLatin1('Em caso de emerg√™ncia, levar para hospital ou cl√≠nica'));

        // hospital_clinica
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica')), 'size' => 50, 'max_length' => 100, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('hospital_clinica', $options);

        // hospital_clinica_endereco
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_endereco')), 'size' => 50, 'max_length' => 50, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('hospital_clinica_endereco', $options);

        // hospital_clinica_telefone
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('hospital_clinica_telefone')), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('hospital_clinica_telefone', $options);

        $this->campoRotulo('tit_dados_responsavel', Portabilis_String_Utils::toLatin1('Em caso de emerg√™ncia, caso n√£o seja encontrado pais ou respons√°veis, avisar'));

        // responsavel
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel')), 'size' => 50, 'max_length' => 50, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('responsavel', $options);

        // responsavel_parentesco
        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('responsavel_parentesco')), 'size' => 20, 'max_length' => 20, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->text('responsavel_parentesco', $options);

    $moradias = array(null   => 'Selecione',
                        'A'  => 'Apartamento',
                        'C'  => 'Casa',
                        'O'  => 'Outro'); 

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('automovel')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('automovel', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('motocicleta')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('motocicleta', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('computador')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('computador', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('geladeira')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('geladeira', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fogao')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fogao', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('maquina_lavar')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('maquina_lavar', $options);

    $situacoes = array( null => 'Selecione',
                        '1' => 'Alugado',
                        '2' => Portabilis_String_Utils::toLatin1('PrÛprio'),
                        '3' => 'Cedido',
                        '4' => 'Financiado',
                        '5' => 'Outros');

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('video_dvd')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('video_dvd', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('televisao')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('televisao', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('telefone')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('telefone', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('celular')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('celular', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('quant_pessoas')), 'size' => 5, 'max_length' => 2, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->integer('quant_pessoas', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('renda')), 'size' => 5, 'max_length' => 10, 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->numeric('renda', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('agua_encanada')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('agua_encanada', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('poco')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('poco', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('energia')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('energia', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('esgoto')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('esgoto', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('fossa')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('fossa', $options);

        $options = array('label' => Portabilis_String_Utils::toLatin1($this->_getLabel('lixo')), 'required' => false, 'placeholder' => '');
        $this->inputsHelper()->checkbox('lixo', $options);

        $recursosProvaInep = array(
            1 => 'Aux√≠lio ledor',
            2 => 'Aux√≠lio transcri√ß√£o',
            3 => 'Guia-int√©rprete',
            4 => 'Int√©rprete de LIBRAS',
            5 => 'Leitura labial',
            6 => 'Prova ampliada (Fonte 16)',
            7 => 'Prova ampliada (Fonte 20)',
            8 => 'Prova ampliada (Fonte 24)',
            9 => 'Prova em Braille'
        );
        $helperOptions = array('objectName'  => 'recursos_prova_inep');
        $options = array(
            'label' => 'Recursos prova INEP',
            'size' => 50,
            'required' => false,
            'options' => array(
                'values' => $this->recursos_prova_inep,
                'all_values' => $recursosProvaInep));
        $this->inputsHelper()->multipleSearchCustom('_', $options, $helperOptions);

        $selectOptions = array(
            3 => 'N√£o recebe',
            1 => 'Em hospital',
            2 => 'Em domic√≠lio'
        );

        $options = array(
            'required' => false,
            'label' => $this->_getLabel('recebe_escolarizacao_em_outro_espaco'),
            'resources' => $selectOptions
        );

        $this->inputsHelper()->select('recebe_escolarizacao_em_outro_espaco', $options);

        // Projetos
        $this->campoTabelaInicio("projetos", "Projetos", array("Projeto", Portabilis_String_Utils::toLatin1("Data inclus√£o"), "Data desligamento", 'Turno'));

        $this->inputsHelper()->text('projeto_cod_projeto', array('required' => false));

        $this->inputsHelper()->date('projeto_data_inclusao', array('required' => false));

        $this->inputsHelper()->date('projeto_data_desligamento', array('required' => false));

        $this->inputsHelper()->select('projeto_turno', array('required' => false, 'resources' => array('' => "Selecione", 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral')));

        $this->campoTabelaFim();

        // Fim projetos

        $this->inputsHelper()->simpleSearchMunicipio('pessoa-aluno', array('required' => false, 'size' => 57), array('objectName' => 'naturalidade_aluno'));

        $enderecamentoObrigatorio = false;
        $desativarCamposDefinidosViaCep = true;

    $options       = array('label' => Portabilis_String_Utils::toLatin1('MunicÌ≠pio'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $options = array('label' => Portabilis_String_Utils::toLatin1('Munic√≠pio'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $helperOptions = array(
            'objectName' => 'municipio',
            'hiddenInputOptions' => array('options' => array('value' => $this->municipio_id))
        );

        $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

    $options       = array( 'label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de LocalizaÁ„o - <b>Buscar</b>'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

        $helperOptions = array(
            'objectName' => 'distrito',
            'hiddenInputOptions' => array('options' => array('value' => $this->distrito_id))
        );

        $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

    $options = array(
      'label'       => 'Bairro / Zona de LocalizaÁ„o - <b>Cadastrar</b>',
      'placeholder' => 'Bairro',
      'value'       => $this->bairro,
      'max_length'  => 40,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

        $options = array('label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localiza√ß√£o - <b>Buscar</b>'), 'required' => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    // zona localizaÁ„o

        $options = array(
            'label' => 'Bairro / Zona de Localiza√ß√£o - <b>Cadastrar</b>',
            'placeholder' => 'Bairro',
            'value' => $this->bairro,
            'max_length' => 40,
            'disabled' => $desativarCamposDefinidosViaCep,
            'inline' => true,
            'required' => $enderecamentoObrigatorio
        );

    $options = array(
      'label'       => '',
      'placeholder' => 'Zona localizaÁ„o',
      'value'       => $this->zona_localizacao,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'resources'   => $zonas,
      'required'    => $enderecamentoObrigatorio
    );

        // zona localiza√ß√£o
        $zonas = App_Model_ZonaLocalizacao::getInstance();
        $zonas = $zonas->getEnums();
        $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza&ccedil;&atilde;o', $zonas);

        $options = array(
            'label' => '',
            'placeholder' => 'Zona localiza√ß√£o',
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
            'label' => 'N√∫mero / Letra',
            'placeholder' => Portabilis_String_Utils::toLatin1('N√∫mero'),
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
            'label' => 'N¬∫ apartamento / Bloco / Andar',
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
        $obrigarCamposCenso = FALSE;
        if ($instituicao && isset($instituicao['obrigar_campos_censo'])) {
            $obrigarCamposCenso = dbBool($instituicao['obrigar_campos_censo']);
        }
        $this->CampoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);

        $racas         = new clsCadastroRaca();
        $racas         = $racas->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);

        foreach ($racas as $raca) {
            $selectOptions[$raca['cod_raca']] = $raca['nm_raca'];
        }

        $selectOptions = array(null => 'Selecione') + Portabilis_Array_Utils::sortByValue($selectOptions);

        $this->campoLista('cor_raca', 'Ra√ßa', $selectOptions, $this->cod_raca, '', FALSE, '', '', '', $obrigarCamposCenso);

        $zonas = array(
            '' => 'Selecione',
            1  => 'Urbana',
            2  => 'Rural',
        );

        $options = array(
          'label'       => 'Zona Localiza√ß√£o',
          'value'       => $this->zona_localizacao_censo,
          'resources'   => $zonas,
          'required'    => $obrigarCamposCenso,
        );

        $this->inputsHelper()->select('zona_localizacao_censo', $options);

        $tiposNacionalidade = array(
            '1'  => 'Brasileiro',
            '2'  => 'Naturalizado brasileiro',
            '3'  => 'Estrangeiro'
        );

        $options = array(
            'label'       => 'Nacionalidade',
            'resources'   => $tiposNacionalidade,
            'required'    => $obrigarCamposCenso,
            'inline'      => TRUE,
            'value'       => $this->tipo_nacionalidade
        );

        $this->inputsHelper()->select('tipo_nacionalidade', $options);

        // pais origem

        $options = array(
          'label'       => '',
          'placeholder' => 'Informe o nome do pais',
          'required'    => $obrigarCamposCenso
        );

        $hiddenInputOptions = array(
            'options' => array(
                'value' => $this->pais_origem_id
            )
        );

        $helperOptions = array(
          'objectName'         => 'pais_origem',
          'hiddenInputOptions' => $hiddenInputOptions
        );
        $this->inputsHelper()->simpleSearchPais('nome', $options, $helperOptions);
    }


    protected function inputPai()
    {
        $this->addParentsInput('pai');
    }

    protected function inputMae()
    {
        $this->addParentsInput('mae', 'm√£e');
    }


    protected function addParentsInput($parentType, $parentTypeLabel = '')
    {
        if (!$parentTypeLabel) {
            $parentTypeLabel = $parentType;
        }

        $parentId = $this->{$parentType . '_id'};

        // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
        //pela antiga interface do cadastro de alunos.

    $options = array(
      'required'    => false,
      'label'       => 'N˙mero / Letra',
      'placeholder' => Portabilis_String_Utils::toLatin1('N˙mero'),
      'value'       => '',
      'max_length'  => 6,
      'inline'      => true
    );

    $this->inputsHelper()->integer('numero', $options);


    // letra

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Letra',
      'value'       => $this->letra,
      'max_length'  => 1,
      'size'        => 15
    );

    $this->inputsHelper()->text('letra', $options);


    // apartamento

    $options = array(
      'required'    => false,
      'label'       => 'N∫ apartamento / Bloco / Andar',
      'placeholder' =>  'Apartamento',
      'value'       => $this->apartamento,
      'max_length'  => 6,
      'inline'      => true
    );

    $this->inputsHelper()->integer('apartamento', $options);


    // bloco

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Bloco',
      'value'       => $this->bloco,
      'max_length'  => 20,
      'size'        => 15,
      'inline'      => true
    );

    $this->inputsHelper()->text('bloco', $options);


    // andar

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Andar',
      'value'       => $this->andar,
      'max_length'  => 2
    );

    $this->inputsHelper()->integer('andar', $options);

    $script = '/modules/Cadastro/Assets/Javascripts/Endereco.js';

    Portabilis_View_Helper_Application::loadJavascript($this, $script);

    $this->loadResourceAssets($this->getDispatcher());

  }

  protected function addParentsInput($parentType, $parentTypeLabel = '') {
    if (! $parentTypeLabel)
      $parentTypeLabel = $parentType;


    $parentId = $this->{$parentType . '_id'};


    // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
    //pela antiga interface do cadastro de alunos.



    $hiddenInputOptions = array('options' => array('value' => $parentId));
    $helperOptions      = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

    $options            = array('label'      => 'Pessoa ' . $parentTypeLabel,
                                'size'       => 69,
                                'required'   => false);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
  }

  protected function inputPai() {
    $this->addParentsInput('pai');
  }

  protected function inputMae() {
    $this->addParentsInput('mae', 'm„e');
  }
}
?>
