/* plugins */
const gulp              = require('gulp');
const less              = require('gulp-less');
const sourcemaps        = require('gulp-sourcemaps');   // Source map support for Gulp.js
const autoprefixer      = require('gulp-autoprefixer'); // It will parse CSS and add vendor prefixes to CSS rules using values from Can I Use.
const notify			= require('gulp-notify');       // Send messages to Mac Notification Center, Linux notifications or Windows >= 8 (using native toaster).
const cssnano           = require('gulp-cssnano');      // Minify CSS with cssnano.

/*src folders*/
const assetsDir         = 'src/';
const lessDir           = 'less';
//const bowerDir          = 'bower_components';
//const jsDir             = assetsDir + 'js';
/*build folders*/
const targetDir         = 'build/';
const targetCss         = 'css';
//const targetJs          = targetDir + 'js';

/* TODO: willen we hier wat mee?
var scripts = [
    bowerDir + '/modernizr/modernizr.js',
    jsDir + '/template.js'
];

gulp.task('mergeScripts', function() {
    gulp.src(scripts)
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(concat('scripts.js'))
        .pipe(uglify()
            .on("error", notify.onError(function (error) {
                return error.message;
            })))
        .pipe(sourcemaps.write('../' + targetMap))
        .pipe(gulp.dest(targetJs));
});*/

gulp.task( 'less', function () {
    gulp.src(lessDir + '/template.less')
        .pipe(sourcemaps.init())
            .pipe(less())
            .on("error", notify.onError("<%= error.message %>"))
            .pipe(autoprefixer('last 10 versions', 'ie 9'))
            .pipe(cssnano())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(targetCss))
        .pipe(notify('cssdev done'));
});

gulp.task('default', function(){
    gulp.start(['less']);
    gulp.watch(lessDir + '/**/*.less', ['less']);
});