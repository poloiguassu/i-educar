@push('page_scripts')
    <style>
        .jexcel > div > table {
            width: 100% !important;
        }

        .jexcel > div > table > tbody > tr > td.readonly {
            color: #333 !important;
        }

        .jexcel > div.jexcel-content {
            padding-top: 38px;
        }

        .jexcel-header {
            position: fixed;
        }

        #feedback-messages {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0);
            z-index: 1050;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/Utils.js') }}'></script>
    <script src='{{ Asset::get('/modules/Portabilis/Assets/Javascripts/ClientApi.js') }}'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jexcel/2.0.2/js/jquery.jexcel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jexcel/2.0.2/js/jquery.jdropdown.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jexcel/2.0.2/css/jquery.jexcel.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jexcel/2.0.2/css/jquery.jdropdown.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jexcel/2.0.2/css/jquery.jexcel.bootstrap.min.css" type="text/css" />
@endpush

@php
    $header = json_encode($content['table_header'], JSON_UNESCAPED_SLASHES);
    $data = json_encode($content['table_lines'], JSON_UNESCAPED_SLASHES);
@endphp

@push('page_content')

    <div id="corpo" style="width: 100%;"></div>

    <script>
        data = {!! $data !!};
        header = {!! $header !!};
        meta = {!! $sheet_meta !!};

        documentos = [
            'rg',
            'cpf',
            'residencia',
            'historico',
            'renda'
        ];

        (function($) {
            $(document).ready(function() {
                $.noConflict();
                var handler = function(instance, cell, value) {
                    var cellChanged = $(instance).jexcel('getColumnNameFromId', $(cell).prop('id'));
                    var position = $(cell).prop('id').split('-');
                    if(position[0] == 3) {
                        var data = {
                            id: meta[position[1]],
                            etapa_id: 1,
                            situacao: value
                        };

                        var options = {
                            url: postResourceUrlBuilder.buildUrl('/module/Api/inscrito', 'inscrito-etapa', {}),
                            dataType: 'json',
                            data: data,

                            success: function (dataResponse) {
                                messageUtils.success('Situação etapa 1 alterada com sucesso.');
                            }
                        };
                        postResource(options);
                    } else if (position[0] > 3) {
                        documento = documentos[position[0]-4];

                        var data = {
                            id: meta[position[1]],
                            documento: documento,
                            situacao: value
                        };

                        var options = {
                            url: putResourceUrlBuilder.buildUrl('/module/Api/inscrito', 'inscrito-documento', {}),
                            dataType: 'json',
                            data: data,

                            success: function (dataResponse) {
                                messageUtils.success('Situação documento ' + documento + ' alterado com sucesso.');
                            }
                        };

                        putResource(options);
                    }
                }

                $('#corpo').jexcel({
                    data: data,
                    editable: true,
                    colHeaders: header,
                    colAlignments: [
                        'left'
                    ],
                    columns: [
                        { type: 'text', readOnly: true },
                        { type: 'text', readOnly: true },
                        { type: 'text', readOnly: true },
                        { type: 'dropdown', source:[{'id':'0', 'name':'Não Compareceu'}, {'id':'1', 'name':'Não Aprovado'}, {'id':'2', 'name':'Aprovado Aparcialmente'}, {'id':'3', 'name':'Aprovado'}] },
                        { type: 'dropdown', source:[{'id':'0', 'name':'Não Entregue'}, {'id':'1', 'name':'Documento Inválido'}, {'id':'2', 'name':'Entregue'}] },
                        { type: 'dropdown', source:[{'id':'0', 'name':'Não Entregue'}, {'id':'1', 'name':'Documento Inválido'}, {'id':'2', 'name':'Entregue'}] },
                        { type: 'dropdown', source:[{'id':'0', 'name':'Não Entregue'}, {'id':'1', 'name':'Documento Inválido'}, {'id':'2', 'name':'Entregue'}] },
                        { type: 'dropdown', source:[{'id':'0', 'name':'Não Entregue'}, {'id':'1', 'name':'Documento Inválido'}, {'id':'2', 'name':'Entregue'}] },
                        { type: 'dropdown', source:[{'id':'0', 'name':'Não Entregue'}, {'id':'1', 'name':'Documento Inválido'}, {'id':'2', 'name':'Entregue'}] }
                    ],
                    allowInsertRow: false,
                    allowManualInsertRow: false,
                    allowInsertColumn: false,
                    allowManualInsertColumn: false,
                    allowDeleteRow: false,
                    allowDeleteColumn: false,
                    columnSorting: false,
                    onchange: handler
                });

                $('#corpo').jexcel('updateSettings', {
                    table: function (instance, cell, col, row, val, id) {
                        // Odd row colours
                        if (col == 3) {
                            if (val >= 2) {
                                $(cell).css('background-color', '#b7e1cd');
                            } else if (val != '' && val < 2) {
                                $(cell).css('background-color', '#f4cccc');
                            }
                        }
                        if(col > 3) {
                            if (val == 2) {
                                $(cell).css('background-color', '#b7e1cd');
                            } else if (val == 1) {
                                $(cell).css('background-color', '#f4cccc');
                            } else {
                                $(cell).css('background-color', '#fff');
                            }
                        }
                    }
                });
            }); // ready
        })(jQuery);
    </script>
@endpush
