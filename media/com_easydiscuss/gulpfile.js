'use strict';

var gulp = require('gulp'),
    debug = require('gulp-debug'),
    fs = require('fs'),
    merge = require('merge-stream'),
    clone = require('gulp-clone'),
    // sass = require('gulp-sass'),
    // watchLess = require('gulp-watch-less'),
    // postcss = require('gulp-postcss'),
    cssnano = require('gulp-cssnano'),

    less = require('gulp-less'),
    // csso = require('gulp-csso'),
    clean = require('gulp-clean'),
    imagemin = require('gulp-imagemin'),
    prefix = require('gulp-autoprefixer'),
    rename = require('gulp-rename'),
    // minifyCSS = require('gulp-minify-css'),
    plumber = require('gulp-plumber'),
    notify = require('gulp-notify'),
    watch = require('gulp-watch'),
    browserSync = require('browser-sync'),
    reload = browserSync.reload,
    path = require('path'),
    newer = require('gulp-newer'),
    // cache = require('gulp-cached'),
    // progeny = require('gulp-progeny'),
    filter = require('gulp-filter'),
    // newy = require('gulp-newy'),

    postcss = require('gulp-postcss'),
    csswring = require('csswring'),

    sourcemaps = require('gulp-sourcemaps'),
    
    rtlcss = require('gulp-rtlcss'),


    nunjucksRender = require('gulp-nunjucks-render'),

    glob = require('glob');


// Error handle
var plumberErrorHandler = { errorHandler: notify.onError({
    title: 'Gulp',
    message: 'Error: <%= error.message %>'
  })
}
module.exports = function() {

  var args = Array.prototype.slice.call(arguments);

  // Send error to notification center with gulp-notify
  notify.onError({
    title: "Compile Error",
    message: "<%= error %>"
  }).apply(this, args);

  // Keep gulp from hanging on this task
  this.emit('end');
};

gulp.task('clean-css', function () {
  return gulp.src('themes/**/*.css', {read: false})
    .pipe(clean());
});

// Compiles updated theme only instead of all themes.
function processThemeFolder(src) {
    function debugTheme(type) {
        return debug({ title: 'Process: ' + theme + ' ' + type});
    }

    var theme = path.basename('/themes/'+src);
    var dest = 'themes/' + theme;

    var cloneSink = clone.sink();

    // console.log('theme '+ theme);
    // console.log('dest '+ dest);

    

    return merge (
        
        gulp
            // .src([src + '/less/**/style.less'])
            .src([src + '/**/*.less'])

            // .pipe(sourcemaps.init())

            .pipe(plumber(plumberErrorHandler))

            .pipe(newer(dest + '/css/style.css'))
            
            // .pipe(filter(['less/style.less', '!/lib/**']))
            .pipe(cloneSink) // clone objects

            .pipe(filter(['**/style.less']))
            .pipe(debugTheme('less'))
            .pipe(less())
            // .pipe(cssnano({discardComments: {removeAll: false}}))
            
            

            .pipe(rename('style.css'))
            

            
            // Output original
            // .pipe(rename({dirname: ''}))
            .pipe(gulp.dest(dest + '/css'))

            // .pipe(cloneSink.tap())
            
            // .pipe(minifyCSS({keepSpecialComments : 0}))
            .pipe(debug())

            .pipe(cssnano({discardComments: {removeAll: true}}))


            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest(dest + '/css'))
            
            .pipe(cloneSink.tap()) // output cloned objects
            .pipe(filter(['**/style-rtl.less']))
            .pipe(debugTheme('less'))
            .pipe(less())
            
            .pipe(rtlcss()) // Convert to RTL. 
            .pipe(rename('style-rtl.css'))
            .pipe(gulp.dest(dest + '/css'))
            
                  
            .pipe(cssnano({discardComments: {removeAll: true}}))

            // .pipe(rename('styles.min.css'))
            // .pipe(filter(['**/rtl.min']))

            .pipe(rename({suffix: '.min'}))
            .pipe(gulp.dest(dest + '/css'))

            
            

            // .pipe(cssnano({discardComments: {removeAll: true}}))

            // .pipe(rename('styles.min.css'))

            // .pipe(rename({suffix: '.min'}))
            // .pipe(gulp.dest(dest + '/css'))

        // gulp.src([dest+'/**/style.css'])
        //     .pipe(debugTheme('minify'))
        //     .pipe(minifyCSS())

        //     .pipe(rename('styles.min.css'))
        //     .pipe(gulp.dest(dest + '/css'))
        
        // gulp
        //     .src([src + '/img/**/*.{png,jpg,gif}'])
        //     .pipe(changed(dest + '/img'))
        //     .pipe(debugTheme('img'))
        //     .pipe(imagemin())
        //     .pipe(gulp.dest(dest + '/img'))
    ).on('change', reload);

}

gulp.task('themes', function() {
    // var srcThemes = glob.sync('*');
    // console.log('themes task');
    var srcThemes = glob.sync('themes/*');
    // console.log(srcThemes);
    return merge(srcThemes.map(processThemeFolder));
});

gulp.task('minify', function() {

});

gulp.task('nunjucks', function() {
    nunjucksRender.nunjucks.configure(['themes-preview/src/templates/']);

    // Gets .html and .nunjucks files in pages
      return gulp.src('themes-preview/src/pages/**/*.+(html|nunjucks)')
      // Renders template with nunjucks
      .pipe(nunjucksRender())
      // output files in folder
      .pipe(gulp.dest('themes-preview/output'));
});


/* Watch less changes */
gulp.task('watch-less', function() {  
    gulp.watch('themes/**/*.less' , ['themes']);
});
gulp.task('watch-template', function() {  
    gulp.watch('themes-preview/**/*.html' , ['nunjucks']);
});

/* Build css */
gulp.task('build', ['clean-css'], function () {
  gulp.start('themes');
});

gulp.task('default', ['watch-less','watch-template']);