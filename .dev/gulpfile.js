const gulp = require('gulp');
const concat = require('gulp-concat');

gulp.task('css:prod', () => {
    const postcss = require('gulp-postcss')
    const cleanCSS = require('gulp-clean-css');
    const cssimport = require("gulp-cssimport");
    const sourcemaps = require('gulp-sourcemaps')

    const sass = require('gulp-sass')(require('sass'));

    const files = [
        './node_modules/datatables.net-dt/css/jquery.dataTables.css',
        './css/Buttons.scss',
        './css/Collapsible.scss',
        './css/Company-select.scss',
        './css/Datatables.scss',
        './css/Login.scss',
        './css/Logs.scss',
        './css/Message.scss',
        './css/Settings.scss',
    ];

    return gulp.src(files)
        .pipe(sourcemaps.init())
        .pipe(sass({includePaths: ['node_modules']}))
        .pipe(cssimport())
        .pipe(postcss([
            require('autoprefixer'),
            require('postcss-combine-media-query')
        ]))
        .pipe(cleanCSS({level: {1: {specialComments: 0}}}))
        .pipe(concat("compiled.min.css"))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('../views/css/'));
});

gulp.task('js:prod', () => {
    const babel = require("gulp-babel");
    const plumber = require("gulp-plumber");
    const uglify = require('gulp-uglify');

    const files = [
        './node_modules/datatables.net/js/jquery.dataTables.js',
        './js/Main.js',
        './js/CompanySelect.js',
        './js/Login.js',
        './js/Logs.js',
        './js/Movements.js',
        './js/PendingOrders.js',
        './js/Settings.js',
        './js/Tools/Tools.js',
        './js/Tools/Overlays/SyncProducts.js',
    ];

    return (
        gulp.src(files)
            .pipe(plumber())
            .pipe(babel({
                presets: [
                    ["@babel/env", {modules: false}],
                ]
            }))
            .pipe(uglify())
            .pipe(concat("compiled.min.js"))
            .pipe(gulp.dest("../views/js/"))
    )
});
