const gulp = require('gulp');
const standard = require('gulp-standard')
const autoprefixer = require('gulp-autoprefixer')
const babel = require('gulp-babel')
const concat = require('gulp-concat')
const cssmin = require('gulp-cssmin')
const sass = require('gulp-sass')
const rename = require('gulp-rename')
const uglify = require('gulp-uglify')
const sourcemaps = require('gulp-sourcemaps')
const gutil = require('gulp-util')

gulp.task('scripts', () => {
  console.log('running scripts')
  return gulp.src('./assets/scripts/*.js')
    .pipe(standard())
    .pipe(standard.reporter('default', {
      breakOnError: false,
      quiet: true
    }))
    .pipe( babel({
      presets: ['es2015']
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(sourcemaps.init({
      loadMaps: true
    }))
    .pipe(uglify())
    .pipe(gulp.dest('./dist/scripts/'))

})

gulp.task('styles', () => {
  return gulp.src('./assets/styles/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: [
        'last 2 versions',
        'android 4',
        'opera 12'
      ]
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(sourcemaps.init({
      loadMaps: true
    }))
    .pipe(cssmin())
    .pipe(gulp.dest('./dist/styles/'))
})

gulp.task('build', ['styles', 'scripts'], () => {
  console.log('Build Complete')
})

gulp.task('watch', () => {
  console.log('Watching Files')
  gulp.watch(['./assets/styles/*'], ['styles'])
  gulp.watch(['./assets/scripts/*'], ['scripts'])
})