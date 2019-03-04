@extends('layout.default')

@foreach ($body as $content)
    @include($content['template'], $content)
@endforeach

@section('head')
    @parent

    <script type="text/javascript">
        window.ambiente = 'development';

        var running = false;
        var altura = null;

        function changeImage(div_id) {
            var id = /[0-9]+/.exec(div_id.element.id);
            var imagem = $('seta_' + id);
            var src = imagem.src.indexOf('arrow-up');

            imagem.src = (src != -1) ?
                'imagens/arrow-down2.png' : 'imagens/arrow-up2.png';

            imagem.title = (src != -1) ?
                imagem.title.replace('Abrir', 'Fechar') :
                imagem.title.replace('Fechar', 'Abrir');

            if (src != -1) {
                setCookie('menu_' + id, 'I', 30);
            }
            else {
                setCookie('menu_' + id, 'V', 30);
            }

            running = false;
            $('tablenum1').style.height = $('tablenum1').offsetHeight - altura;
        }

        function teste(div_id) {
            altura = div_id.element.offsetHeight;
        }

        function toggleMenu(div_id) {
            if (running) {
                return;
            }

            var src = $('link1_' + div_id).title.indexOf('Abrir');

            $('link1_' + div_id).title = (src != -1) ?
                $('link1_' + div_id).title.replace('Abrir', 'Fechar') :
                $('link1_' + div_id).title.replace('Fechar', 'Abrir');

            $('link2_' + div_id).title = (src != -1) ?
                $('link2_' + div_id).title.replace('Abrir', 'Fechar') :
                $('link2_' + div_id).title.replace('Fechar', 'Abrir');

            running = true;

            new Effect.toggle($('div_' + div_id), 'slide', {
                afterFinish: changeImage,
                duration: 0.3,
                beforeStart: teste
            });
        }
    </script>

    @stack('page_scripts')
@endsection

@section('content')
    @stack('page_content')
@endsection
