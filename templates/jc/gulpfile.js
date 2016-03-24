/* plugins */
const gulp              = require('gulp');
const less              = require('gulp-less');
const sourcemaps        = require('gulp-sourcemaps');   // Source map support for Gulp.js
const autoprefixer      = require('gulp-autoprefixer'); // It will parse CSS and add vendor prefixes to CSS rules using values from Can I Use.
const notify			= require('gulp-notify');       // Send messages to Mac Notification Center, Linux notifications or Windows >= 8 (using native toaster).
const cssnano           = require('gulp-cssnano');      // Minify CSS with cssnano.

/* src folders */
const lessDir           = 'less';
const targetCss         = 'css';

/* Gulp tasks */
gulp.task('less', function () {
    gulp.src(lessDir + '/template.less')
        .pipe(sourcemaps.init())
            .pipe(less())
            .on("error", notify.onError("<%= error.message %>"))
            .pipe(autoprefixer('last 10 versions', 'ie 9'))
            .pipe(cssnano())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(targetCss))
        .pipe(notify('LESS compiled successfully'));
});

gulp.task('default', function(){
    gulp.start(['less']);
    gulp.watch(lessDir + '/**/*.less', ['less']);
});
