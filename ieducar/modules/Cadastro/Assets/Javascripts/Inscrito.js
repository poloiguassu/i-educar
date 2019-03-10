
var url = window.location.href;
var modoCadastro = url.indexOf("id=") == -1;

if (modoCadastro) {
    $j("[name^=tr_historico_altura_peso]").remove();
}

$j('#autorizado_um').closest('tr').show();
$j('#parentesco_um').closest('tr').show();


$j('#autorizado_um').change(abriCampoDois);
$j('#autorizado_dois').change();


function abriCampoDois() {
    $j('#autorizado_dois').closest('tr').show();
    $j('#parentesco_dois').closest('tr').show();
}

let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';
let obrigarDocumentoPessoa = $j('#obrigar_documento_pessoa').val() == '1';

var editar_pessoa = false;
var person_details;
var pai_details;
var mae_details;
var pessoaPaiOuMae;
var $idField = $j('#id');
var $nomeField = $j('#pessoa_nome');
var $cpfField = $j('#id_federal');

var $resourceNotice = $j('<span>')
    .html('')
    .addClass('error resource-notice')
    .hide()
    .width($nomeField.outerWidth() - 12)
    .insertBefore($idField.parent());

var $pessoaNotice = $resourceNotice
    .clone()
    .appendTo($nomeField.parent());

var $cpfNotice = $j('<span>')
    .html('')
    .addClass('error resource-notice')
    .hide()
    .width($j('#pessoa_nome')
    .outerWidth() - 12)
    .appendTo($cpfField.parent());

var $loadingLaudoMedico = $j('<img>')
    .attr('src', 'imagens/indicator.gif')
    .css('margin-top', '3px')
    .hide()
    .insertBefore($j('#span-laudo_medico'));

function possuiDocumentoObrigatorio() {
    var cpf = $j('#id_federal').val();
    var rg = $j('#rg').val();

    return cpf || rg;
  }

var newSubmitForm = function (event) {
    if (obrigarDocumentoPessoa && !possuiDocumentoObrigatorio()) {
        messageUtils.error('É necessário o preenchimento de pelo menos um dos seguintes documentos: CPF, RG ou Certidão civil.');
        return false;
    }

    var codigoInep = $j('#aluno_inep_id').val();

    if (codigoInep && codigoInep.length != 12) {
        return codigoInepInvalido();
    }

    if ($j('#deficiencias').val().length > 1) {
        if ($j('#url_laudo_medico_obrigatorio').length > 0 && $j('#url_laudo_medico').val().length < 1) {
            return laudoMedicoObrigatorio();
        }
    }

    submitFormExterno();
};

var $loadingDocumento = $j('<img>')
    .attr('src', 'imagens/indicator.gif')
    .css('margin-top', '3px')
    .hide()
    .insertBefore($j('#span-documento'));

var $arrayDocumento = [];
var $arrayUrlDocumento = [];
var $arrayDataDocumento = [];

function excluirDocumento(event) {
    $arrayUrlDocumento.splice(event.data.i - 1,1);
    $j('#documento').val('').removeClass('success');
    messageUtils.notice('Documento excluído com sucesso!');
    $j('#documento' + event.data.i).hide();
    montaUrlDocumento();
}

function addDocumento(url, data) {
    $index = $arrayDocumento.length;
    $id = $index + 1;
    $arrayUrlDocumento[$index] = url;
    $arrayDataDocumento[$index] = data;

    var dataDocumento = '';

    if (data) {
        dataDocumento = ' adicionado em ' + data;
    }

    $arrayDocumento[$arrayDocumento.length] = $j('<div>')
        .append($j('<span>')
        .html('Documento ' + $id + dataDocumento + ':')
        .attr('id', 'documento' + $id)
        .append($j('<a>')
        .html('Excluir')
        .addClass('decorated')
        .attr('id', 'link_excluir_documento_' + $id)
        .css('cursor', 'pointer')
        .css('margin-left', '10px')
        .click({i: $id}, excluirDocumento))
        .append($j('<a>')
        .html('Visualizar')
        .addClass('decorated')
        .attr('id', 'link_visualizar_documento_' + $id)
        .attr('target', '_blank')
        .attr('href', url)
        .css('cursor', 'pointer')
        .css('margin-left', '10px'))
    ).insertBefore($j('#documento'));

    montaUrlDocumento();
}

function montaUrlDocumento() {
    var url = '';

    for (var i = 0; i < $arrayUrlDocumento.length; i++) {
        if ($arrayUrlDocumento[i]) {
            var dataDocumento = '';
            var urlDocumento = $arrayUrlDocumento[i];

            if ($arrayDataDocumento[i]) {
                dataDocumento = '"data" : "' + $arrayDataDocumento[i] + '",';
            }

            url += '{' + dataDocumento + '"url" : "' + urlDocumento + '"},';
        }
    }

    //Remove a ultima vírgula
    if (url.substring(url.length - 1, url.length) == ",") {
        url = url.substring(0, url.length - 1);
    }

    $j('#url_documento').val('[' + url + ']');
}

var $paiNomeField = $j('#pai_nome');
var $paiIdField = $j('#pai_id');

var $maeNomeField = $j('#mae_nome');
var $maeIdField = $j('#mae_id');

var $responsavelNomeField = $j('#responsavel_nome');
var $responsavelIdField = $j('#responsavel_id');

var $pessoaPaiActionBar = $j('<span>')
    .html('')
    .addClass('pessoa-links pessoa-pai-links')
    .width($paiNomeField.outerWidth() - 12)
    .appendTo($paiNomeField.parent());

var $pessoaMaeActionBar = $pessoaPaiActionBar
    .clone()
    .removeClass('pessoa-pai-links')
    .addClass('pessoa-mae-links')
    .appendTo($maeNomeField.parent());

var $pessoaResponsavelActionBar = $pessoaPaiActionBar
    .clone()
    .removeClass('pessoa-pai-links')
    .addClass('pessoa-responsavel-links')
    .appendTo($responsavelNomeField.parent());


var $linkToCreatePessoaPai = $j('<a>')
    .addClass('cadastrar-pessoa-pai decorated')
    .attr('id', 'cadastrar-pessoa-pai-link')
    .html('Cadastrar pessoa')
    .appendTo($pessoaPaiActionBar);

var $linkToEditPessoaPai = $j('<a>')
    .hide()
    .addClass('editar-pessoa-pai decorated')
    .attr('id', 'editar-pessoa-pai-link')
    .html('Editar pessoa')
    .appendTo($pessoaPaiActionBar);

var $linkToCreatePessoaMae = $linkToCreatePessoaPai
    .clone()
    .removeClass('cadastrar-pessoa-pai')
    .attr('id', 'cadastrar-pessoa-mae-link')
    .addClass('cadastrar-pessoa-mae')
    .appendTo($pessoaMaeActionBar);

var $linkToEditPessoaMae = $linkToEditPessoaPai
    .clone()
    .removeClass('editar-pessoa-pai')
    .addClass('editar-pessoa-mae')
    .attr('id', 'editar-pessoa-mae-link')
    .appendTo($pessoaMaeActionBar);

var $linkToCreatePessoaResponsavel = $linkToCreatePessoaPai
    .clone()
    .removeClass('cadastrar-pessoa-pai')
    .attr('id', 'cadastrar-pessoa-responsavel-link')
    .addClass('cadastrar-pessoa-responsavel')
    .appendTo($pessoaResponsavelActionBar)
    .css('display', 'none');

var $linkToEditPessoaResponsavel = $linkToEditPessoaPai
    .clone()
    .removeClass('editar-pessoa-pai')
    .addClass('editar-pessoa-responsavel')
    .attr('id', 'editar-pessoa-responsavel-link')
    .appendTo($pessoaResponsavelActionBar);


// adiciona id 'stop' na linha separadora
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id', 'stop');
// Adiciona abas na página
$j('td .formdktd:first').append('<div id="tabControl"><ul><li><div id="tab1" class="alunoTab"> <span class="tabText">Dados pessoais</span></div></li><li><div id="tab2" class="alunoTab"> <span class="tabText">Processo Seletivo</span></div></li><li><div id="tab6" class="alunoTab"> <span class="tabText" style="">Projetos</span></div></li></ul></div>');

// Adiciona estilo de aba selecionada a primeira aba
$j('#tab1').addClass('alunoTab-active').removeClass('alunoTab');

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function (index, row) {
    if (index > $j('#tr_encaminhamento').index() - 1) {
        if (row.id != 'stop') {
            row.hide();
        } else {
            return false;
        }
    }
});

// Adiciona classe para que os campos de descrição possam ser desativados (checkboxs)
$j('#restricao_atividade_fisica, #acomp_medico_psicologico, #medicacao_especifica, #tratamento_medico, #doenca_congenita, #alergia_alimento, #alergia_medicamento, #fratura_trauma, #plano_saude').addClass('temDescricao');

// ajax

resourceOptions.handlePost = function (dataResponse) {
    $nomeField.attr('disabled', 'disabled');
    $j('.pessoa-links .cadastrar-pessoa').hide();

    if (!dataResponse.any_error_msg) {
        window.setTimeout(function () {
            document.location = '/intranet/selecao_inscritos_det.php?cod_inscrito=' + resource.id();
        }, 500);
    } else {
        $submitButton.removeAttr('disabled').val('Gravar');
    }
};

resourceOptions.handlePut = function (dataResponse) {
    if (!dataResponse.any_error_msg) {
        window.setTimeout(function () {
            document.location = '/intranet/selecao_inscritos_det.php?cod_inscrito=' + resource.id();
        }, 500);
    } else {
        $submitButton.removeAttr('disabled').val('Gravar');
    }
};

var tipo_resp;

resourceOptions.handleGet = function (dataResponse) {
    console.log(dataResponse);

    handleMessages(dataResponse.msgs);
    $resourceNotice.hide();

    if (dataResponse.id && !dataResponse.ativo) {
        $submitButton.attr('disabled', 'disabled').hide();
        $deleteButton.attr('disabled', 'disabled').hide();

        var msg = "Este cadastro foi desativado em <b>" + dataResponse.destroyed_at +
            " </b><br/>pelo usuário <b>" + dataResponse.destroyed_by + "</b>, ";

        $resourceNotice.html(msg).slideDown('fast');

        $j('<a>').addClass('decorated')
            .attr('href', '#')
            .click(resourceOptions.enable)
            .html('reativar cadastro.')
            .appendTo($resourceNotice);
    } else {
        $deleteButton.removeAttr('disabled').show();
    }

    if (dataResponse.pessoa_id) {
        getPersonDetails(dataResponse.pessoa_id);
    }

    $idField.val(dataResponse.id);

    $beneficios = $j('#beneficios');

    $j.each(dataResponse.beneficios, function (id, nome) {
        $beneficios.children("[value=" + id + "]").attr('selected', '');
    });

    $beneficios.trigger('chosen:updated');

    tipo_resp = dataResponse.tipo_responsavel;
    $j('#religiao_id').val(dataResponse.religiao_id);
    $j('#autorizado_um').val(dataResponse.autorizado_um);
    $j('#parentesco_um').val(dataResponse.parentesco_um);
    $j('#autorizado_dois').val(dataResponse.autorizado_dois);
    $j('#parentesco_dois').val(dataResponse.parentesco_dois);

    if ($j('#autorizado_um').val() == '') {
        $j('#autorizado_dois').closest('tr').hide();
        $j('#autorizado_dois').closest('tr').hide();
    } else {
        $j('#autorizado_dois').closest('tr').show();
        $j('#autorizado_dois').closest('tr').show();
    }

    if (dataResponse.url_documento) {
        var arrayDocumento = JSON.parse(dataResponse.url_documento);

        for (var i = 0; i < arrayDocumento.length; i++) {
            addDocumento(arrayDocumento[i].url, arrayDocumento[i].data);
        }
    }

    if (dataResponse.hasOwnProperty('etapas')) {
      $j.each(dataResponse.etapas, function (i, object) {
        $j('#etapa_' + i).val(dataResponse.etapas[i]);
      });
    }

    // campos texto
    $j('#grupo_sanguineo').val(dataResponse.grupo_sanguineo);
    $j('#fator_rh').val(dataResponse.fator_rh);
    $j('#responsavel').val(dataResponse.responsavel);
    $j('#responsavel_parentesco').val(dataResponse.responsavel_parentesco);
    $j('#responsavel_parentesco_telefone').val(dataResponse.responsavel_parentesco_telefone);
    $j('#responsavel_parentesco_celular').val(dataResponse.responsavel_parentesco_celular);

    $j('#processo_seletivo_id').val(dataResponse.ref_cod_selecao_processo);
    $j('#escola_municipio_id').val(dataResponse.estudando_escola);
    $j('#serie').val(dataResponse.estudando_serie);
    $j('#egresso').val(dataResponse.egresso);
    $j('#turno').val(dataResponse.estudando_turno);
    $j('#guarda_mirim').prop('checked', dataResponse.guarda_mirim);
    $j('#encaminhamento').prop('checked', dataResponse.encaminhamento);
    $j('#area_interesse').val(dataResponse.area_interesse);
    $j('#copia_rg').val(dataResponse.copia_rg);
    $j('#copia_cpf').val(dataResponse.copia_cpf);
    $j('#copia_residencia').val(dataResponse.copia_residencia);
    $j('#copia_renda').val(dataResponse.copia_renda);
    $j('#copia_historico').val(dataResponse.copia_historico);
};


// pessoa links callbacks
var changeVisibilityOfLinksToPessoaParent = function (parentType) {
    var $nomeField = $j(buildId(parentType + '_nome'));
    var $idField = $j(buildId(parentType + '_id'));
    var $linkToEdit = $j('.pessoa-' + parentType + '-links .editar-pessoa-' + parentType);

    if ($nomeField.val() && $idField.val()) {
        $linkToEdit.show().css('display', 'inline');
    } else {
        $nomeField.val('')
        $idField.val('');

        $linkToEdit.hide();
    }
};

var changeVisibilityOfLinksToPessoaPai = function () {
    changeVisibilityOfLinksToPessoaParent('pai');
};

var changeVisibilityOfLinksToPessoaMae = function () {
    changeVisibilityOfLinksToPessoaParent('mae');
};

var changeVisibilityOfLinksToPessoaResponsavel = function () {
    changeVisibilityOfLinksToPessoaParent('responsavel');
};

var simpleSearchPaiOptions = {
    autocompleteOptions: {close: changeVisibilityOfLinksToPessoaPai}
};

var simpleSearchMaeOptions = {
    autocompleteOptions: {close: changeVisibilityOfLinksToPessoaMae}
};

var simpleSearchResponsavelOptions = {
    autocompleteOptions: {close: changeVisibilityOfLinksToPessoaResponsavel}
};

$paiIdField.change(changeVisibilityOfLinksToPessoaPai);
$maeIdField.change(changeVisibilityOfLinksToPessoaMae);
$responsavelIdField.change(changeVisibilityOfLinksToPessoaResponsavel);

var handleGetPersonDetails = function (dataResponse) {
    handleMessages(dataResponse.msgs);
    $pessoaNotice.hide();

    person_details = dataResponse;

    mae_details = dataResponse.mae_details;

    pai_details = dataResponse.pai_details;

    var alunoId = dataResponse.aluno_id;

    if (alunoId && alunoId != resource.id()) {
        $submitButton.attr('disabled', 'disabled').hide();

        $pessoaNotice.html('Esta pessoa já possui o aluno código ' + alunoId + ' cadastrado, ')
            .slideDown('fast');

        $j('<a>')
            .addClass('decorated')
            .attr('href', resource.url(alunoId))
            .attr('target', '_blank')
            .html('acessar cadastro.')
            .appendTo($pessoaNotice);
    } else {
        $j('.pessoa-links .editar-pessoa')//.attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + dataResponse.id)
            .show().css('display', 'inline');

        $submitButton.removeAttr('disabled').show();
    }

    $j('#pessoa_id').val(dataResponse.id);
    var nameFull = dataResponse.id + ' - ' + dataResponse.nome;

    if (dataResponse.nome_social) {
      nameFull = dataResponse.id + ' - ' + dataResponse.nome_social + ' - Nome de registro: ' + dataResponse.nome;
    }

    $nomeField.val(nameFull);

    var nomePai = dataResponse.nome_pai;
    var nomeMae = dataResponse.nome_mae;
    var nomeResponsavel = dataResponse.nome_responsavel;

    if (dataResponse.pai_id) {
        pai_details.nome = nomePai;
        $j('#pai_nome').val(dataResponse.pai_id + ' - ' + nomePai);
        $j('#pai_id').val(dataResponse.pai_id);
    } else {
        $j('#pai_nome').val('');
        $j('#pai_id').val('');
    }

    $j('#pai_id').trigger('change');

    if (dataResponse.mae_id) {
        mae_details.nome = nomeMae;
        $j('#mae_nome').val(dataResponse.mae_id + ' - ' + nomeMae);
        $j('#mae_id').val(dataResponse.mae_id);
    } else {
        $j('#mae_nome').val('');
        $j('#mae_id').val('');
    }

    $j('#mae_id').trigger('change');


    if (dataResponse.responsavel_id) {
        $j('#responsavel_nome').val(dataResponse.responsavel_id + ' - ' + nomeResponsavel);
        $j('#responsavel_id').val(dataResponse.responsavel_id);
    } else {
        $j('#responsavel_nome').val('');
        $j('#responsavel_id').val('');
    }

    $j('#responsavel_id').trigger('change');

    if (dataResponse.responsavel_id) {
        nomeResponsavel = dataResponse.responsavel_id + ' - ' + nomeResponsavel;
    }

    $j('#data_nascimento').val(dataResponse.data_nascimento);
    $j('#rg').val(dataResponse.rg);

    $j('#orgao_emissao_rg').val(dataResponse.orgao_emissao_rg);
    $j('#uf_emissao_rg').val(dataResponse.uf_emissao_rg);

    $j('#responsavel_nome').val(nomeResponsavel);
    $j('#responsavel_id').val(dataResponse.responsavel_id);

    $j('#religiao_id').val(dataResponse.religiao_id);

    // deficiencias
    $deficiencias = $j('#deficiencias');

    $j.each(dataResponse.deficiencias, function (id, nome) {
        $deficiencias.children("[value=" + id + "]").attr('selected', '');
    });

    $deficiencias.trigger('chosen:updated');

    $j('#tipo_responsavel').find('option').remove().end();

    if ($j('#pai').val() == '' && $j('#mae').val() == '') {
        $j('#tipo_responsavel').append('<option value="outra_pessoa" selected >Outra pessoa</option>');
        $j('#responsavel_nome').show();
        $j('#cadastrar-pessoa-responsavel-link').show();
    } else if ($j('#pai').val() == '') {
        $j('#tipo_responsavel').append('<option value="mae" selected >M&atilde;e</option>');
        $j('#tipo_responsavel').append('<option value="outra_pessoa" >Outra pessoa</option>');
    } else if ($j('#mae').val() == '') {
        $j('#tipo_responsavel').append('<option value="pai" selected >Pai</option>');
        $j('#tipo_responsavel').append('<option value="outra_pessoa" >Outra pessoa</option>');
    } else {
        $j('#tipo_responsavel').append('<option value="mae" selected >M&atilde;e</option>');
        $j('#tipo_responsavel').append('<option value="pai" selected >Pai</option>');
        $j('#tipo_responsavel').append('<option value="pai_mae" >Pai e M&atilde;e</option>');
        $j('#tipo_responsavel').append('<option value="outra_pessoa" >Outra pessoa</option>');
    }

    $j('#tipo_responsavel').val(tipo_resp).change();

    var validaRg = function () {
        var rg = $j('#rg').val().replace(" ", "");
        var dataEmissao = $j('#data_emissao_rg').val().replace(" ", "");
    };

    $j('#rg').change(function () {
        validaRg();
    });

    $j('#data_emissao_rg').change(function () {
        validaRg();
    });

    var cpf = dataResponse.cpf;

    var mascara = null;

    if (cpf) {
        mascara = cpf.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    }

    $j('#id_federal').val(mascara);
    $j('#data_emissao_rg').val(dataResponse.data_emissao_rg);

    // # TODO show aluno photo
    //$j('#aluno_foto').val(dataResponse.url_foto);
    canShowParentsFields();
}

var handleGetPersonParentDetails = function (dataResponse, parentType) {
    window[parentType + '_details'] = dataResponse;

    if (dataResponse.id) {
        if (parentType == 'mae') {
            $maeNomeField.val(dataResponse.id + ' - ' + dataResponse.nome);
            $maeIdField.val(dataResponse.id);
            changeVisibilityOfLinksToPessoaMae();
        } else if (parentType == 'responsavel') {
            $responsavelNomeField.val(dataResponse.id + ' - ' + dataResponse.nome);
            $responsavelIdField.val(dataResponse.id);
            changeVisibilityOfLinksToPessoaResponsavel();
        } else {
            $paiNomeField.val(dataResponse.id + ' - ' + dataResponse.nome);
            $paiIdField.val(dataResponse.id);
            changeVisibilityOfLinksToPessoaPai();
        }
    }
};

var getPersonDetails = function (personId) {
    var additionalVars = {
        id: personId,
    };

    var options = {
        url: getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', additionalVars),
        dataType: 'json',
        data: {},
        success: handleGetPersonDetails
    };
    getResource(options);
};

var getPersonParentDetails = function (personId, parentType) {
    var additionalVars = {
        id: personId
    };

    var options = {
        url: getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa-parent', additionalVars),
        dataType: 'json',
        data: {},
        success: function (data) {
            handleGetPersonParentDetails(data, parentType)
        }
    };

    getResource(options);
};

var updatePersonDetails = function () {
    canShowParentsFields();

    if ($j('#pessoa_nome').val() && $j('#pessoa_id').val()) {
        getPersonDetails($j('#pessoa_id').val());
    } else {
        clearPersonDetails();
    }
};

if ($j('#person').val() && !$j('#pessoa_nome').val() && !$j('#pessoa_id').val()) {
    getPersonDetails($j('#person').val());
}

var clearPersonDetails = function () {
    $j('#pessoa_id').val('');
    $j('#pai').val('');
    $j('#mae').val('');
    $j('.pessoa-links .editar-pessoa').hide();
};

// simple search options
var simpleSearchPessoaOptions = {
    autocompleteOptions: {close: updatePersonDetails /*, change : updatePersonDetails*/}
};

// children callbacks
function pegaDominio() {
    var url = location.href; //pega endereço que esta no navegador
    url = url.split("/"); //quebra o endereço de acordo com a / (barra)
    return (url[2]); // retorna a parte www.endereco.com.br
}

function afterChangePessoa(targetWindow, parentType, parentId, parentName) {
    if (targetWindow != null) {
        targetWindow.close();

        if (parentType == null) {
            dominio = pegaDominio();
            url = $j('#id').val() ? location.origin + '/module/Cadastro/inscrito?id=' + $j('#id').val() : location.origin + '/module/Cadastro/inscrito?person=' + parentId;
            setTimeout("document.location = url", 5);
        }
    }

    var $tempIdField;
    var $tempNomeField;

    if (parentType) {
        $tempIdField = $j(buildId(parentType + '_id'));
        $tempNomeField = $j(buildId(parentType + '_nome'));
    } else {
        $tempIdField = $j('pessoa_id');
        $tempNomeField = $nomeField;
    }

    //timeout para usuario perceber mudança
    if (targetWindow == null || parentType != null) {
        window.setTimeout(function () {
            messageUtils.success('Pessoa alterada com sucesso', $tempNomeField);

            $tempIdField.val(parentId);

            if (!parentType) {
                getPersonDetails(parentId);
            } else {
                $tempNomeField.val(parentId + ' - ' + parentName);
            }

            if ($tempNomeField.is(':active')) {
                $tempNomeField.focus();
            }

            changeVisibilityOfLinksToPessoaParent(parentType);

        }, 500);
    }
}

function afterChangePessoaParent(pessoaId, parentType) {
    $tempField = $paiNomeField;
    var $parente = '';

    switch (parentType) {
        case 'mae':
            $tempField = $maeNomeField;
            $parente = 'm\u00e3e';
            break;
        case 'responsavel':
            $tempField = $responsavelNomeField;
            $parente = 'respons\u00e1vel';
            break;
        default:
            $tempField = $paiNomeField;
            $parente = 'pai';
    }

    if (editar_pessoa) {
        messageUtils.success('Pessoa ' + $parente + ' alterada com sucesso', $tempField);
    } else {
        messageUtils.success('Pessoa ' + $parente + ' cadastrada com sucesso', $tempField);
    }

    getPersonParentDetails(pessoaId, parentType);

    if ($tempField.is(':active')) {
        $tempField.focus();
    }
}

function canShowParentsFields() {
    if ($j('#pessoa_id').val()) {
        $paiNomeField.removeAttr('disabled');
        $maeNomeField.removeAttr('disabled');
    } else {
        $paiNomeField.attr('disabled', 'true');
        $maeNomeField.attr('disabled', 'true');
    }
}

// when page is ready
(function ($) {
    $(document).ready(function () {

        function currentDate() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();

            if (dd < 10) {
                dd = '0' + dd
            }

            if (mm < 10) {
                mm = '0' + mm
            }

            return dd + '/' + mm + '/' + yyyy;
        }

        $j('#documento').on('change', prepareUploadDocumento);

        $j('#deficiencias').trigger('chosen:updated');

        function prepareUploadDocumento(event) {
            $j('#documento').removeClass('error');
            uploadFilesDocumento(event.target.files);
        }

        function uploadFilesDocumento(files) {
            if (files && files.length > 0) {
                $j('#documento').attr('disabled', 'disabled');
                $j('#btn_enviar').attr('disabled', 'disabled').val('Aguarde...');
                $loadingDocumento.show();
                messageUtils.notice('Carregando documento...');

                var data = new FormData();
                $j.each(files, function (key, value) {
                    data.append(key, value);
                });

                $j.ajax({
                    url: '/intranet/upload.php?files',
                    type: 'POST',
                    data: data,
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (dataResponse) {
                        if (dataResponse.error) {
                            $j('#documento').val("").addClass('error');
                            messageUtils.error(dataResponse.error);
                        } else {
                            messageUtils.success('Documento carregado com sucesso');
                            $j('#documento').addClass('success');
                            addDocumento(dataResponse.file_url, currentDate());
                        }

                    },
                    error: function () {
                        $j('#documento').val("").addClass('error');
                        messageUtils.error('Não foi possível enviar o arquivo');
                    },
                    complete: function () {
                        $j('#documento').removeAttr('disabled');
                        $loadingDocumento.hide();
                        $j('#btn_enviar').removeAttr('disabled').val('Gravar');
                    }
                });
            }
        }

        canShowParentsFields();

        var $pessoaActionBar = $j('<span>').html('')
            .addClass('pessoa-links')
            .width($nomeField.outerWidth() - 12)
            .appendTo($nomeField.parent());

        $j('<a>').hide()
            .addClass('cadastrar-pessoa decorated')
            .attr('id', 'cadastrar-pessoa-link')
            //.attr('href', '/intranet/atendidos_cad.php')
            //.attr('target', '_blank')
            .html('Cadastrar pessoa')
            .appendTo($pessoaActionBar);

        $j('<a>').hide()
            .addClass('editar-pessoa decorated')
            .attr('id', 'editar-pessoa-link')
            //.attr('href', '#')
            //.attr('target', '_blank')
            .html('Editar pessoa')
            .appendTo($pessoaActionBar);

        if (resource.isNew()) {
            $nomeField.focus();
            $j('.pessoa-links .cadastrar-pessoa').show().css('display', 'inline');
        }
        else
            $nomeField.attr('disabled', 'disabled');

        // responsavel

        var checkTipoResponsavel = function () {
            if ($j('#tipo_responsavel').val() == 'outra_pessoa') {
                $j('#responsavel_nome').show();
                $j('#cadastrar-pessoa-responsavel-link').show();
            } else {
                $j('#responsavel_nome').hide();
                $j('#cadastrar-pessoa-responsavel-link').hide();
            }
        };

        checkTipoResponsavel();
        $j('#tipo_responsavel').change(checkTipoResponsavel);


        /***********************
         EVENTOS DE CLICK EM ABAS
         ************************/
        // DADOS PESSOAIS
        $j('#tab1').click(
            function () {

                $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
                $j('#tab1').toggleClass('alunoTab alunoTab-active')
                $j('.tablecadastro >tbody  > tr').each(function (index, row) {
                    if (index > $j('#tr_encaminhamento').index() - 1) {
                        if (row.id != 'stop')
                            row.hide();
                        else
                            return false;
                    } else {
                        row.show();
                    }
                });
            }
        );

        var first_click_documentos = true;
        // FICHA MÉDICA
        $j('#tab2').click(
            function () {
                $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
                $j('#tab2').toggleClass('alunoTab alunoTab-active')
                $j('.tablecadastro >tbody  > tr').each(function (index, row) {
                    if (row.id != 'stop') {
                        if (index >= $j('#tr_encaminhamento').index() && index < $j('#tr_copia_renda').index() + 1) {
                            row.show();
                        } else if (index != 0) {
                            row.hide();
                        }
                    } else {
                        return false;
                    }
                });

                // Esse loop desativa/ativa os campos de descrição, conforme os checkbox
                $j('.temDescricao').each(function (i, obj) {
                    $j('#desc_' + obj.id).prop('disabled', !$j('#' + obj.id).prop('checked'));
                });

                first_click_documentos = false;
            }
        );

        /* A seguinte função habilitam/desabilitam o campo de descrição quando for clicado
        nos referentes checkboxs */

        $j('.temDescricao').click(function () {
            if ($j('#' + this.id).prop('checked'))
                $j('#desc_' + this.id).removeAttr('disabled');
            else {
                $j('#desc_' + this.id).attr('disabled', 'disabled');
                $j('#desc_' + this.id).val('');
            }
        });

        // MODAL pessoa-aluno


        //  Esse simplesSearch é carregado no final do arquivo, então a sua linha deve ser escondida,
        // é só campo será 'puxado' para a modal
        $j('#municipio_pessoa-aluno').closest('tr').hide();

        $j('body').append(`
          <div id="dialog-form-pessoa-aluno">
            <form>
              <h2></h2>
              <table>
                <tr>
                  <td valign="top">
                    <fieldset>
                      <legend>Dados b&aacute;sicos</legend>
                      <label for="nome-pessoa-aluno">Nome<span class="campo_obrigatorio">*</span> </label>
                      <input type="text" name="nome-pessoa-aluno" id="nome-pessoa-aluno" size="49" maxlength="255" class="text">
                      <label for="nome-social-pessoa-aluno">Nome social</label>
                      <input type="text" name="nome-social-pessoa-aluno" id="nome-social-pessoa-aluno" size="49" maxlength="255" class="text">
                      <label for="sexo-pessoa-aluno">Sexo<span class="campo_obrigatorio">*</span> </label>
                      <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-aluno" id="sexo-pessoa-aluno">
                        <option value="" selected>Sexo</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                      </select>
                      <label for="estado-civil-pessoa-aluno">Estado civil<span class="campo_obrigatorio">*</span> </label>
                      <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-aluno" id="estado-civil-pessoa-aluno">
                        <option id="estado-civil-pessoa-aluno_" value="" selected>Estado civil</option>
                        <option id="estado-civil-pessoa-aluno_2" value="2">Casado(a)</option>
                        <option id="estado-civil-pessoa-aluno_6" value="6">Companheiro(a)</option>
                        <option id="estado-civil-pessoa-aluno_3" value="3">Divorciado(a)</option>
                        <option id="estado-civil-pessoa-aluno_4" value="4">Separado(a)</option>
                        <option id="estado-civil-pessoa-aluno_1" value="1">Solteiro(a)</option>
                        <option id="estado-civil-pessoa-aluno_5" value="5">Vi&uacute;vo(a)</option>
                      </select>
                      <label for="data-nasc-pessoa-aluno"> Data de nascimento<span class="campo_obrigatorio">*</span> </label>
                      <input onKeyPress="formataData(this, event);" class="" placeholder="dd/mm/yyyy" type="text" name="data-nasc-pessoa-aluno" id="data-nasc-pessoa-aluno" value="" size="11" maxlength="10">
                      <label id="telefone_fixo_dois" style="display: inline;">Telefone</label>
                      <input placeholder="ddd" type="text" name="ddd_telefone_fixo" id="ddd_telefone_fixo" size="3" maxlength="3" style="display: inline;" />
                      <input placeholder="n\u00famero" type="text" name="telefone_fixo" id="telefone_fixo" size="9" maxlength="9" style="display: inline;" />
                      <label style="display: inline;" id="telefone_cel_dois">Celular</label>
                      <input placeholder="ddd" type="text " name="ddd_telefone_cel" id="ddd_telefone_cel" size="3" maxlength="3" style="display: inline; padding: 4px 6px;">
                      <input placeholder="n\u00famero" type="text " name="telefone_cel" id="telefone_cel" size="9" maxlength="9" style="display: inline; padding: 4px 6px;">
                      <label style="display: inline;" id="telefone_whatsapp_dois">Whatsapp</label>
                      <input placeholder="ddd" type="text " name="ddd_telefone_whatsapp" id="ddd_telefone_whatsapp" size="3" maxlength="3" style="display: inline; padding: 4px 6px;">
                      <input placeholder="n\u00famero" type="text " name="telefone_whatsapp" id="telefone_whatsapp" size="9" maxlength="9" style="display: inline; padding: 4px 6px;">
                      <label style="display: block;" for="naturalidade_pessoa-aluno"> Naturalidade<span class="campo_obrigatorio">*</span> </label>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset valign="top">
                      <legend>Dados do endere&ccedil;o</legend>
                      <table></table>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset>
                      <table></table>
                    </fieldset>
                  </td>
                </tr>
              </table>
              <p><a id="link_cadastro_detalhado" target="_blank">Cadastro detalhado</a></p>
            </form>
          </div>

        `);

        var name = $j("#nome-pessoa-aluno"),
            nome_social = $j("#nome-social-pessoa-aluno"),
            sexo = $j("#sexo-pessoa-aluno"),
            estadocivil = $j("#estado-civil-pessoa-aluno"),
            datanasc = $j("#data-nasc-pessoa-aluno"),
            municipio = $j("#naturalidade_aluno_pessoa-aluno"),
            municipio_id = $j("#naturalidade_aluno_id"),
            telefone_1 = $j("#telefone_fixo"),
            telefone_mov = $j("#telefone_cel"),
            telefone_whatsapp = $j("#telefone_whatsapp"),
            ddd_telefone_1 = $j("#ddd_telefone_fixo"),
            ddd_telefone_mov = $j("#ddd_telefone_cel"),
            ddd_telefone_whatsapp = $j("#ddd_telefone_whatsapp"),
            complemento = $j("#complemento"),
            numero = $j("#numero"),
            letra = $j("#letra"),
            apartamento = $j("#apartamento"),
            bloco = $j("#bloco"),
            andar = $j("#andar"),
            allFields = $j([]).add(name).add(nome_social).add(sexo).add(estadocivil).add(datanasc).add(municipio).add(ddd_telefone_1).add(telefone_1).add(ddd_telefone_mov).add(telefone_mov).add(ddd_telefone_whatsapp).add(telefone_whatsapp).add(municipio_id).add(complemento).add(numero).add(letra).add(apartamento).add(bloco).add(andar);

        municipio.show().toggleClass('geral text').attr('display', 'block').appendTo('#dialog-form-pessoa-aluno tr td:first-child fieldset');

        $j('<label>').html('CEP').attr('for', 'cep_').insertBefore($j('#cep_'));
        $j('#cep_').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('Munic&iacute;pio').attr('for', 'municipio_municipio').insertBefore($j('#municipio_municipio'));
        $j('#municipio_municipio').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('Distrito').attr('for', 'distrito_distrito').insertBefore($j('#distrito_distrito'));
        $j('#distrito_distrito').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('Logradouro').attr('for', 'logradouro_logradouro').insertBefore($j('#logradouro_logradouro'));
        $j('#logradouro_logradouro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('Tipo de logradouro').attr('for', 'idtlog').insertBefore($j('#idtlog'));
        $j('#idtlog').toggleClass('geral text');
        $j('<label>').html('Logradouro').attr('for', 'logradouro').insertBefore($j('#logradouro'));
        $j('#logradouro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('Bairro').attr('for', 'bairro_bairro').insertBefore($j('#bairro_bairro'));
        $j('#bairro_bairro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('Zona de localiza&ccedil;&atilde;o').attr('for', 'zona_localizacao').insertBefore($j('#zona_localizacao'));
        $j('#zona_localizacao').toggleClass('geral text');
        $j('<label>').html('Bairro').attr('for', 'bairro').insertBefore($j('#bairro'));
        $j('#bairro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();

        let $label = $j('<label>').html('Zona localização').attr('for', 'zona_localizacao_censo').insertBefore($j('#zona_localizacao_censo'));
        if ($j('#zona_localizacao_censo').hasClass('obrigatorio')) {
          $label.append($j('<span/>').addClass('campo_obrigatorio').text('*'));
        }
        $j('#zona_localizacao_censo').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();

        $label = $j('<label>').html('Nacionalidade').attr('for', 'tipo_nacionalidade').attr('style', 'display:block;').insertBefore($j('#tipo_nacionalidade'));
        $j('#tipo_nacionalidade').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().insertAfter('#cor_raca').find('td').removeClass();
        $j('#tipo_nacionalidade').unwrap().unwrap().unwrap();
        if ($j('#tipo_nacionalidade').hasClass('obrigatorio')) {
          $label.append($j('<span/>').addClass('campo_obrigatorio').text('*'));
        }

        let checkTipoNacionalidade = () => {
          if ($j.inArray($j('#tipo_nacionalidade').val(), ['2', '3']) > -1) {
            $j('#pais_origem_nome').show();
          } else {
            $j('#pais_origem_nome').hide();
          }
        }
        $j('#tipo_nacionalidade').change(checkTipoNacionalidade);

        $j('<label>').html('Complemento').attr('for', 'complemento').insertBefore($j('#complemento'));
        $j('#complemento').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
        $j('<label>').html('N&uacute;mero').attr('for', 'numero').insertBefore($j('#numero'));
        $j('#numero').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(3) fieldset table').find('td').removeClass();
        $j('<label>').html('Letra').attr('for', 'letra').insertBefore($j('#letra'));
        $j('#letra').toggleClass('geral text');
        $j('<label>').html('N&ordm; de apartamento').attr('for', 'apartamento').insertBefore($j('#apartamento'));
        $j('#apartamento').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(3) fieldset table').find('td').removeClass();
        $j('<label>').html('Bloco').attr('for', 'bloco').insertBefore($j('#bloco'));
        $j('#bloco').toggleClass('geral text');
        $j('<label>').html('Andar').attr('for', 'andar').insertBefore($j('#andar'));
        $j('#andar').toggleClass('geral text');

        $j('#dialog-form-pessoa-aluno').find(':input').css('display', 'block');
        $j('#cep_').css('display', 'inline');
        $j('#ddd_telefone_fixo').css('display', 'inline');
        $j('#telefone_fixo').css('display', 'inline');
        $j('#ddd_telefone_cel').css('display', 'inline');
        $j('#ddd_telefone_whatsapp').css('display', 'inline');
        $j('#telefone_whatsapp').css('display', 'inline');
        $j('#telefone_cel').css('display', 'inline');
        $j('#telefone_fixo_dois').css('display', 'block');
        $j('#telefone_cel_dois').css('display', 'block');
        $j('#telefone_whatsapp_dois').css('display', 'block');


        $j("#dialog-form-pessoa-aluno").dialog({
            autoOpen: false,
            height: 'auto',
            width: 'auto',
            modal: true,
            resizable: false,
            draggable: false,
            buttons: {
                "Gravar": function () {
                    var bValid = true;
                    allFields.removeClass("error");
                    // $j( this ).addClass('btn-green');
                    // console.log($j(this))

                    bValid = bValid && checkLength(name, "nome", 3, 255);
                    bValid = bValid && checkSelect(sexo, "sexo");
                    bValid = bValid && checkSelect(estadocivil, "estado civil");
                    bValid = bValid && checkRegexp(datanasc, /(^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$)/i, "O campo data de nascimento deve ser preenchido no formato dd/mm/yyyy.");
                    bValid = bValid && checkSimpleSearch(municipio, municipio_id, "munic\u00edpio");
                    bValid = bValid && ($j('#cep_').val() == '' ? true : validateEndereco());

                    if ($j('#zona_localizacao_censo').hasClass('obrigatorio')) {
                      bValid = bValid && checkSelect($j('#zona_localizacao_censo'), "zona localização");
                    }
                    if ($j('#cor_raca').hasClass('obrigatorio')) {
                      bValid = bValid && checkSelect($j('#cor_raca'), "raça");
                    }
                    if ($j('#tipo_nacionalidade').hasClass('obrigatorio')) {
                      bValid = bValid && checkSelect($j('#tipo_nacionalidade'), "nacionalidade");
                    }
                    if ($j('#pais_origem_id').hasClass('obrigatorio') && $j('#pais_origem_nome').is(':visible')) {
                      bValid = bValid && checkSimpleSearch($j('#pais_origem_nome'), $j('#pais_origem_id'), "pais de origem");
                    }

                    if (bValid) {
                        postPessoa($j('#pessoa_nome'), name.val(), sexo.val(), estadocivil.val(), datanasc.val(), municipio_id.val(), (editar_pessoa ? $j('#pessoa_id').val() : null), null, ddd_telefone_1.val(), telefone_1.val(), ddd_telefone_mov.val(), telefone_mov.val(), ddd_telefone_whatsapp.val(), telefone_whatsapp.val(), undefined,
                          $j('#tipo_nacionalidade').val(), $j('#pais_origem_id').val(), $j('#zona_localizacao_censo').val(), nome_social.val());
                        $j(this).dialog("close");
                    }
                },
                "Cancelar": function () {
                    $j(this).dialog("close");
                }
            },
            create: function () {
                $j(this).closest(".ui-dialog")
                    .find(".ui-button-text:first")
                    .addClass("btn-green");
            },
            close: function () {

                allFields.val("").removeClass("error");

            },
            hide: {
                effect: "clip",
                duration: 500
            },
            show: {
                effect: "clip",
                duration: 500
            }
        });

        $j('body').append('<div id="dialog-form-pessoa-parent"><form><h2></h2><table><tr><td valign="top"><fieldset><label for="nome-pessoa-parent">Nome</label>    <input type="text " name="nome-pessoa-parent" id="nome-pessoa-parent" size="49" maxlength="255" class="text">    <label for="sexo-pessoa-parent">Sexo</label>  <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-parent" id="sexo-pessoa-parent" ><option value="" selected>Sexo</option><option value="M">Masculino</option><option value="F">Feminino</option></select>    <label for="estado-civil-pessoa-parent">Estado civil</label>   <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-parent" id="estado-civil-pessoa-parent"  ><option id="estado-civil-pessoa-parent_" value="" selected>Estado civil</option><option id="estado-civil-pessoa-parent_2" value="2">Casado(a)</option><option id="estado-civil-pessoa-parent_6" value="6">Companheiro(a)</option><option id="estado-civil-pessoa-parent_3" value="3">Divorciado(a)</option><option id="estado-civil-pessoa-parent_4" value="4">Separado(a)</option><option id="estado-civil-pessoa-parent_1" value="1">Solteiro(a)</option><option id="estado-civil-pessoa-parent_5" value="5">Vi&uacute;vo(a)</option></select><label for="data-nasc-pessoa-parent"> Data de nascimento </label> <input onKeyPress="formataData(this, event);" class="" placeholder="dd/mm/yyyy" type="text" name="data-nasc-pessoa-parent" id="data-nasc-pessoa-parent" value="" size="11" maxlength="10"> <div id="falecido-modal"> <label>Falecido?</label><input type="checkbox" name="falecido-parent" id="falecido-parent" style="display:inline;"> </div></fieldset><p><a id="link_cadastro_detalhado_parent" target="_blank">Cadastro detalhado</a></p></form></div>');

        $j('#dialog-form-pessoa-parent').find(':input').css('display', 'block');

        var nameParent = $j("#nome-pessoa-parent"),
            sexoParent = $j("#sexo-pessoa-parent"),
            estadocivilParent = $j("#estado-civil-pessoa-parent"),
            datanascParent = $j("#data-nasc-pessoa-parent"),
            falecidoParent = $j("#falecido-parent"),
            allFields = $j([]).add(nameParent).add(sexoParent).add(estadocivilParent).add(datanascParent).add(falecidoParent);

        $j("#dialog-form-pessoa-parent").dialog({
            autoOpen: false,
            height: 'auto',
            width: 'auto',
            modal: true,
            resizable: false,
            draggable: false,
            title: "teste",
            buttons: {
                "Gravar": function () {
                    var bValid = true;
                    allFields.removeClass("ui-state-error");

                    bValid = bValid && checkLength(nameParent, "nome", 3, 255);
                    bValid = bValid && checkSelect(sexoParent, "sexo");
                    bValid = bValid && checkSelect(estadocivilParent, "estado civil");

                    if ($j("#data-nasc-pessoa-parent").val() != '') {
                        bValid = bValid && checkRegexp(datanascParent, /(^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$)/i, "O campo data de nascimento deve ser preenchido no formato dd/mm/yyyy.");
                    }


                    if (bValid) {
                        postPessoa(nameParent, nameParent.val(), sexoParent.val(), estadocivilParent.val(), datanascParent.val(), null, (editar_pessoa ? $j('#' + pessoaPaiOuMae + '_id').val() : null), pessoaPaiOuMae, null, null, null, null, null, null, falecidoParent.is(':checked'));
                        $j(this).dialog("close");
                    }
                },
                "Cancelar": function () {

                    $j(this).dialog("close");
                }
            },
            create: function () {
                $j(this)
                    .closest(".ui-dialog")
                    .find(".ui-button-text:first")
                    .addClass("btn-green");
            },
            close: function () {
                allFields.val("").removeClass("error");
            },
            hide: {
                effect: "clip",
                duration: 500
            },
            show: {
                effect: "clip",
                duration: 500
            }
        });

        $j('#link_cadastro_detalhado').click(function () {
            $j("#dialog-form-pessoa-aluno").dialog("close");
        });

        $j('#link_cadastro_detalhado_parent').click(function () {
            $j("#dialog-form-pessoa-parent").dialog("close");
        });

        $j("#cadastrar-pessoa-link").click(function () {
            $j('#link_cadastro_detalhado').attr('href', '/intranet/atendidos_cad.php');
            $j("#dialog-form-pessoa-aluno").dialog("open");
            $j('#cep_').val('');
            clearEnderecoFields();
            hideEnderecoFields();
            permiteEditarEndereco();
            checkTipoNacionalidade();

            $j(".ui-widget-overlay").click(function () {
                $j(".ui-dialog-titlebar-close").trigger('click');
            });

            $j('#nome-pessoa-aluno').focus();

            $j('#dialog-form-pessoa-aluno form h2:first-child').html('Cadastrar pessoa aluno').css('margin-left', '0.75em');
            editar_pessoa = false;
        });

        $j("#editar-pessoa-link").click(function () {
            $j('#link_cadastro_detalhado').attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + person_details.id);
            clearEnderecoFields();

            name.val(person_details.nome);
            nome_social.val(person_details.nome_social);
            datanasc.val(person_details.data_nascimento);
            estadocivil.val(person_details.estadocivil);
            sexo.val(person_details.sexo);

            if (person_details.idmun_nascimento) {
                $j('#naturalidade_aluno_id').val(person_details.idmun_nascimento);
                $j('#naturalidade_aluno_pessoa-aluno').val(person_details.idmun_nascimento + ' - ' + person_details.municipio_nascimento + ' (' + person_details.sigla_uf_nascimento + ')');
            }

            $j('#zona_localizacao_censo').val(person_details.zona_localizacao_censo);
            $j('#tipo_nacionalidade').val(person_details.tipo_nacionalidade);
            if (person_details.pais_origem_id) {
              $j('#pais_origem_id').val(person_details.pais_origem_id);
              $j('#pais_origem_nome').val(`${person_details.pais_origem_id} - ${person_details.pais_origem_nome}`);
            } else {
              $j('#pais_origem_id').val('');
              $j('#pais_origem_nome').val('');
            }

            $j('#cep_').val(person_details.cep);
            $j('#ddd_telefone_fixo').val(person_details.ddd_fone_fixo);
            $j('#telefone_fixo').val(person_details.fone_fixo);
            $j('#ddd_telefone_cel').val(person_details.ddd_fone_mov);
            $j('#ddd_telefone_whatsapp').val(person_details.ddd_fone_whatsapp);
            $j('#telefone_cel').val(person_details.fone_mov);
            $j('#telefone_whatsapp').val(person_details.fone_whatsapp);
            $j('#distrito_id').val(person_details.iddis);

            if ($j('#cep_').val()) {
                $j('#municipio_municipio').removeAttr('disabled');
                $j('#distrito_distrito').removeAttr('disabled');
                $j('#bairro_bairro').removeAttr('disabled');
                $j('#logradouro_logradouro').removeAttr('disabled');
                $j('#bairro').removeAttr('disabled');
                $j('#zona_localizacao').removeAttr('disabled');
                $j('#idtlog').removeAttr('disabled');
                $j('#logradouro').removeAttr('disabled');
                $j('#complemento').val(person_details.complemento);
                $j('#numero').val(person_details.numero);
                $j('#letra').val(person_details.letra);
                $j('#apartamento').val(person_details.apartamento);
                $j('#bloco').val(person_details.bloco);
                $j('#andar').val(person_details.andar);

                $j('#municipio_id').val(person_details.idmun);

                $j('#municipio_municipio').val(person_details.idmun + ' - ' + person_details.municipio + ' (' + person_details.sigla_uf + ')');
                $j('#distrito_distrito').val(person_details.iddis + ' - ' + person_details.distrito);

                if (person_details.idbai && person_details.idlog) {
                    var params = $j('#id').val();
                    $j.get('/module/Api/aluno?&oper=get&resource=get-nome-bairro&id=' + params, function (data) {
                        $j('#bairro_bairro').empty();
                        $j('#bairro_bairro').val(data[0]['nome'] + ' / Zona ' + (person_details.zona_localizacao == "1" ? "Urbana" : "Rural"));
                    });

                    $j('#bairro_id').val(person_details.idbai);
                    $j('#logradouro_id').val(person_details.idlog);
                    $j('#logradouro_logradouro').val($j("#idtlog option[value='" + person_details.idtlog + "']").text() + ' ' + person_details.logradouro);
                } else {
                    $j('#bairro').val(person_details.bairro);
                    $j('#logradouro').val(person_details.logradouro);
                    $j('#idtlog').val(person_details.idtlog);
                    $j('#zona_localizacao').val(person_details.zona_localizacao);
                }
            }

            hideEnderecoFields();

            $j("#dialog-form-pessoa-aluno").dialog("open");

            $j(".ui-widget-overlay").click(function () {
                $j(".ui-dialog-titlebar-close").trigger('click');
            });

            $j('#nome-pessoa-aluno').focus();

            $j('#dialog-form-pessoa-aluno form h2:first-child').html('Editar pessoa aluno').css('margin-left', '0.75em');

            editar_pessoa = true;

            permiteEditarEndereco();
            checkTipoNacionalidade();
        });

        $j("#cadastrar-pessoa-pai-link").click(function () {
            if ($j('#pessoa_id').val()) {
                openModalParent('pai');
            } else {
                alertSelecionarPessoaAluno();
            }
        });


        $j("#cadastrar-pessoa-mae-link").click(function () {
            if ($j('#pessoa_id').val()) {
                openModalParent('mae');
            } else {
                alertSelecionarPessoaAluno();
            }
        });

        $j("#cadastrar-pessoa-responsavel-link").click(function () {
            if ($j('#pessoa_id').val()) {
                openModalParent('responsavel');
            } else {
                alertSelecionarPessoaAluno();
            }
        });

        $j("#editar-pessoa-pai-link").click(function () {
            if ($j('#pessoa_id').val()) {
                openEditModalParent('pai');
            }
        });


        $j("#editar-pessoa-mae-link").click(function () {
            if ($j('#pessoa_id').val()) {
                openEditModalParent('mae');
            }
        });

        $j("#editar-pessoa-responsavel-link").click(function () {
            if ($j('#pessoa_id').val()) {
                openEditModalParent('responsavel');
            }
        });

        function alertSelecionarPessoaAluno() {
            messageUtils.error('Primeiro cadastre/selecione uma pessoa para o aluno. ');
        }

        function openModalParent(parentType) {
            $j('#link_cadastro_detalhado_parent').attr('href', '/intranet/atendidos_cad.php?parent_type=' + parentType);
            $j("#dialog-form-pessoa-parent").dialog("open");
            $j(".ui-widget-overlay").click(function () {
               $j(".ui-dialog-titlebar-close").trigger('click');
            });
            $j('#nome-pessoa-parent').focus();
            $j('#falecido-parent').attr('checked', false);

            var tipoPessoa = 'pai';

            switch (parentType) {
                case 'mae':
                    tipoPessoa = 'mãe';
                    break;
                case 'responsavel':
                    tipoPessoa = 'responsável';
                    break;
                default:
                    tipoPessoa = 'pai';
            }

            if (parentType == 'responsavel') {
                $j('#falecido-modal').hide();
            } else {
                $j('#falecido-modal').show();
            }

            $j('#dialog-form-pessoa-parent form h2:first-child').html('Cadastrar pessoa ' + tipoPessoa).css('margin-left', '0.75em');

            pessoaPaiOuMae = parentType;
            editar_pessoa = false;
        }

        function openEditModalParent(parentType) {
            $j('#link_cadastro_detalhado_parent').attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + $j('#' + parentType + '_id').val() + '&parent_type=' + parentType);
            $j("#dialog-form-pessoa-parent").dialog("open");
            $j(".ui-widget-overlay").click(function () {
                $j(".ui-dialog-titlebar-close").trigger('click');
            });
            $j('#nome-pessoa-parent').focus();

            nameParent.val(window[parentType + '_details'].nome);
            estadocivilParent.val(window[parentType + '_details'].estadocivil);
            sexoParent.val(window[parentType + '_details'].sexo);
            datanascParent.val(window[parentType + '_details'].data_nascimento);
            // console.log(window[parentType+'_details'].falecido);
            falecidoParent.prop('checked', (window[parentType + '_details'].falecido));

            if (parentType == 'responsavel') {
                $j('#falecido-modal').hide();
            } else {
                $j('#falecido-modal').show();
            }


            $j('#dialog-form-pessoa-parent form h2:first-child').html('Editar pessoa ' + (parentType == 'mae' ? 'm&atilde;e' : parentType)).css('margin-left', '0.75em');

            pessoaPaiOuMae = parentType;
            editar_pessoa = true;
        }

        function checkLength(o, n, min, max) {
            if (o.val().length > max || o.val().length < min) {
                o.addClass("error");

                messageUtils.error("Tamanho do " + n + " deve ter entre " +  min + " e " + max + " caracteres.");
                return false;
            } else {
                return true;
            }
        }

        function checkRegexp(o, regexp, n) {
            if (!( regexp.test(o.val()) )) {
                o.addClass("error");
                messageUtils.error(n);
                return false;
            } else {
                return true;
            }
        }

        function checkSelect(comp, name) {
            if (comp.val() == '') {
                comp.addClass("error");
                messageUtils.error("Selecione um(a) " + name + ".");
                return false;
            } else {
                return true;
            }
        }

        function checkSimpleSearch(comp, hiddenComp, name) {
            if (hiddenComp.val() == '') {
                comp.addClass("error");
                messageUtils.error("Selecione um(a) " + name + ".");
                return false;
            } else {
                return true;
            }
        }

        $j('#pai_id').change(function () {
            getPersonParentDetails($j(this).val(), 'pai')
        });

        $j('#mae_id').change(function () {
            getPersonParentDetails($j(this).val(), 'mae')
        });

        $j('#responsavel_id').change(function () {
            getPersonParentDetails($j(this).val(), 'responsavel')
        });

        $cpfField.focusout(function () {
            $j(document).removeData('submit_form_after_ajax_validation');
            validatesUniquenessOfCpf();
        });

        var validatesUniquenessOfCpf = function () {
            var cpf = $cpfField.val();

            $cpfNotice.hide();

            if (cpf && validatesCpf()) {
                getPersonByCpf(cpf);
            }
        };

        var handleGetPersonByCpf = function (dataResponse) {
            handleMessages(dataResponse.msgs);
            $cpfNotice.hide();

            var pessoaId = dataResponse.id;

            if (pessoaId && pessoaId != $j('#pessoa_id').val()) {
                $cpfNotice.html('CPF já utilizado pela pessoa código ' + pessoaId + ', ').slideDown('fast');

                $j('<a>')
                    .addClass('decorated')
                    .attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + pessoaId)
                    .attr('target', '_blank')
                    .html('acessar cadastro.')
                    .appendTo($cpfNotice);

                $j('body,html').animate({scrollTop: $j('body').offset().top}, 'fast');

                $submitButton.attr('disabled', 'disabled').hide();
            } else {
                $submitButton.removeAttr('disabled').show();
            }
        };

        var getPersonByCpf = function (cpf) {
            var options = {
                url: getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa'),
                dataType: 'json',
                data: {cpf: cpf},
                success: handleGetPersonByCpf,

                // forçado requisições sincronas, evitando erro com requisições ainda não concluidas,
                // como no caso, onde o usuário pressiona cancelar por exemplo.
                async: false
            };

            getResource(options);
        };

        var validatesCpf = function () {
            var valid = true;
            var cpf = $cpfField.val();

            $cpfNotice.hide();

            if (cpf && !validationUtils.validatesCpf(cpf)) {
                $cpfNotice.html('O CPF informado é inválido').slideDown('fast');

                //Esconde botão Gravar
                $submitButton.attr('disabled', 'disabled').hide();

                valid = false;
            }

            return valid;
        }

    }); // ready


    function postPessoa($pessoaField, nome, sexo, estadocivil, datanasc, naturalidade, pessoa_id, parentType, ddd_telefone_1, telefone_1, ddd_telefone_mov, telefone_mov, ddd_telefone_whatsapp, telefone_whatsapp, falecido,
      tipo_nacionalidade, pais_origem_id, cor_raca, zona_localizacao_censo, nome_social) {
        var data = {
            nome: nome,
            sexo: sexo,
            estadocivil: estadocivil,
            datanasc: datanasc,
            ddd_telefone_1: ddd_telefone_1,
            telefone_1: telefone_1,
            ddd_telefone_mov: ddd_telefone_mov,
            telefone_mov: telefone_mov,
            ddd_telefone_whatsapp: ddd_telefone_whatsapp,
            telefone_whatsapp: telefone_whatsapp,
            naturalidade: naturalidade,
            pessoa_id: pessoa_id,
            falecido: falecido,
            tipo_nacionalidade: tipo_nacionalidade,
            pais_origem_id: pais_origem_id,
            zona_localizacao_censo: zona_localizacao_censo,
            nome_social: nome_social
        };

        var options = {
            url: postResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', {}),
            dataType: 'json',
            data: data,
            success: function (dataResponse) {
                if (parentType == 'mae')
                    afterChangePessoaParent(dataResponse.pessoa_id, 'mae');
                else if (parentType == 'pai')
                    afterChangePessoaParent(dataResponse.pessoa_id, 'pai');
                else if (parentType == 'responsavel')
                    afterChangePessoaParent(dataResponse.pessoa_id, 'responsavel');
                else
                    postEnderecoPessoa(dataResponse.pessoa_id);
            }
        };

        postResource(options);
    }

    function postEnderecoPessoa(pessoa_id) {

        if (checkCepFields($j('#cep_').val())) {
            var data = {
                pessoa_id: pessoa_id,
                cep: $j('#cep_').val(),
                municipio_id: $j('#municipio_id').val(),
                distrito_id: $j('#distrito_id').val(),
                bairro: $j('#bairro').val(),
                bairro_id: $j('#bairro_id').val(),
                zona_localizacao: $j('#zona_localizacao').val(),
                logradouro: $j('#logradouro').val(),
                idtlog: $j('#idtlog').val(),
                logradouro_id: $j('#logradouro_id').val(),
                apartamento: $j('#apartamento').val(),
                complemento: $j('#complemento').val(),
                numero: $j('#numero').val(),
                letra: $j('#letra').val(),
                bloco: $j('#bloco').val(),
                andar: $j('#andar').val()
            };

            var options = {
                url: postResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa-endereco', {}),
                dataType: 'json',
                data: data,
                success: function (dataResponse) {
                    afterChangePessoa(null, null, pessoa_id);
                }
            };

            postResource(options);
        } else {
            afterChangePessoa(null, null, pessoa_id);
        }

    }

    $j('#beneficios_chzn ul').css('width', '307px');

    //gambiarra sinistra que funciona
    window.setTimeout(function () {
        $j('#btn_enviar').unbind().click(newSubmitForm)
    }, 500);
})(jQuery);

var handleSelect = function (event, ui) {
    $j(event.target).val(ui.item.label);
    return false;
};

var searchProjeto = function (request, response) {
    var searchPath = '/module/Api/Projeto?oper=get&resource=projeto-search';
    var params = {query: request.term};

    $j.get(searchPath, params, function (dataResponse) {
        simpleSearch.handleSearch(dataResponse, response);
    });
};

function setAutoComplete() {
    $j.each($j('input[id^="projeto_cod_projeto"]'), function (index, field) {
        $j(field).autocomplete({
            source: searchProjeto,
            select: handleSelect,
            minLength: 1,
            autoFocus: true
        });

    });
}

setAutoComplete();

var $addProjetoButton = $j('#btn_add_tab_add_2');

$addProjetoButton.click(function () {
    setAutoComplete();
});
