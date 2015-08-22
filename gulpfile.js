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
    del = require('del'),
    browserify = require('browserify'),
    babelify = require('babelify'),
    watchify = require('watchify'),
    es = require('event-stream'),
    productionMode = !!require('yargs').argv.production;

if (productionMode) {
    // fudgy, but means certain optimisations in things like react kick in
    process.env.NODE_ENV = 'production';
}

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
            src: 'vendor.js',
            dest: 'web/js/',
            externals: [
                { id: 'react', expose: 'react' },
                { id: 'redux', expose: 'redux' },
                { id: 'react-redux', expose: 'react-redux' },
                { id: 'xhr', expose: 'xhr' },
                { id: 'native-promise-only', expose: 'native-promise-only' }
            ]
        },
        routing: {
            src: [
                'vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js',
                'web/js/fos_js_routes.js'
            ],
            destPath: 'web/js/',
            destName: 'routing.js'
        },
        linelist: {
            src: 'src/Resources/js/linelist/',
            entry: 'index.js',
            destName: 'linelist.js',
            destPath: 'web/js/'
        },

        // generic
        lines: {
            src: 'src/Resources/js/',
            entry: 'lines.js',
            destName: 'lines.js',
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
 * Get a vendor aware browserify package
 *
 * You'll need to add an entry point once you've obtained this
 *
 * @return Browserify
 */
function getBrowserify() {
    var b = browserify({
        transform: [ babelify ],
        cache: {}, packageCache: {},
        fullPaths: false,
        debug: !productionMode
    });

    config.js.vendor.externals.forEach(function(external) {
        b.external(external.id);
    });

    return b;
}

/**
 * Default bundle with browserify function
 *
 * @param Browserify browserify Can also be watchify
 * @param object config A standard config object
 * @return stream
 */
function bundleWithBrowserify(browserify, config) {
    return browserify.bundle()
        .on('error', plugins.util.log)
        .pipe(source(config.destName))
        .pipe(buffer())
        .pipe(plugins.sourcemaps.init({ loadMaps: true }))
        .pipe(plugins.if(productionMode, plugins.uglify()))
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(gulp.dest(config.destPath));
}

/**
 * Routing
 *
 * First generate the routing file in web/js/fos_js_routes.js
 * Then combine all of the relevant files
 * Then remove the web/js/fos_js_routes.js file
 */
gulp.task('js:routing:generate', function() {
    var env = (productionMode) ? 'prod' : 'dev';
    return gulp.src('')
        .pipe(plugins.shell([
            '/usr/bin/php app/console fos:js-routing:dump -q --env='+env
        ]));
});

/**
 * Vendor
 *
 * A browserify bundle of commonly used libraries as well as the routing
 * system from FOSJsRouting bundle
 */
gulp.task('js:vendor', [ 'js:routing:generate' ], function() {
    var cb = config.js.vendor,
        cr = config.js.routing,
        b = browserify({
            cache: {}, packageCache: {},
            fullPaths: false,
            debug: !productionMode
        });

    cb.externals.forEach(function(external) {
        b.require(external.id, { expose: external.expose });
    });

    var browserifyStream = b.bundle()
        .pipe(source(cb.src))
        .pipe(buffer());

    var routingStream = gulp.src(cr.src)
        .pipe(buffer());

    return es.merge(browserifyStream, routingStream)
        .pipe(plugins.sourcemaps.init({ loadMaps: true }))
        .pipe(plugins.concat(cb.src))
        .pipe(plugins.if(productionMode, plugins.uglify()))
        .pipe(plugins.sourcemaps.write('./'))
        .pipe(plugins.size({ showFiles: true }))
        .pipe(plugins.size({ showFiles: true, gzip: true }))
        .pipe(gulp.dest(cb.dest));
});

/**
 * Line list
 *
 * React component for a container of lines that may be paginated
 */
gulp.task('js:linelist', function() {
    var c = config.js.linelist,
        b = getBrowserify();

    // note: not add, expose this as an external package
    b.require('./'+c.src+c.entry, { expose: 'linelist' });

    if (!productionMode) {
        var w = watchify(b);
        w.on('update', function() { bundleWithBrowserify(w, c); });
        w.on('log', plugins.util.log);
        return bundleWithBrowserify(w, c);
    }

    return bundleWithBrowserify(b, c);
});

/*
 * Home page
 */
gulp.task('js:lines', function() {
    var c = config.js.lines,
        b = getBrowserify();

    b.add(c.src+c.entry);
    b.external('linelist');

    return bundleWithBrowserify(b, c);
});

gulp.task('js', [ 'js:head' ]);

gulp.task('default', [ 'theme', 'js' ], function() {
    if (!productionMode) {
        gulp.watch(config.theme.main.src+'**/*.scss', [ 'theme:main' ]);
    }
});
