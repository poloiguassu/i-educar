@foreach ($body as $content)
    @include($content['template'], $content)
@endforeach

@section('head')
    @parent

    @stack('page_scripts')
@endforeach

@section('body')
    @stack('page_content')
@endsection
