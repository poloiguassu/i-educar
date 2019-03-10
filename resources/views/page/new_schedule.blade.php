@push('page_scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/arrobefr-jquery-calendar@1.0.12/dist/css/jquery-calendar.min.css">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.1.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.1/min/moment-with-locales.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-touchswipe@1.6.18/jquery.touchSwipe.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/eonasdan-bootstrap-datetimepicker@4.17.47/build/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/arrobefr-jquery-calendar@1.0.12/dist/js/jquery-calendar.min.js"></script>
@endpush

@php
    $columns = $content['table_header'];
    $lines = $content['table_lines'];
@endphp


@push('page_content')
    <div id="calendar"></div>

    {!! $content['buttons'] !!}

    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $.noConflict();
                console.log("teste");
                moment.locale('en');
                var now = moment();

                var events = [{
                    start: now.startOf('week').add(14, 'h').format('X'),
                    end: now.startOf('week').add(16, 'h').format('X'),
                    title: '1',
                    content: 'Turismo e Atendimento',
                    category:'TUR'
                },{
                    start: now.startOf('week').add(16, 'h').format('X'),
                    end: now.startOf('week').add(18, 'h').format('X'),
                    title: '2',
                    content: 'Inglês',
                    category:'ING'
                }];

                var myCalendar = $('#calendar').Calendar({
                    locale: 'pt-BR',
                    events: events,

                    weekday: {
                        timeline: {
                            fromHour: 14, // start hour
                            toHour: 18, // end hour
                            intervalMinutes: 60,
                            format: 'HH:mm',
                            autoResize: true
                        },
                        dayline: {
                            weekdays: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }
                }).init();
            });
        })(jQuery);
    </script>
@endpush