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
    browserify = require('browserify'),
    babelify = require('babelify'),
    watchify = require('watchify'),
    productionMode = !!require('yargs').argv.production;

var config = {
    theme: {
        main: {
            src: 'src/Resources/sass/',
            entry: 'theme.scss',
            destPath: 'web/css/',
            destName: 'style.css'
        },

        fonts: {
            src: 'src/Resources/sass/fonts.scss',
            dest: 'web/css/'
        }
    },

    js: {
        head: {
            src: 'src/Resources/js/head/**/*.js',
            dest: 'web/js/'
        },
        vendor: {
        },
        linelist: {
            src: 'src/Resources/js/linelist/',
            entry: 'index.js',
            destName: 'linelist.js',
            destPath: 'web/js/'
        }
    }
};

gulp.task('theme:main', function() {
    var c = config.theme.main;

    return gulp.src(c.src+c.entry)
        .pipe(plugins.sourcemaps.init({ loadMaps: true }))
        .pipe(plugins.sass())
        .on('error', plugins.util.log)
        .pipe(plugins.if(productionMode, plugins.minifyCss({
            debug: !productionMode,
            keepSpecialComments: 0
        })))
        .pipe(plugins.rename(c.destName))
        .pipe(plugins.autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(gulp.dest(c.destPath));
});

gulp.task('theme:fonts', function() {
    var c = config.theme.fonts;

    return gulp.src(c.src)
        .pipe(plugins.sourcemaps.init({ loadMaps: true }))
        .pipe(plugins.sass())
        .on('error', plugins.util.log)
        .pipe(plugins.if(productionMode, plugins.minifyCss({
            debug: !productionMode,
            keepSpecialComments: 0
        })))
        .pipe(plugins.rename('fonts.css'))
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(gulp.dest(c.dest));
});

gulp.task('theme', [ 'theme:fonts', 'theme:main' ]);

gulp.task('js:head', function() {
    var c = config.js.head;

    return gulp.src(c.src)
        .pipe(plugins.sourcemaps.init({ loadMaps: true }))
        .pipe(plugins.concat('head.js'))
        .pipe(plugins.if(productionMode, plugins.uglify()))
        .on('error', plugins.util.log)
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(gulp.dest(c.dest));
});

/**
 * Line list
 *
 * React component for a container of lines that may be paginated
 */
gulp.task('js:linelist', function() {
    var c = config.js.linelist,
        b = browserify({
            entries: [ c.src+c.entry ],
            transform: [ babelify ],
            cache: {}, packageCache: {},
            fullPaths: true,
            debug: !productionMode
        });

    function bundle(b) {
        return b.bundle()
            .pipe(source(c.destName))
            .pipe(buffer())
            .pipe(plugins.sourcemaps.init({ loadMaps: true }))
            .pipe(plugins.if(productionMode, plugins.uglify()))
            .pipe(plugins.sourcemaps.write('./'))
            .pipe(gulp.dest(c.destPath));
    }

    if (!productionMode) {
        var w = watchify(b);
        w.on('update', function() { bundle(w); });
        w.on('log', plugins.util.log);
        return bundle(w);
    }

    return bundle(b);
});

gulp.task('js', [ 'js:head' ]);

gulp.task('default', [ 'theme', 'js' ], function() {
    if (!productionMode) {
        gulp.watch(config.theme.main.src+'**/*.scss', [ 'theme:main' ]);
    }
});
