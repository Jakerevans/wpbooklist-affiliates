/**
Mostly derived from https://bitsofco.de/a-simple-gulp-workflow
npm install gulp
npm install --save-dev gulp-sass
npm install --save-dev gulp-concat
npm install --save-dev gulp-uglify
npm install --save-dev gulp-util
npm install --save-dev gulp-rename
npm install --save-dev gulp-babel
npm install --save-dev gulp-zip
npm install --save-dev del
 */

// First require gulp.
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    gutil = require('gulp-util'),
    rename = require('gulp-rename'),
    zip = require('gulp-zip'),
    del = require('del');


// Define default task
gulp.task('default', function() {
  console.log("Hello, world!");
});

// Define file sources
var sassMain = ['dev/scss/wpbooklist-admin-ui.scss'];
var sassFrontendSource = ['dev/scss/wpbooklist-main-frontend.scss'];
var sassFrontendSourcePartial = ['dev/scss/_wpbooklist-frontend-ui.scss'];
var sassBackendSource = ['dev/scss/wpbooklist-main-admin.scss'];
var sassBackendSourcePartial = ['dev/scss/_wpbooklist-backend-ui.scss'];
var sassPostPagesSource = ['dev/scss/wpbooklist-posts-pages-default.scss'];
var sassWatch = ['dev/scss/*.scss'];

var jsBackendSource = ['dev/js/backend/*.js']; // Any .js file in scripts directory
var jsFrontendSource = ['dev/js/frontend/*.js']; // Any .js file in scripts 


var jsFrontendWatch = ['dev/js/frontend/*.js'];
var jsBackendWatch = ['dev/js/backend/*.js'];


// Task to compile Frontend SASS file
gulp.task('sassFrontendSource', function() {
    gulp.src(sassFrontendSource) // use sassMain file source
        .pipe(sass({
            outputStyle: 'compressed' // Style of compiled CSS
        })
            .on('error', gutil.log)) // Log descriptive errors to the terminal
        .pipe(gulp.dest('assets/css')); // The destination for the compiled file
});

// Task to compile Backend SASS file
gulp.task('sassBackendSource', function() {
    gulp.src(sassBackendSource) // use sassMain file source
        .pipe(sass({
            outputStyle: 'compressed' // Style of compiled CSS
        })
            .on('error', gutil.log)) // Log descriptive errors to the terminal
        .pipe(gulp.dest('assets/css')); // The destination for the compiled file
});

// Task to compile Post/Pages SASS file
gulp.task('sassPostPagesSource', function() {
    gulp.src(sassPostPagesSource) // use sassMain file source
        .pipe(sass({
            outputStyle: 'compressed' // Style of compiled CSS
        })
            .on('error', gutil.log)) // Log descriptive errors to the terminal
        .pipe(gulp.dest('assets/css')); // The destination for the compiled file
});

// Task to concatenate and uglify js files
gulp.task('concatAdminJs', function() {
    gulp.src(jsBackendSource) // use jsSources
        .pipe(concat('wpbooklist_admin.min.js')) // Concat to a file named 'script.js'
        .pipe(uglify()) // Uglify concatenated file
        .pipe(gulp.dest('assets/js')); // The destination for the concatenated and uglified file
});

// Task to concatenate and uglify js files
gulp.task('concatFrontendJs', function() {
    gulp.src(jsFrontendSource) // use jsSources
        .pipe(concat('wpbooklist_frontend.min.js')) // Concat to a file named 'script.js'
        .pipe(uglify()) // Uglify concatenated file
        .pipe(gulp.dest('assets/js')); // The destination for the concatenated and uglified file
});

gulp.task('copyassets', function () {
    gulp.src(['./assets/**/*'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('copyincludes', function () {
    gulp.src(['./includes/**/*'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('copyquotes', function () {
    gulp.src(['./quotes/**/*'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('copyreadme', function () {
    gulp.src(['./readme.txt'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('copylang', function () {
    gulp.src(['./languages/**/*'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('copymainfile', function () {
    gulp.src(['./wpbooklist-affiliates.php'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('copyuifile', function () {
    gulp.src(['./class-admin-settings-affiliates-tab-extension-ui.php'], {base: './'}).pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task('zip', function () {
    return gulp.src('../wpbooklist-affiliates_dist/**')
        .pipe(zip('wpbooklist-affiliates.zip'))
        .pipe(gulp.dest('../wpbooklist-affiliates_dist'));
});

gulp.task( 'cleanzip', function(cb) {
    del([ '../wpbooklist-affiliates_dist/**/*' ], {force: true}, cb);
});

gulp.task('clean', function(cb) {
    del(['../wpbooklist-affiliates_dist/**/*', '!../wpbooklist-affiliates_dist/wpbooklist-affiliates.zip'], {force: true}, cb);
});

// Task to watch for changes in our file sources
gulp.task('watch', function() {
    gulp.watch(sassWatch,['sassFrontendSource', 'sassBackendSource']);
    gulp.watch(jsFrontendWatch,['concatFrontendJs']);
    gulp.watch(jsBackendWatch,['concatAdminJs']);
});

// Default gulp task
//gulp.task('default', ['sassFrontendSource', 'sassBackendSource', 'sassPostPagesSource', 'concatAdminJs', 'concatFrontendJs', 'watch']);


//gulp.task( 'default', [ 'cleanzip' ]);

//gulp.task( 'default', [ 'copyassets', 'copyincludes', 'copyreadme', 'copylang', 'copymainfile' , 'copyuifile' ]);

//gulp.task( 'default', [ 'zip' ]);

gulp.task( 'default', [ 'clean' ]);