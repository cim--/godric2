const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.autoload({
    jquery: ['$', 'window.jQuery']
})
    .js([
	'node_modules/tinymce/tinymce.js',
	'node_modules/tinymce/themes/silver/theme.js',
	'node_modules/tinymce/models/dom/model.js',
	'node_modules/tinymce/icons/default/icons.js',
	'node_modules/tinymce/plugins/table/plugin.js',
	'node_modules/tinymce/plugins/link/plugin.js',
	'node_modules/tinymce/plugins/image/plugin.js',
	'node_modules/tinymce/plugins/lists/plugin.js',
	'node_modules/tinymce/plugins/code/plugin.js',
	'node_modules/chart.js/dist/chart.js',
	'node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.js',
	'node_modules/jquery/dist/jquery.js',
	'node_modules/datatables.net-dt/js/dataTables.dataTables.js',
	'resources/js/app.js'
    ], 'public/js/app.js')
    .css('resources/css/app.css', 'public/css', [
        //
    ])
    .copy('node_modules/tinymce/skins/content/default/content.min.css', 'public/css/tinymce/')
    .copy('node_modules/tinymce/skins/ui/tinymce-5/skin.min.css', 'public/css/tinymce/')
;
