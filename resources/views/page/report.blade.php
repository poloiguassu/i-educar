@push('page_scripts')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jqc-1.12.4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jqc-1.12.4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.33/vfs_fonts.js"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
@endpush

@php
    $columns = $content['table_header'];
    $lines = $content['table_lines'];
@endphp

@push('page_content')
    {!! $content['html'] !!}

    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $.noConflict();
                $('[data-toggle="popover"]').popover()
                $.fn.dataTable.ext.search.push(
                    function( settings, data, dataIndex ) {
                        var min = parseInt( $('#min_Idade').val(), 10 );
                        var max = parseInt( $('#max_Idade').val(), 10 );
                        var age = parseFloat( data[5] ) || 0; // use data for the age column
                        if ( ( isNaN( min ) && isNaN( max ) ) ||
                                ( isNaN( min ) && age <= max ) ||
                                ( min <= age   && isNaN( max ) ) ||
                                ( min <= age   && age <= max ) )
                        {
                            return true;
                        }
                        return false;
                    }
                );
                var table = $('#table_id').DataTable({
                    colReorder: true,
                    responsivel: false,
                    select: true,
                    lengthChange: true,
                    pageLength: 25,
                    dom: "lf<'floatright'B>rtip",
                    select: true,
                    buttons: [
                        "excel",
                        {
                            extend: "print",
                            exportOptions: {
                                columns: ":visible",
                                modifier: { search: "applied" }
                            }
                        },
                        {
                            extend: "pdfHtml5",
                            orientation: "landscape",
                            pageSize: "LEGAL",
                            download: "open",
                            exportOptions: {
                                columns: ":visible",
                                modifier: { search: "applied" }
                            }
                        },
                        "colvis"
                    ],
                    language: {
                        url: '/language/datatable.pt_BR.json'
                    },
                });
                $('#min_Idade, #max_Idade').on('keyup change', function () {
                    table.draw();
                } );
                $("#table_id tfoot input.input-texto").on('keyup change', function () {
                    table
                    .column( $(this).parent().index()+':visible' )
                    .search(this.value, true, false)
                    .draw();
                } );
            }); // ready
        })(jQuery);
    </script>

    <table id="table_id" class="tablelistagem table table-striped" cellspacing="0" width="100%">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tfoot>
            <tr>
                @foreach($columns as $column)
                    @if(in_array($column, ['idade']))
                        <th>
                            <input type="text" id="min_{{ $column }}" placeholder="Minimo" maxlength="2" size="4">
                            <input type="text" id="max_{{ $column }}" placeholder="Maximo" maxlength="2" size="4">
                        </th>
                    @else
                        <th>
                            <input type="text" class="input-texto" placeholder="Pesquisar {{ $column }}">
                        </th>
                    @endif
                @endforeach
            </tr>
        </tfoot>
        <tbody>
            @foreach($lines as $line)
                <tr>
                    @foreach($line as $column)
                        <th>{!! $column !!}</th>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <table id="table_id" class="tablelistagem table table-striped" cellspacing="0" width="100%">
        <div id="chartdiv" class="grafico"></div>
    </table>

    <script>
        var chart = AmCharts.makeChart( "chartdiv", {
            "type": "serial",
            "theme": "light",
            "dataProvider": [
                @foreach($lines as $line)
                    {
                        "label": "{{ line[0] }}",
                        "value": "{{ line[1] }}"
                    },
                @endforeach
            ],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<b>[[category]]: [[value]]</b>",
                "fillColorsField": "color",
                "fillAlphas": 0.9,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "value"
            }],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "label",
            "categoryAxis": {
                "gridPosition": "start",
                "labelRotation": 45
            },
            "export": {
                "enabled": true
            }
        } );
    </script>

    {!! $content['buttons'] !!}
@endpush
