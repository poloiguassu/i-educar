import $ from 'jquery';

// export for others scripts to use
//window.$ = $;
//window.jQuery = jQuery;

/*if (typeof($j) == 'undefined')
	{ var $j = jQuery.noConflict(); }*/

require('pdfmake/build/pdfmake.js');
require('imports-loader?this=>window!pdfmake/build/vfs_fonts.js');
require('script-loader!jszip/dist/jszip.js');

// you can use import or require
require("datatables.net")(window, $);
require("datatables.net-bs")(window, $);
require("datatables.net-select")(window, $);
require("datatables.net-buttons")(window, $);
require("datatables.net-buttons-bs")(window, $);
require("datatables.net-colreorder")(window, $);

require( 'datatables.net-buttons/js/buttons.colvis.js' )(window, $);
require( 'datatables.net-buttons/js/buttons.html5.js' )(window, $);
require( 'datatables.net-buttons/js/buttons.print.js' )(window, $);

require("datatables.net-select-bs/css/select.bootstrap.css");
require("datatables.net-colreorder-bs/css/colReorder.bootstrap.css");

var zip = new JSZip();

$(document).ready(function() {
	console.log("tsete");
	var table = $('#table_id').DataTable({
		colReorder: true,
		select: true,
		lengthChange: true,
		pageLength: 25,
		dom: "lf<'floatright'B>rtip",
		select: true,
		buttons: [
			{
				text: "Excel",
				extend: "excel",
			},
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
			url: '/assets/language/datatable.pt_BR.json'
		}
	});
});
