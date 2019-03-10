@push('page_scripts')
@endpush

@php
    $columns = $content['table_header'];
    $lines = $content['table_lines'];
@endphp


@push('page_content')
    {!! $lines[0] !!}

    {!! $content['buttons'] !!}

    <script type="text/javascript">
        function envia(obj, var1, var2, var3, var4, var5, var6, var7, var8)
        {
            var identificador = Math.round(1000000000 * Math.random());

            window.location = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&ano=' + var8 + '&identificador=' + identificador;
        }

        if (document.createStyleSheet) {
            document.createStyleSheet('styles/calendario.css');
        } else {
            var objHead = document.getElementsByTagName('head');
            var objCSS = objHead[0].appendChild(document.createElement('link'));
            objCSS.rel = 'stylesheet';
            objCSS.href = 'styles/calendario.css';
            objCSS.type = 'text/css';
        }
    </script>
@endpush