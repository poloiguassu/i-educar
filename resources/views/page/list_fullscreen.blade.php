@push('page_scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs-3.3.7/jq-3.3.1/dt-1.10.18/cr-1.5.0/fh-3.1.4/r-2.2.2/sl-1.2.6/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css"/>



    <script type="text/javascript" src="https://cdn.datatables.net/v/bs-3.3.7/jq-3.3.1/dt-1.10.18/cr-1.5.0/fh-3.1.4/r-2.2.2/sl-1.2.6/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
@endpush

@php
    $columns = $content['table_header'];
    $lines = $content['table_lines'];
@endphp

@push('page_content')
    <table id="table_id" class="table table-striped table-bordered" style="width: 100%; margin-top: 0 !important; margin-bottom: 0 !important;">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th>{!! $column !!}</th>
                @endforeach
            </tr>
        </thead>
        <tfoot>
            <tr>
                @foreach($columns as $column)
                    <th>{{ $column }}</th>
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

    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $.noConflict();
                var table = $('#table_id').DataTable({
                    colReorder: true,
                    select: true,
                    responsive: true,
                    paging: false,
                    pageLength: 1000,
                    "fixedHeader": {
                        header: true,
                        footer: false
                    },
                    dom: "",
                    language: {
                        url: '/language/datatable.pt_BR.json'
                    },
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var column = this;
                            var select = $('<select class="multiselect" multiple="multiple"><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    var vals = $('option:selected', this).map(function (index, element) {
                                        return $.fn.dataTable.util.escapeRegex($(element).val());
                                    }).toArray().join('|');

                                    column
                                        .search( vals.length > 0 ? '^('+vals+')$' : '', true, false )
                                        .draw();
                                })

                            column.data().unique().sort().each(function ( d, j ) {
                                select.append('<option value="'+d+'">'+d+'</option>')
                            });
                        });

                    }
                });
            }); // ready
        })(jQuery);
    </script>
@endpush
