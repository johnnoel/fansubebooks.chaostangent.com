/**
 * fansubebooks.chaostangent.com gulpfile
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

var gulp = require('gulp'),
    plugins = require('gulp-load-plugins')(),
    source = require('vinyl-source-stream'),
    buffer = require('vinyl-buffer'),
    productionMode = false;

var config = {
    theme: {
        src: 'src/ChaosTangent/FansubEbooks/Resources/sass/',
        entry: 'theme.scss',
        destPath: 'web/css/',
        destName: 'style.css'
    }
};

gulp.task('theme', function() {
    var c = config.theme;

    return gulp.src(c.src+c.entry)
        .pipe(plugins.sourcemaps.init({ loadMaps: true }))
        .pipe(plugins.sass())
        .on('error', plugins.util.log)
        .pipe(plugins.rename(c.destName))
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(gulp.dest(c.destPath));
});

gulp.task('default', [ 'theme' ], function() {
    if (!productionMode) {
        gulp.watch(config.theme.src+'**/*.scss', [ 'theme' ]);
    }
});
