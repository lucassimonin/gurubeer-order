var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');
var concat = require('gulp-concat');
var sourcemaps = require('gulp-sourcemaps');
var livereload = require('gulp-livereload');
var gulp = require('gulp');
var env = 'dev';

var adminRootPath = 'public/build/admin/';

var paths = {
    admin: {
        js: [
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
            'assets/admin/js/app.js'
        ],
        css: [
            'node_modules/bootstrap/dist/css/bootstrap.css',
            'assets/admin/css/vendor/pixeladmin.min.css',
            'assets/admin/css/vendor/widgets.min.css',
            'assets/admin/css/vendor/themes/white.min.css',
            'assets/admin/css/app.css',
            'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
        ],
        img: [
            'assets/admin/images/**'
        ],
        fonts: [
            'assets/admin/fonts/**'
        ]
    }
};

gulp.task('admin-js', function () {
    return gulp.src(paths.admin.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(env === 'prod', uglify()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'js/'))
        ;
});

gulp.task('admin-css', function() {
    return gulp.src(paths.admin.css)
        .pipe(concat('style.css'))
        .pipe(gulpif(env === 'prod', uglifycss()))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'css/'))
        ;
});

gulp.task('admin-ckeditor', function() {
    return gulp.src(paths.admin.ckeditor)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'ckeditor/'))
        ;
});

gulp.task('admin-fonts', function() {
    return gulp.src(paths.admin.fonts)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(adminRootPath + 'fonts/'))
        ;
});


gulp.task('app-watch', function() {
    livereload.listen();

    gulp.watch(paths.admin.js, ['admin-js']);
    gulp.watch(paths.admin.css, ['admin-css']);
    gulp.watch(paths.admin.fonts, ['admin-fonts']);
});

gulp.task('build', ['admin-js', 'admin-css', 'admin-fonts']);
gulp.task('watch', ['build', 'app-watch']);
