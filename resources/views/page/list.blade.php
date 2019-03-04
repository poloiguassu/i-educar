@push('page_scripts')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jqc-1.12.4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.1/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.1.0.js"></script>
@endpush

@php
    $columns = $content['table_header'];
    $lines = $content['table_lines'];
@endphp

@push('page_content')
    {!! $content['html'] !!}

    <div style="width: 84.9vw;">
        <table id="table_id" class="tablelistagem table table-striped" cellspacing="0" width="100%" style="width:100%;">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th class="formdktd">{!! $column !!}</th>
                    @endforeach
                </tr>
            </thead>
            <tfoot>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $coluna }}</th>
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
    </div>

    {!! $content['buttons'] !!}

    <script type="text/javascript">
        (function($) {
        $(document).ready(function() {
            $.noConflict();
            /*$('[data-toggle="popover"]').popover()*/
            var table = $('#table_id').DataTable({
                colReorder: true,
                scrollX: true,
                lengthChange: true,
                pageLength: 50,
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
                }
            });
            }); // ready
        })(jQuery);
    </script>
@endpush
