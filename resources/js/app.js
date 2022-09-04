var $       = require( 'jquery' );
var dt      = require( 'datatables.net' );
var tinymce      = require( 'tinymce' );
var chartjs = require('chart.js');

// initialise datatables
$(document).ready( function () {
    $('.datatable').DataTable();

    tinymce.init({
	selector: '.htmlbox',
	skin_url: '/css/tinymce',
	content_css: '/css/app.css',
//	menubar: false,
//	statusbar: false,
	toolbar: 'undo redo code | bold italic link unlink | formatselect bullist numlist image hr h2 h3 | table tableinsertrowbefore tableinsertrowafter tabledeleterow tableinsertcolbefore tableinsertcolafter tabledeletecol tabledelete',
	block_formats: 'Heading=h2; Subheading=h3; Paragraph=p',
	plugins: 'link, table, lists, image, code',
	menu: {
	    edit: { title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace' },
	    view: { title: 'View', items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen' },
	    insert: { title: 'Insert', items: 'image link inserttable hr' },
	    format: { title: 'Format', items: 'bold italic superscript subscript | blockformats | removeformat' },
	    table: { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
	},
	link_assume_external_targets: true,
	target_list: false,
	link_title: false
    });
} );

