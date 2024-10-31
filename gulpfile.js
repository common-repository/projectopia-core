const gulp = require( 'gulp' ),
    del = require( 'del' ),
    zip = require( 'gulp-zip' );
const { series } = require( 'gulp' );

// Pot Path
var potPath = [ './*.php', 'includes/*.php', 'includes/**/*.php', 'templates/*.php' ];

// ZIP Path
var zipPath = [ './', './**', '!./.git', '!./.git/**', '!./.gitignore', '!./node_modules', '!./node_modules/**', '!./build', '!./build/**', '!./gulpfile.js', '!./package.json', '!./package-lock.json', '!./phpcs.xml', '!./LICENSE', '!./README.md', '!./.babelrc', '!./.editorconfig', '!./.eslintignore', '!./.eslintrc.json', '!./postcss.config.js', '!./webpack.config.js', '!./.stylelintrc' ];

// Clean CSS, JS and ZIP
function clean_files() {
    let cleanPath = [ './build/projectopia-core.zip' ];
    return del( cleanPath, { force : true } ); 
}

// Create ZIP file
function create_zip() {
    return gulp.src( zipPath, { base : '../' } )
        .pipe( zip( 'projectopia-core.zip' ) )
        .pipe( gulp.dest( './build/' ) )
}

exports.default = series( clean_files, create_zip );