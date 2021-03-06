let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.react('resources/assets/js/scorer/scorer.js', 'public/js');
mix.react('resources/assets/js/invoicer/invoicer.js', 'public/js').sourceMaps(productionToo=false);
mix.react('resources/assets/js/video_review/video_review.js', 'public/js').sourceMaps(productionToo=false);
