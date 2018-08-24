var gulp = require('gulp'),
  less = require('gulp-less'),
  useref = require('gulp-useref'),
  csso = require('gulp-csso'),
  uglify = require('gulp-uglify'),
  gulpif = require('gulp-if'),
  size = require('gulp-size'),
  del = require('del'),
  connect = require('gulp-connect'),
  watch = require('gulp-watch'),
  autoprefixer = require('gulp-autoprefixer');

var path = {
  src: './',
  dist: 'dist/',
  bower: 'vendor/bower/'
};

gulp.task('less', function () {
  gulp.src(path.src + 'lesses/*.less')
    .pipe(less())
    .pipe(autoprefixer())
    .pipe(gulp.dest(path.src + 'styles'))
    .pipe(size({title:'less:'}));
});

gulp.task('useref', ['less'], function () {
  return gulp.src(path.src + 'test.html')
    .pipe(useref())
    .pipe(gulpif('*.js', uglify()))
    .pipe(gulpif('*.css', csso()))
    .pipe(gulpif('*.js', gulp.dest(path.dist)))
    .pipe(gulpif('*.css', gulp.dest(path.dist)))
    .pipe(size({title:'useref:'}));
});

gulp.task('del', function (callback) {
  var map = [
    path.dist
  ];
  return del(map, {force: true}, callback);
});

gulp.task('copy', function () {
  gulp.src(path.bower + 'font-awesome/fonts/*')
    .pipe(gulp.dest(path.dist + 'fonts'))
    .pipe(size({title:'copyFont:'}));
});

gulp.task('connect', function () {
  connect.server({
    root: path.src,
    port: 5000,
    livereload: true
  });
});

gulp.task('watch', function () {
  gulp.watch([path.src + 'lesses/**/*.less'], ['less', 'reload']);
  gulp.watch([path.src + '*.html'], ['reload']);
});

gulp.task('reload', function () {
  gulp.src(path.src + '*.html')
    .pipe(connect.reload());
});

gulp.task('build', ['del'],function() {
  return gulp.start('useref','copy');
});

gulp.task('default', ['less', 'copy', 'connect', 'watch']);
