<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';

require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/modules/clsModulesPessoaTransporte.inc.php';
require_once 'include/modules/clsModulesMotorista.inc.php';
require_once 'image_check.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/Database.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Pessoas Físicas - Cadastro');
    $this->processoAp = 43;
    $this->addEstilo('localizacaoSistema');
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $cod_pessoa_fj;
  var $nm_pessoa;
  var $id_federal;
  var $data_nasc;
  var $endereco;
  var $cep;
  var $idlog;
  var $idbai;
  var $sigla_uf;
  var $ddd_telefone_1;
  var $telefone_1;
  var $ddd_telefone_2;
  var $telefone_2;
  var $ddd_telefone_mov;
  var $telefone_mov;
  var $ddd_telefone_fax;
  var $telefone_fax;
  var $email;
  var $tipo_pessoa;
  var $sexo;
  var $busca_pessoa;
  var $complemento;
  var $apartamento;
  var $bloco;
  var $andar;
  var $numero;
  var $retorno;
  var $zona_localizacao;
  var $cor_raca;
  var $sus;
  var $nis_pis_pasep;
  var $municipio_id;
  var $bairro_id;
  var $logradouro_id;
  var $ocupacao;
  var $empresa;
  var $ddd_telefone_empresa;
  var $telefone_empresa;
  var $pessoa_contato;
  var $renda_mensal;
  var $data_admissao;
  var $zona_localizacao_censo;

  // Variáveis para controle da foto
  var $objPhoto;
  var $arquivoFoto;
  var $file_delete;

  var $caminho_det;
  var $caminho_lst;

  function Inicializar()
  {

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(43, $this->pessoa_logada, 7, 'atendidos_lst.php');

    $this->cod_pessoa_fj = @$_GET['cod_pessoa_fj'];
    $this->retorno       = 'Novo';

    if (is_numeric($this->cod_pessoa_fj)) {
      $this->retorno = 'Editar';
      $objPessoa     = new clsPessoaFisica();

      list($this->nm_pessoa, $this->id_federal, $this->data_nasc,
        $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2,
        $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov,
        $this->ddd_telefone_fax, $this->telefone_fax, $this->email,
        $this->tipo_pessoa, $this->sexo, $this->cidade,
        $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai,
        $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero,
        $this->bloco, $this->apartamento, $this->andar, $this->zona_localizacao, $this->estado_civil,
        $this->pai_id, $this->mae_id, $this->tipo_nacionalidade, $this->pais_origem, $this->naturalidade,
        $this->letra, $this->sus, $this->nis_pis_pasep, $this->ocupacao, $this->empresa, $this->ddd_telefone_empresa,
        $this->telefone_empresa, $this->pessoa_contato, $this->renda_mensal, $this->data_admissao, $this->falecido, $this->religiao_id, $this->zona_localizacao_censo
      ) =

       $objPessoa->queryRapida(
        $this->cod_pessoa_fj, 'nome', 'cpf', 'data_nasc',  'ddd_1', 'fone_1',
        'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'email',
        'tipo', 'sexo', 'cidade', 'bairro', 'logradouro', 'cep', 'idlog',
        'idbai', 'idtlog', 'sigla_uf', 'complemento', 'numero', 'bloco', 'apartamento',
        'andar', 'zona_localizacao', 'ideciv', 'idpes_pai', 'idpes_mae', 'nacionalidade',
        'idpais_estrangeiro', 'idmun_nascimento', 'letra', 'sus', 'nis_pis_pasep', 'ocupacao',
        'empresa', 'ddd_telefone_empresa', 'telefone_empresa', 'pessoa_contato', 'renda_mensal', 'data_admissao', 'falecido', 'ref_cod_religiao', 'zona_localizacao_censo'
      );

       // var_dump($objPessoa); die;
      $this->id_federal      = is_numeric($this->id_federal)   ? int2CPF($this->id_federal) : '';
      $this->cep             = is_numeric($this->cep)          ? int2Cep($this->cep) : '';
      $this->renda_mensal    = number_format($this->renda_mensal, 2, ',', '.');
      // $this->data_nasc       = $this->data_nasc                ? dataFromPgToBr($this->data_nasc) : '';
      $this->data_admissao   = $this->data_admissao            ? dataFromPgToBr($this->data_admissao) : '';


      $this->estado_civil_id = $this->estado_civil->ideciv;
      $this->pais_origem_id  = $this->pais_origem->idpais;
      $this->naturalidade_id = $this->naturalidade->idmun;

      $raca           = new clsCadastroFisicaRaca($this->cod_pessoa_fj);
      $raca           = $raca->detalhe();
      $this->cod_raca = is_array($raca) ? $raca['ref_cod_raca'] : null;
    }

    $this->fexcluir = $obj_permissoes->permissao_excluir(
          43, $this->pessoa_logada, 7, 'atendidos_lst.php'
        );

    $this->nome_url_cancelar = 'Cancelar';

    $nomeMenu = $this->retorno == "Editar" ? $this->retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_pessoas_index.php"          => "Pessoas",
         ""                                  => "$nomeMenu pessoa f&iacute;sica"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $this->retorno;
  }

  function Gerar()
  {
    $camposObrigatorios = !$GLOBALS['coreExt']['Config']->app->remove_obrigatorios_cadastro_pessoa == 1;
    $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
    $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);
    $this->url_cancelar = $this->retorno == 'Editar' ?
      'atendidos_det.php?cod_pessoa=' . $this->cod_pessoa_fj : 'atendidos_lst.php';

    $this->cod_pessoa_fj;
    $objPessoa = new clsPessoaFisica($this->cod_pessoa_fj);
    $db        = new clsBanco();

    $detalhe = $objPessoa->queryRapida(
      $this->cod_pessoa_fj, 'idpes', 'complemento','nome', 'cpf', 'data_nasc',
      'logradouro', 'idtlog', 'numero', 'apartamento','cidade','sigla_uf',
      'cep', 'ddd_1', 'fone_1', 'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov',
      'ddd_fax', 'fone_fax', 'email', 'url', 'tipo', 'sexo', 'zona_localizacao',
      'ativo', 'data_exclusao'
    );

    if(isset($this->cod_pessoa_fj) && !$detalhe['ativo'] == 1 && $this->retorno == 'Editar'){
      $getNomeUsuario = $objPessoa->getNomeUsuario();
      $detalhe['data_exclusao'] = date_format(new DateTime($detalhe['data_exclusao']), 'd/m/Y');
      $this->mensagem = "Este cadastro foi desativado em <strong>" . $detalhe['data_exclusao'] .  "</strong>,
                         pelo usuário <strong>" . $getNomeUsuario . "</strong>. <a href='javascript:ativarPessoa($this->cod_pessoa_fj);'>Reativar cadastro</a>";
    }

    $this->campoCpf('id_federal', 'CPF', $this->id_federal, FALSE);

    $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
    $this->campoTexto('nm_pessoa', 'Nome', $this->nm_pessoa, '50', '255', TRUE);

    $foto = false;
    if (is_numeric($this->cod_pessoa_fj)){
        $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
        $detalheFoto = $objFoto->detalhe();
        if(count($detalheFoto))
          $foto = $detalheFoto['caminho'];
    } else
      $foto=false;

    if ($foto){
      $this->campoRotulo('fotoAtual_','Foto atual','<img height="117" src="'.$foto.'"/>');
      $this->inputsHelper()->checkbox('file_delete', array('label' => 'Excluir a foto'));
      $this->campoArquivo('file','Trocar foto',$this->arquivoFoto,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 150KB</span>');
    }else
      $this->campoArquivo('file','Foto',$this->arquivoFoto,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho máximo: 150KB</span>');


    // ao cadastrar pessoa do pai ou mãe apartir do cadastro de outra pessoa,
    // é enviado o tipo de cadastro (pai ou mae).
    $parentType = isset($_REQUEST['parent_type']) ? $_REQUEST['parent_type'] : '';
    // Se a pessoa for pai ou mãe, não tera naturalidade obrigatoria


    $naturalidadeObrigatoria = ($parentType == '' ? true : false);

     // sexo

    $sexo = $this->sexo;

    // sugere sexo quando cadastrando o pai ou mãe

    if (! $sexo && $parentType == 'pai')
      $sexo = 'M';
    elseif (! $sexo && $parentType == 'mae')
      $sexo = 'F';


    $options = array(
      'label'       => 'Sexo / Estado civil',
      'value'     => $sexo,
      'resources' => array(
        '' => 'Sexo',
        'M' => 'Masculino',
        'F' => 'Feminino'
      ),
      'inline' => true,
      'required' => $camposObrigatorios
    );

    $this->inputsHelper()->select('sexo', $options);


    // estado civil

    $this->inputsHelper()->estadoCivil(array('label' => '', 'required' => empty($parentType) && $camposObrigatorios));


    // data nascimento

    $options = array(
      'label'       => 'Data de nascimento',
      'value'       => $this->data_nasc,
      'required'    => empty($parentType) && $camposObrigatorios
    );

    $this->inputsHelper()->date('data_nasc', $options);


    // pai, mãe

    $this->inputPai();
    $this->inputMae();


    // documentos

    $documentos        = new clsDocumento();
    $documentos->idpes = $this->cod_pessoa_fj;
    $documentos        = $documentos->detalhe();

    // rg

    // o rg é obrigatorio ao cadastrar pai ou mãe, exceto se configurado como opcional.

    $required = (! empty($parentType));

    if ($required && $GLOBALS['coreExt']['Config']->app->rg_pessoa_fisica_pais_opcional) {
      $required = false;
    }

    $options = array(
      'required'    => $required,
      'label'       => 'RG / Data emissão',
      'placeholder' => 'Documento identidade',
      'value'       => $documentos['rg'],
      'max_length'  => 25,
      'size'        => 27,
      'inline'      => true
    );

    $this->inputsHelper()->integer('rg', $options);


    // data emissão rg

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emissão',
      'value'       => $documentos['data_exp_rg'],
      'size'        => 19
    );

    $this->inputsHelper()->date('data_emissao_rg', $options);


    // orgão emissão rg

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

    // Código NIS (PIS/PASEP)

    $options = array(
      'required'    => false,
      'label'       => 'NIS (PIS/PASEP)',
      'placeholder' => '',
      'value'       => $this->nis_pis_pasep,
      'max_length'  => 11,
      'size'        => 20
    );

    $this->inputsHelper()->integer('nis_pis_pasep', $options);

    // Carteira do SUS

    $options = array(
      'required'    => false,
      'label'       => 'Número da carteira do SUS',
      'placeholder' => '',
      'value'       => $this->sus,
      'max_length'  => 20,
      'size'        => 20
    );

    $this->inputsHelper()->text('sus', $options);

    // tipo de certidao civil

    $selectOptions = array(
      null                               => 'Tipo certidão civil',
      'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
      91                                 => 'Nascimento (antigo formato)',
      'certidao_casamento_novo_formato'  => 'Casamento (novo formato)',
      92                                 => 'Casamento (antigo formato)'
    );


    // caso certidao nascimento novo formato tenha sido informado,
    // considera este o tipo da certidão
    if (! empty($documentos['certidao_nascimento']))
      $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
    else if (! empty($documentos['certidao_casamento']))
      $tipoCertidaoCivil = 'certidao_casamento_novo_formato';
    else
      $tipoCertidaoCivil = $documentos['tipo_cert_civil'];

    $options = array(
      'required'  => false,
      'label'     => 'Tipo certidão civil',
      'value'     => $tipoCertidaoCivil,
      'resources' => $selectOptions,
      'inline'    => true
    );

    $this->inputsHelper()->select('tipo_certidao_civil', $options);


    // termo certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Termo',
      'value'       => $documentos['num_termo'],
      'max_length'  => 8,
      'inline'      => true
    );

    $this->inputsHelper()->integer('termo_certidao_civil', $options);


    // livro certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Livro',
      'value'       => $documentos['num_livro'],
      'max_length'  => 8,
      'size'        => 15,
      'inline'      => true
    );

    $this->inputsHelper()->text('livro_certidao_civil', $options);


    // folha certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Folha',
      'value'       => $documentos['num_folha'],
      'max_length'  => 4,
      'inline'      => true
    );

    $this->inputsHelper()->integer('folha_certidao_civil', $options);


    // certidao nascimento (novo padrão)

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Certidão nascimento',
      'value'       => $documentos['certidao_nascimento'],
      'max_length'  => 32,
      'size'        => 32,
      'inline'      => true
    );

    $this->inputsHelper()->text('certidao_nascimento', $options);

    // certidao casamento (novo padrão)

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Certidão casamento',
      'value'       => $documentos['certidao_casamento'],
      'max_length'  => 32,
      'size'        => 32
    );

    $this->inputsHelper()->text('certidao_casamento', $options);


    // uf emissão certidão civil

    $options = array(
      'required' => false,
      'label'    => 'Estado emissão / Data emissão',
      'label_hint' => 'Informe o estado para poder informar o código do cartório',
      'value'    => $documentos['sigla_uf_cert_civil'],
      'inline'   => true
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_certidao_civil'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // data emissão certidão civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emissão',
      'value'       => $documentos['data_emissao_cert_civil'],
      'inline'   => true
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


    // cartório emissão certidão civil

    $options = array(
      'required'    => false,
      'label'       => 'Cartório emissão',
      'value'       => $documentos['cartorio_cert_civil'],
      'cols'        => 45,
      'max_length'  => 200,
    );

    $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);

    // Passaporte
    $options = array(
      'required'    => false,
      'label'       => 'Passaporte',
      'value'       => $documentos['passaporte'],
      'cols'        => 45,
      'max_length'  => 20
    );

    $this->inputsHelper()->text('passaporte', $options);

    // carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => 'Carteira de trabalho / Série',
      'placeholder' => 'Carteira de trabalho',
      'value'       => $documentos['num_cart_trabalho'],
      'max_length'  => 7,
      'inline'      => true

    );

    $this->inputsHelper()->integer('carteira_trabalho', $options);

    // serie carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Série',
      'value'       => $documentos['serie_cart_trabalho'],
      'max_length'  => 5
    );

    $this->inputsHelper()->integer('serie_carteira_trabalho', $options);


    // uf emissão carteira de trabalho

    $options = array(
      'required' => false,
      'label'    => 'Estado emissão / Data emissão',
      'value'    => $documentos['sigla_uf_cart_trabalho'],
      'inline'   => true
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_carteira_trabalho'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // data emissão carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emissão',
      'value'       => $documentos['data_emissao_cart_trabalho']
    );

    $this->inputsHelper()->date('data_emissao_carteira_trabalho', $options);


    // titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => 'Titulo eleitor / Zona / Seção',
      'placeholder' => 'Titulo eleitor',
      'value'       => $documentos['num_tit_eleitor'],
      'max_length'  => 13,
      'inline'      => true
    );

    $this->inputsHelper()->integer('titulo_eleitor', $options);


    // zona titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Zona',
      'value'       => $documentos['zona_tit_eleitor'],
      'max_length'  => 4,
      'inline'      => true
    );

    $this->inputsHelper()->integer('zona_titulo_eleitor', $options);


    // seção titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Seção',
      'value'       => $documentos['secao_tit_eleitor'],
      'max_length'  => 4
    );

    $this->inputsHelper()->integer('secao_titulo_eleitor', $options);


    // Cor/raça.

    $racas         = new clsCadastroRaca();
    $racas         = $racas->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);

    foreach ($racas as $raca)
      $selectOptions[$raca['cod_raca']] = $raca['nm_raca'];

    $selectOptions = array(null => 'Selecione') + Portabilis_Array_Utils::sortByValue($selectOptions);

    $this->campoLista('cor_raca', 'Raça', $selectOptions, $this->cod_raca, '', FALSE, '', '', '', $obrigarCamposCenso);


    // nacionalidade

    // tipos
    $tiposNacionalidade = array('1'  => 'Brasileira',
                                '2'  => 'Naturalizado brasileiro',
                                '3'  => 'Estrangeira');

    $options            = array('label'       => 'Nacionalidade',
                                'resources'   => $tiposNacionalidade,
                                'required'    => $obrigarCamposCenso,
                                'inline'      => true,
                                'value'       => $this->tipo_nacionalidade);

    $this->inputsHelper()->select('tipo_nacionalidade', $options);


    // pais origem

    $options = array(
      'label'       => '',
      'placeholder' => 'Informe o nome do pais',
      'required'    => true
    );

    $hiddenInputOptions = array(
      'options' => array('value' => $this->pais_origem_id)
    );

    $helperOptions = array(
      'objectName'         => 'pais_origem',
      'hiddenInputOptions' => $hiddenInputOptions
    );

    $this->inputsHelper()->simpleSearchPais('nome', $options, $helperOptions);


    //Falecido
    $options = array('label' => 'Falecido?', 'required' => false, 'value' => dbBool($this->falecido));

    $this->inputsHelper()->checkbox('falecido', $options);

    // naturalidade

     $options       = array('label' => 'Naturalidade', 'required'   => $naturalidadeObrigatoria && $camposObrigatorios);

    $helperOptions = array('objectName'         => 'naturalidade',
                           'hiddenInputOptions' => array('options' => array('value' => $this->naturalidade_id)));

    $this->inputsHelper()->simpleSearchMunicipio('nome', $options, $helperOptions);


    // Religião
    $this->inputsHelper()->religiao(array('required' => false));

    // Detalhes do Endereço
    if ($this->idlog && $this->idbai){

      $objLogradouro = new clsLogradouro($this->idlog);
      $detalheLogradouro = $objLogradouro->detalhe();
      if ($detalheLogradouro)
        $this->municipio_id = $detalheLogradouro['idmun'];

      $sql = "SELECT iddis FROM public.bairro
            WHERE idbai = '{$this->idbai}'";

      $options = array('return_only' => 'first-field');
      $this->distrito_id = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);


    // Caso seja um endereço externo, tentamos então recuperar a cidade pelo cep
    }elseif($this->cep){

      $numCep = idFederal2int($this->cep);

      $sql = "SELECT idmun, count(idmun) as count_mun FROM public.logradouro l, urbano.cep_logradouro cl
              WHERE cl.idlog = l.idlog AND cl.cep = '{$numCep}' group by idmun order by count_mun desc limit 1";

      $options = array('return_only' => 'first-field');
      $result = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

      if ($result)
        $this->municipio_id = $result;

    }
    if ($this->cod_pessoa_fj){

      $objPE = new clsPessoaEndereco($this->cod_pessoa_fj);
      $det = $objPE->detalhe();

      if($det){

        $this->bairro_id = $det['idbai'];
        $this->logradouro_id = $det['idlog'];
        $sql = "SELECT iddis FROM public.bairro
              WHERE idbai = '{$this->bairro_id}'";

        $options = array('return_only' => 'first-field');
        $this->distrito_id = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
      }
    }

    if (!($this->bairro_id && $this->municipio_id && $this->logradouro_id && $this->distrito_id)){
      $this->bairro_id = null;
      $this->municipio_id = null;
      $this->logradouro_id = null;
    }

    $this->campoOculto('idbai', $this->idbai);
    $this->campoOculto('idlog', $this->idlog);
    $this->campoOculto('cep', $this->cep);
    $this->campoOculto('ref_sigla_uf', $this->sigla_uf);
    $this->campoOculto('ref_idtlog', $this->idtlog);
    $this->campoOculto('id_cidade', $this->cidade);


    // o endereçamento é opcional
    $enderecamentoObrigatorio = false;


    // considera como endereço localizado por CEP quando alguma das variaveis de instancia
    // idbai (bairro) ou idlog (logradouro) estão definidas, neste caso desabilita a edição
    // dos campos definidos via CEP.
    //$desativarCamposDefinidosViaCep = ($this->idbai || $this->idlog);

    // Caso o cep já esteja definido, os campos já vem desbloqueados inicialmente
    $desativarCamposDefinidosViaCep = empty($this->cep);

    $this->campoRotulo('enderecamento','<b> Endereçamento</b>', '', '', 'Digite um CEP ou clique na lupa para<br/> busca avançada para começar');

    $this->campoCep(
      'cep_',
      'CEP',
      $this->cep,
      $enderecamentoObrigatorio,
      '-',
            "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro2.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=distrito_id&campo7=distrito_distrito&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
      false
    );

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Município'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $helperOptions = array('objectName'         => 'municipio',
                           'hiddenInputOptions' => array('options' => array('value' => $this->municipio_id)));

    $this->inputsHelper()->simpleSearchMunicipio('municipio', $options, $helperOptions);

    $options       = array('label' => Portabilis_String_Utils::toLatin1('Distrito'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $helperOptions = array('objectName'         => 'distrito',
                           'hiddenInputOptions' => array('options' => array('value' => $this->distrito_id)));

    $this->inputsHelper()->simpleSearchDistrito('distrito', $options, $helperOptions);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->bairro_id)));

    $options       = array( 'label' => Portabilis_String_Utils::toLatin1('Bairro / Zona de Localização - <b>Buscar</b>'), 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);


    $this->inputsHelper()->simpleSearchBairro('bairro', $options, $helperOptions);

    $options = array(
      'label'       => 'Bairro / Zona de Localização - <b>Cadastrar</b>',
      'placeholder' => 'Bairro',
      'value'       => $this->bairro,
      'max_length'  => 40,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('bairro', $options);

    // zona localização

    $zonas = App_Model_ZonaLocalizacao::getInstance();
    $zonas = $zonas->getEnums();
    $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localização', $zonas);

    $options = array(
      'label'       => '',
      'placeholder' => 'Zona localização',
      'value'       => $this->zona_localizacao,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'resources'   => $zonas,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->select('zona_localizacao', $options);

    $helperOptions = array('hiddenInputOptions' => array('options' => array('value' => $this->logradouro_id)));

    $options       = array('label' => 'Tipo / Logradouro - <b>Buscar</b>', 'required'   => $enderecamentoObrigatorio, 'disabled' => $desativarCamposDefinidosViaCep);

    $this->inputsHelper()->simpleSearchLogradouro('logradouro', $options, $helperOptions);

    // tipo logradouro

    $options = array(
      'label'       => 'Tipo / Logradouro - <b>Cadastrar</b>',
      'value'       => $this->idtlog,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $helperOptions = array(
      'attrName' => 'idtlog'
    );

    $this->inputsHelper()->tipoLogradouro($options, $helperOptions);


    // logradouro

    $options = array(
      'label'       => '',
      'placeholder' => 'Logradouro',
      'value'       => $this->logradouro,
      'max_length'  => 150,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('logradouro', $options);

    // zona localização

    $zonas = array('' => 'Selecione',
                   1  => 'Urbana',
                   2  => 'Rural');

    $options = array(
      'label'       => 'Zona localização',
      'value'       => $this->zona_localizacao_censo,
      'resources'   => $zonas,
      'required'    => $obrigarCamposCenso,
    );

    $this->inputsHelper()->select('zona_localizacao_censo', $options);

    // complemento

    $options = array(
      'required'    => false,
      'value'       => $this->complemento,
      'max_length'  => 20
    );

    $this->inputsHelper()->text('complemento', $options);


    // numero

    $options = array(
      'required'    => false,
      'label'       => 'Número / Letra',
      'placeholder' => 'Número',
      'value'       => $this->numero,
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
      'label'       => 'Nº apartamento / Bloco / Andar',
      'placeholder' => 'Nº apartamento',
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


    // contato
    $this->campoRotulo('contato','<b>Contato</b>', '', '', 'Informações de contato da pessoa');
    $this->inputTelefone('1', 'Telefone residencial');
    $this->inputTelefone('2', 'Celular');
    $this->inputTelefone('mov', 'Telefone adicional');
    $this->inputTelefone('fax', 'Fax');
    $this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);

    // renda
    $this->campoRotulo('renda','<b>Trabalho e renda</b>', '', '', 'Informações de trabalho e renda da pessoa');
    $this->campoTexto('ocupacao', 'Ocupação', $this->ocupacao, '50', '255', FALSE);
    $this->campoMonetario('renda_mensal', 'Renda mensal (R$)', $this->renda_mensal, '9', '10');
    $this->campoData('data_admissao', 'Data de admissão', $this->data_admissao);
    $this->campoTexto('empresa', 'Empresa', $this->empresa, '50', '255', FALSE);
    $this->inputTelefone('empresa', 'Telefone da empresa');
    $this->campoTexto('pessoa_contato', 'Pessoa de contato na empresa', $this->pessoa_contato, '50', '255', FALSE);

    // after change pessoa pai / mae

    if ($parentType)
      $this->inputsHelper()->hidden('parent_type', array('value' => $parentType));


    $styles = array(
      '/modules/Portabilis/Assets/Stylesheets/Frontend.css',
      '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
      '/modules/Cadastro/Assets/Stylesheets/PessoaFisica.css'
    );

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

    $script = array('/modules/Cadastro/Assets/Javascripts/PessoaFisica.js',
                    '/modules/Cadastro/Assets/Javascripts/Endereco.js');
    Portabilis_View_Helper_Application::loadJavascript($this, $script);

    $this->campoCep(
      'cep_',
      'CEP',
      $this->cep,
      $enderecamentoObrigatorio,
      '-',
      "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro2.php?campo1=bairro_bairro&campo2=bairro_id&campo3=cep&campo4=logradouro_logradouro&campo5=logradouro_id&campo6=distrito_id&campo7=distrito_distrito&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=municipio_municipio&campo12=idtlog&campo13=municipio_id&campo14=zona_localizacao\'></iframe>');\">",
      false
    );

  }

  function Novo() {
    return $this->createOrUpdate();
  }

  function Editar() {
    return $this->createOrUpdate($this->cod_pessoa_fj);
  }

  function Excluir() {
    $idPes = $this->cod_pessoa_fj;

    $aluno = new clsPmieducarAluno();
    $aluno = $aluno->lista(null, null, null, null, null, $idPes, null, null, null, null, 1);

    if ($aluno) {
      $this->mensagem = 'Exclusão não realizada.';
      $this->mensagem .= '<br />Esta pessoa possuí vínculo com aluno.';

      return FALSE;
    }

    $usuario = new clsPmieducarUsuario();
    $usuario = $usuario->lista($idPes, null, null, null, null, null, null, null, null, null, TRUE);
    $funcionario = new clsPortalFuncionario();
    $funcionario->ref_cod_pessoa_fj = $idPes;
    $funcionario = $funcionario->lista(null, null, 1);

    if ($funcionario && $usuario) {
      $this->mensagem = 'Exclusão não realizada.';
      $this->mensagem .= '<br />Esta pessoa possuí vínculo com usuário do sistema.';

      return FALSE;
    }

    $servidor = new clsPmieducarServidor();
    $servidor = $servidor->lista($idPes, null, null, null, null, null, null, null, 1);

    if ($servidor) {
      $this->mensagem = 'Exclusão não realizada.';
      $this->mensagem .= '<br />Esta pessoa possuí vínculo com servidor.';

      return FALSE;
    }

    $cliente = new clsPmieducarCliente();
    $cliente = $cliente->lista(null, null, null, $idPes, null, null, null, null, null, null, 1);

    if ($cliente) {
      $this->mensagem = 'Exclusão não realizada.';
      $this->mensagem .= '<br />Esta pessoa possuí vínculo com cliente.';

      return FALSE;
    }

    $usuarioTransporte = new clsModulesPessoaTransporte();
    $usuarioTransporte = $usuarioTransporte->lista(null, $idPes);

    if ($usuarioTransporte) {
      $this->mensagem = 'Exclusão não realizada.';
      $this->mensagem .= '<br />Esta pessoa possuí vínculo com usuário de transporte.';

      return FALSE;
    }

    $motorista = new clsModulesMotorista();
    $motorista = $motorista->lista(null, null, null, null, null, $idPes);

    if ($motorista) {
      $this->mensagem = 'Exclusão não realizada.';
      $this->mensagem .= '<br />Esta pessoa possuí vínculo com motorista.';

      return FALSE;
    }

    $pessoaFisica = new clsPessoaFisica($idPes);
    $pessoaFisica->excluir();

    $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.';
    header('Location: atendidos_lst.php');
    die();
  }

  function afterChangePessoa($id) {
    Portabilis_View_Helper_Application::embedJavascript($this, "

      if(window.opener &&  window.opener.afterChangePessoa) {
        var parentType = \$j('#parent_type').val();

        if (parentType)
          window.opener.afterChangePessoa(self, parentType, $id, \$j('#nm_pessoa').val());
        else
          window.opener.afterChangePessoa(self, null, $id, \$j('#nm_pessoa').val());
      }
      else
        document.location = 'atendidos_lst.php';

    ", $afterReady = true);
  }

  protected function loadAlunoByPessoaId($id) {
    $aluno            = new clsPmieducarAluno();
    $aluno->ref_idpes = $id;

    return $aluno->detalhe();
  }

  protected function inputPai() {
    $this->addParentsInput('pai');
  }

  protected function inputMae() {
    $this->addParentsInput('mae', 'mãe');
  }

  protected function addParentsInput($parentType, $parentTypeLabel = '') {
    if (! $parentTypeLabel)
      $parentTypeLabel = $parentType;

    if (! isset($this->_aluno))
      $this->_aluno = $this->loadAlunoByPessoaId($this->cod_pessoa_fj);

    $parentId = $this->{$parentType . '_id'};


    // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
    //pela antiga interface do cadastro de alunos.

    if (! $parentId && $this->_aluno['nm_' . $parentType]) {
      $nome      = Portabilis_String_Utils::toLatin1($this->_aluno['nm_' . $parentType],
                                                     array('transform' => true, 'escape' => false));

      $inputHint = '<br /><b>Dica:</b> Foi informado o nome "' . $nome .
                   '" no cadastro de aluno,<br />tente pesquisar esta pessoa ' .
                   'pelo CPF ou RG, caso não encontre, cadastre uma nova pessoa.';
    }


    $hiddenInputOptions = array('options' => array('value' => $parentId));
    $helperOptions      = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

    $options            = array('label'      => 'Pessoa ' . $parentTypeLabel,
                                'size'       => 50,
                                'required'   => false,
                                'input_hint' => $inputHint);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
  }

  protected function validatesCpf($cpf) {
    $isValid = true;

    if ($cpf && ! Portabilis_Utils_Validation::validatesCpf($cpf)) {
      $this->erros['id_federal'] = 'CPF inválido.';
      $isValid = false;
    }
    elseif($cpf) {
      $fisica      = new clsFisica();
      $fisica->cpf = idFederal2int($cpf);
      $fisica      = $fisica->detalhe();

      if ($fisica['cpf'] && $this->cod_pessoa_fj != $fisica['idpes']) {
        $link = "<a class='decorated' target='__blank' href='/intranet/atendidos_cad.php?cod_pessoa_fj=" .
                "{$fisica['idpes']}'>{$fisica['idpes']}</a>";

        $this->erros['id_federal'] = "CPF já utilizado pela pessoa $link.";
        $isValid = false;
      }
    }

    return $isValid;
  }

  protected function createOrUpdate($pessoaIdOrNull = null) {
    if (!$this->possuiDocumentoObrigatorio()) {
      $this->mensagem = 'É necessário o preenchimento de pelo menos um dos seguintes documentos: CPF, RG ou Certidão civil.';
      return false;
    }

    if (! $this->validatesCpf($this->id_federal))
      return false;

    if (!$this->validatePhoto())
      return false;

    if (!$this->validaCertidao())
      return false;

    if (!$this->validaNisPisPasep()) {
      return false;
    }

    $pessoaId = $this->createOrUpdatePessoa($pessoaIdOrNull);
    $this->savePhoto($pessoaId);
    $this->createOrUpdatePessoaFisica($pessoaId);
    $this->createOrUpdateDocumentos($pessoaId);
    $this->createOrUpdateTelefones($pessoaId);
    $this->createOrUpdateEndereco($pessoaId);
    $this->afterChangePessoa($pessoaId);

    return true;
  }

  //envia foto e salva caminha no banco
  protected function savePhoto($id){

    if ($this->objPhoto!=null){

      $caminhoFoto = $this->objPhoto->sendPicture($id);
      if ($caminhoFoto!=''){
        //new clsCadastroFisicaFoto($id)->exclui();
        $obj = new clsCadastroFisicaFoto($id,$caminhoFoto);
        $detalheFoto = $obj->detalhe();
        if (is_array($detalheFoto) && count($detalheFoto)>0)
         $obj->edita();
        else
         $obj->cadastra();

        return true;
      } else{
        echo '<script>alert(\'Foto não salva.\')</script>';
        return false;
      }
    }elseif($this->file_delete == 'on'){
      $obj = new clsCadastroFisicaFoto($id);
      $obj->excluir();
    }
  }

  // Retorna true caso a foto seja válida
  protected function validatePhoto(){

    $this->arquivoFoto = $_FILES["file"];
    if (!empty($this->arquivoFoto["name"])){
      $this->arquivoFoto["name"] = mb_strtolower($this->arquivoFoto["name"], 'UTF-8');
      $this->objPhoto = new PictureController($this->arquivoFoto);
      if ($this->objPhoto->validatePicture()){
        return TRUE;
      } else {
        $this->mensagem = $this->objPhoto->getErrorMessage();
        return false;
      }
      return false;
    }else{
      $this->objPhoto = null;
      return true;
    }

  }

  function possuiDocumentoObrigatorio() {
    $certidaoCivil = $this->termo_certidao_civil && $this->folha_certidao_civil && $this->livro_certidao_civil;
    $certidaoNascimentoNovoFormato = $this->certidao_nascimento;
    $certidaoCasamentoNovoFormato = $this->certidao_casamento;

    return $this->id_federal ||
           $this->rg ||
           $certidaoCivil ||
           $certidaoCasamentoNovoFormato ||
           $certidaoNascimentoNovoFormato;
  }

  protected function validaCertidao() {
    $certidaoNascimento = ($_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato');
    $certidaoCasamento = ($_REQUEST['tipo_certidao_civil'] == 'certidao_casamento_novo_formato');

    if ($certidaoNascimento && strlen($this->certidao_nascimento) < 32) {
      $this->mensagem = 'O campo referente a certidão de nascimento deve conter exatos 32 dígitos.';
      return false;
    } else if ($certidaoCasamento && strlen($this->certidao_casamento) < 32) {
      $this->mensagem = 'O campo referente a certidão de casamento deve conter exatos 32 dígitos.';
      return false;
    }

    return true;
  }

  protected function validaNisPisPasep()
  {
    if ($this->nis_pis_pasep && strlen($this->nis_pis_pasep) != 11) {
      $this->mensagem = 'O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.';
      return false;
    }
    return true;
  }

  protected function createOrUpdatePessoa($pessoaId = null) {
    $pessoa        = new clsPessoa_();
    $pessoa->idpes = $pessoaId;
    $pessoa->nome  = $this->nm_pessoa;
    $pessoa->email = addslashes($this->email);

    $sql = "select 1 from cadastro.pessoa WHERE idpes = $1 limit 1";

    if (! $pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
      $pessoa->tipo      = 'F';
      $pessoa->idpes_cad = $this->currentUserId();
      $pessoaId          = $pessoa->cadastra();
    }
    else {
      $pessoa->idpes_rev = $this->currentUserId();
      $pessoa->data_rev  = date('Y-m-d H:i:s', time());
      $pessoa->edita();
    }

    return $pessoaId;
  }

  protected function createOrUpdatePessoaFisica($pessoaId) {
    $fisica                         = new clsFisica();
    $fisica->idpes                  = $pessoaId;
    $fisica->data_nasc              = Portabilis_Date_Utils::brToPgSQL($this->data_nasc);
    $fisica->sexo                   = $this->sexo;
    $fisica->ref_cod_sistema        = 'NULL';
    $fisica->cpf                    = $this->id_federal ? idFederal2int($this->id_federal) : 'NULL';
    $fisica->ideciv                 = $this->estado_civil_id;
    $fisica->idpes_pai              = $this->pai_id ? $this->pai_id : "NULL";
    $fisica->idpes_mae              = $this->mae_id ? $this->mae_id : "NULL";
    $fisica->nacionalidade          = $_REQUEST['tipo_nacionalidade'];
    $fisica->idpais_estrangeiro     = $_REQUEST['pais_origem_id'];
    $fisica->idmun_nascimento       = $_REQUEST['naturalidade_id'];
    $fisica->sus                    = $this->sus;
    $fisica->nis_pis_pasep          = $this->nis_pis_pasep ? $this->nis_pis_pasep : "NULL";
    $fisica->ocupacao               = $this->ocupacao;
    $fisica->empresa                = $this->empresa;
    $fisica->ddd_telefone_empresa   = $this->ddd_telefone_empresa;
    $fisica->telefone_empresa       = $this->telefone_empresa;
    $fisica->pessoa_contato         = $this->pessoa_contato;
    $this->renda_mensal             = str_replace('.', '', $this->renda_mensal);
    $this->renda_mensal             = str_replace(',', '.', $this->renda_mensal);
    $fisica->renda_mensal           = $this->renda_mensal;
    $fisica->data_admissao          = $this->data_admissao ? Portabilis_Date_Utils::brToPgSQL($this->data_admissao) : null;
    $fisica->falecido               = $this->falecido;
    $fisica->ref_cod_religiao       = $this->religiao_id;
    $fisica->zona_localizacao_censo = empty($this->zona_localizacao_censo) ? NULL : $this->zona_localizacao_censo;

    $sql = "select 1 from cadastro.fisica WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $fisica->cadastra();
    else
      $fisica->edita();

    $this->createOrUpdateRaca($pessoaId, $this->cor_raca);
  }

  function createOrUpdateRaca($pessoaId, $corRaca) {
    $pessoaId = (int) $pessoaId;
    $corRaca  = (int) $corRaca;

    if ($corRaca == 0) return false; //Quando não tiver cor/raça selecionado não faz update

    $raca = new clsCadastroFisicaRaca($pessoaId, $corRaca);

    if ($raca->existe())
      return $raca->edita();

    return $raca->cadastra();
  }

  protected function createOrUpdateDocumentos($pessoaId) {
    $documentos                             = new clsDocumento();
    $documentos->idpes                      = $pessoaId;


    // rg

    $documentos->rg                         = $_REQUEST['rg'];

    $documentos->data_exp_rg                = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_rg']
    );

    $documentos->idorg_exp_rg               = $_REQUEST['orgao_emissao_rg'];
    $documentos->sigla_uf_exp_rg            = $_REQUEST['uf_emissao_rg'];


    // certidão civil


    // o tipo certidão novo padrão é apenas para exibição ao usuário,
    // não precisa ser gravado no banco
    //
    // quando selecionado um tipo diferente do novo formato,
    // é removido o valor de certidao_nascimento.
    //
    if ($_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato') {
      $documentos->tipo_cert_civil     = null;
      $documentos->certidao_casamento  = '';
      $documentos->certidao_nascimento = $_REQUEST['certidao_nascimento'];
    }else if ($_REQUEST['tipo_certidao_civil'] == 'certidao_casamento_novo_formato') {
      $documentos->tipo_cert_civil     = null;
      $documentos->certidao_nascimento = '';
      $documentos->certidao_casamento = $_REQUEST['certidao_casamento'];
    }else{
      $documentos->tipo_cert_civil     = $_REQUEST['tipo_certidao_civil'];
      $documentos->certidao_nascimento = '';
      $documentos->certidao_casamento  = '';
    }

    $documentos->num_termo                  = $_REQUEST['termo_certidao_civil'];
    $documentos->num_livro                  = $_REQUEST['livro_certidao_civil'];
    $documentos->num_folha                  = $_REQUEST['folha_certidao_civil'];

    $documentos->data_emissao_cert_civil    = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_certidao_civil']
    );

    $documentos->sigla_uf_cert_civil        = $_REQUEST['uf_emissao_certidao_civil'];
    $documentos->cartorio_cert_civil        = addslashes($_REQUEST['cartorio_emissao_certidao_civil']);
    $documentos->passaporte                 = addslashes($_REQUEST['passaporte']);
    $documentos->cartorio_cert_civil_inep   = $_REQUEST['cartorio_cert_civil_inep_id'];


    // carteira de trabalho

    $documentos->num_cart_trabalho          = $_REQUEST['carteira_trabalho'];
    $documentos->serie_cart_trabalho        = $_REQUEST['serie_carteira_trabalho'];

    $documentos->data_emissao_cart_trabalho = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_carteira_trabalho']
    );

    $documentos->sigla_uf_cart_trabalho     = $_REQUEST['uf_emissao_carteira_trabalho'];


    // titulo de eleitor

    $documentos->num_tit_eleitor            = $_REQUEST['titulo_eleitor'];
    $documentos->zona_tit_eleitor           = $_REQUEST['zona_titulo_eleitor'];
    $documentos->secao_tit_eleitor          = $_REQUEST['secao_titulo_eleitor'];


    // Alteração de documentos compativel com a versão anterior do cadastro,
    // onde era possivel criar uma pessoa, não informando os documentos,
    // o que não criaria o registro do documento, sendo assim, ao editar uma pessoa,
    // o registro do documento será criado, caso não exista.

    $sql = "select 1 from cadastro.documento WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $documentos->cadastra();
    else
      $documentos->edita();
  }

  protected function _createOrUpdatePessoaEndereco($pessoaId) {

    $cep = idFederal2Int($this->cep_);

    $objCepLogradouro = new ClsCepLogradouro($cep, $this->logradouro_id);

    if (! $objCepLogradouro->existe())
      $objCepLogradouro->cadastra();

    $objCepLogradouroBairro = new ClsCepLogradouroBairro();
    $objCepLogradouroBairro->cep = $cep;
    $objCepLogradouroBairro->idbai = $this->bairro_id;
    $objCepLogradouroBairro->idlog = $this->logradouro_id;


    if (! $objCepLogradouroBairro->existe())
      $objCepLogradouroBairro->cadastra();

    #die("Morram <br> $cep <br> {$this->bairro_id} <br> {$this->logradouro_id}");
    $endereco = new clsPessoaEndereco(
      $pessoaId,
      $cep,
      $this->logradouro_id,
      $this->bairro_id,
      $this->numero,
      addslashes($this->complemento),
      FALSE,
      addslashes($this->letra),
      addslashes($this->bloco),
      $this->apartamento,
      $this->andar
    );

    // forçado exclusão, assim ao cadastrar endereco_pessoa novamente,
    // será excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function _createOrUpdateEnderecoExterno($pessoaId) {
    $endereco = new clsEnderecoExterno(
      $pessoaId,
      '1',
      $this->idtlog,
      addslashes($this->logradouro),
      $this->numero,
      addslashes($this->letra),
      addslashes($this->complemento),
      addslashes($this->bairro),
      idFederal2int($this->cep_),
      addslashes($this->cidade),
      $this->sigla_uf,
      FALSE,
      addslashes($this->bloco),
      $this->apartamento,
      $this->andar,
      FALSE,
      FALSE,
      $this->zona_localizacao
    );

    // forçado exclusão, assim ao cadastrar endereco_externo novamente,
    // será excluido endereco_pessoa (por meio da trigger fcn_aft_ins_endereco_externo).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function createOrUpdateEndereco($pessoaId) {

    if ($this->cep_ && is_numeric($this->bairro_id) && is_numeric($this->logradouro_id))
      $this->_createOrUpdatePessoaEndereco($pessoaId);
    else if($this->cep_ && is_numeric($this->municipio_id) && is_numeric($this->distrito_id)){

      if (!is_numeric($this->bairro_id)){
        if ($this->canCreateBairro())
          $this->bairro_id = $this->createBairro();
        else
          return;
      }

      if (!is_numeric($this->logradouro_id)){
        if($this->canCreateLogradouro())
          $this->logradouro_id = $this->createLogradouro();
        else
          return;
      }

      $this->_createOrUpdatePessoaEndereco($pessoaId);

    }else{
      $endereco = new clsPessoaEndereco($pessoaId);
      $endereco->exclui();
    }


    /* *** IMPLEMENTAÇÃO ANTIGA ***

    $enderecoExterno = ! empty($this->cep_);

    if (! $enderecoExterno && $this->cep && $this->idbai && $this->idlog)
      $this->_createOrUpdatePessoaEndereco($pessoaId);

    elseif($enderecoExterno)
      $this->_createOrUpdateEnderecoExterno($pessoaId);*/
  }

  protected function canCreateBairro(){
    return !empty($this->bairro) && !empty($this->zona_localizacao);
  }

  protected function canCreateLogradouro(){
    return !empty($this->logradouro) && !empty($this->idtlog);
  }

  protected function createBairro(){
    $objBairro = new clsBairro(null,$this->municipio_id,null,addslashes($this->bairro), $this->currentUserId());
    $objBairro->zona_localizacao = $this->zona_localizacao;
    $objBairro->iddis = $this->distrito_id;

    return $objBairro->cadastra();
  }

  protected function createLogradouro(){
    $objLogradouro = new clsLogradouro(null,$this->idtlog, $this->logradouro, $this->municipio_id,
                                           null, 'S', $this->currentUserId());
    return $objLogradouro->cadastra();
  }

  protected function createOrUpdateTelefones($pessoaId) {
    $telefones   = array();

    $telefones[] = new clsPessoaTelefone($pessoaId, 1, $this->telefone_1,   $this->ddd_telefone_1);
    $telefones[] = new clsPessoaTelefone($pessoaId, 2, $this->telefone_2,   $this->ddd_telefone_2);
    $telefones[] = new clsPessoaTelefone($pessoaId, 3, $this->telefone_mov, $this->ddd_telefone_mov);
    $telefones[] = new clsPessoaTelefone($pessoaId, 4, $this->telefone_fax, $this->ddd_telefone_fax);

    foreach ($telefones as $telefone)
      $telefone->cadastra();
  }

  // inputs usados em Gerar,
  // implementado estes metodos para não duplicar código
  // uma vez que estes campos são usados várias vezes em Gerar.

  protected function inputTelefone($type, $typeLabel = '') {
    if (! $typeLabel)
      $typeLabel = "Telefone {$type}";

    // ddd

    $options = array(
      'required'    => false,
      'label'       => "(ddd) / {$typeLabel}",
      'placeholder' => 'ddd',
      'value'       => $this->{"ddd_telefone_{$type}"},
      'max_length'  => 3,
      'size'        => 3,
      'inline'      => true
    );

    $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);


   // telefone

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => $typeLabel,
      'value'       => $this->{"telefone_{$type}"},
      'max_length'  => 11
    );

    $this->inputsHelper()->integer("telefone_{$type}", $options);
  }
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
