import $ from 'jquery';
import dt from 'datatables.net';
import tinymce from 'tinymce';
import Chart from 'chart.js/auto';
import cdl from 'chartjs-plugin-datalabels';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(ChartDataLabels);
Chart.defaults.plugins.datalabels.display = false;

/* Default icons are required for TinyMCE 5.3 or above */
import 'tinymce/icons/default';

/* A theme is also required */
import 'tinymce/themes/silver';

/* TinyMCE imports */
/* Import the skin */
import 'tinymce/skins/ui/oxide/skin.css';
/* Import content css */
import contentUiCss from 'tinymce/skins/ui/oxide/content.css?inline';
import contentCss from 'tinymce/skins/content/default/content.css?inline';

/* TinyMCE plugins */
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/image';
import 'tinymce/plugins/code';

import 'tinymce/models/dom';

window.Chart = Chart;

// initialise datatables
$(document).ready(function () {
    $('.datatable').DataTable();

    tinymce.init({
        selector: '.htmlbox',
	license_key: 'gpl',
        skin: false,
        content_css: '/css/app.css',
        menubar: false,
        statusbar: false,
        toolbar:
            'undo redo code | bold italic link unlink | formatselect bullist numlist image hr h2 h3 | table tableinsertrowbefore tableinsertrowafter tabledeleterow tableinsertcolbefore tableinsertcolafter tabledeletecol tabledelete',
        block_formats: 'Heading=h2; Subheading=h3; Paragraph=p',
        plugins: 'link, table, lists, image, code',
        menu: {
            edit: {
                title: 'Edit',
                items: 'undo redo | cut copy paste | selectall | searchreplace',
            },
            view: {
                title: 'View',
                items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen',
            },
            insert: { title: 'Insert', items: 'image link inserttable hr' },
            format: {
                title: 'Format',
                items: 'bold italic superscript subscript | blockformats | removeformat',
            },
            table: {
                title: 'Table',
                items: 'inserttable | cell row column | tableprops deletetable',
            },
        },
        link_assume_external_targets: true,
        target_list: false,
        link_title: false,
    });
});
