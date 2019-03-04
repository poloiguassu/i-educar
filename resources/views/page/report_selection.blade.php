@push('page_scripts')
    <style>
        .grafico {
            width: 100%;
            height: 500px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.33/vfs_fonts.js"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/pie.js"></script>
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

    <div class="g-block size-100 w-clearfix">
        <div id="grafico_sexo" class="grafico"></div>
        <div id="grafico_idade" class="grafico"></div>
        <div id="grafico_escolaridade" class="grafico"></div>
    </div>

    {!! $content['buttons'] !!}

    <script>
        var chart = AmCharts.makeChart( "grafico_sexo", {
            "type": "pie",
            "theme": "light",
            "dataProvider": [
                {
                    "label": "{{ $columns[0] }}",
                    "ammount": "{{ $lines[0][0] }}"
                },
                {
                    "label": "{{ $columns[1] }}",
                    "ammount": "{{ $lines[0][1] }}"
                },
            ],
            "valueField": "ammount",
            "titleField": "label",
            "outlineAlpha": 0.4,
            "depth3D": 15,
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[ammount]]</b> ([[percents]]%)</span>",
            "angle": 30,
            "export": {
                "enabled": true
            }
        });

        var chart = AmCharts.makeChart( "grafico_idade", {
            "type": "serial",
            "theme": "light",
            "dataProvider": [
                @foreach($lines[0][2] as $item)
                    {
                        "country": "{{ $item.idade }}",
                        "visits": "{{ $item.count }}"
                    },
                @endforeach
            ],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<b>[[category]] anos: [[value]]</b>",
                "fillColorsField": "color",
                "fillAlphas": 0.9,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "visits"
            }],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "country",
            "categoryAxis": {
                "gridPosition": "start",
                "labelRotation": 45
            },
            "export": {
                "enabled": true
            }
        } );

        var chart = AmCharts.makeChart( "grafico_escolaridade", {
            "type": "pie",
            "theme": "light",
            "dataProvider": [
                @foreach($lines[0][3] as $key => $item)
                    {
                        "label": "{{ $key }}",
                        "ammount": "{{ $value }}"
                    },
                @endforeach
            ],
            "valueField": "ammount",
            "titleField": "label",
            "outlineAlpha": 0.4,
            "depth3D": 15,
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[ammount]]</b> ([[percents]]%)</span>",
            "angle": 30,
            "export": {
                "enabled": true
            }
        });
    </script>
@endpush
