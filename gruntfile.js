
module.exports = function(grunt) {
  'use strict';

  const sass = require('node-sass');

  var autoprefixer = require('autoprefixer')({
    browsers: [
      'Chrome >= 45',
      'Firefox >= 40',
      'Edge >= 12',
      'Explorer >= 11',
      'iOS >= 9',
      'Safari >= 9',
      'Android 2.3',
      'Android >= 4',
      'Opera >= 30'
    ]
  });


  // Project configuration.
  grunt.initConfig({

    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*!\n' +
            ' * <%= pkg.banner_name %> v<%= pkg.version %> (<%= pkg.homepage %>)\n' +
            ' * Copyright <%= grunt.template.today("yyyy") %> <%= pkg.author %>\n' +
            ' * Licensed under the Themeforest Standard Licenses\n' +
            ' */\n',


    // Task configuration
    // -------------------------------------------------------------------------------


    // Complile SCSS
    //
    sass: {

      expanded: {
        options: {
          implementation: sass,
          sourceMap: true,
          outputStyle: 'expanded'
        },
        files: {
          'public/src/assets/css/style.css': 'public/src/assets/css/style.scss'
        }
      },

      compressed: {
        options: {
          implementation: sass,
          sourceMap: true,
          outputStyle: 'compressed'
        },
        files: {
          'public/src/assets/css/style.min.css': 'public/src/assets/css/style.scss'
        }
      }

    },





    // Watch on SCSS and JS files
    //
    watch: {
      sass: {
        files: ['public/src/assets/css/**/*.scss'],
        tasks: ['sass:compressed']
      },
      css: {
        files: ['public/src/assets/css/*.css', '!public/src/assets/css/*.min.css'],
        tasks: ['css']
      },
      js: {
        files: ['public/src/assets/js/*.js', '!public/src/assets/js/*.min.js'],
        tasks: ['js']
      },
      script_dir: {
        files: ['public/src/assets/js/script/*.js', 'public/src/assets/js/script/**/*.js'],
        tasks: ['neuter:js']
      },
    },





    // Browser Sync
    //
    browserSync: {
      dev: {
        bsFiles: {
          src : [
            'public/src/assets/css/*.min.css',
            'public/src/assets/js/*.min.js',
            'public/src/**/*.html'
          ]
        },
        options: {
          watchTask: true,
          server: "public/src"
        }
      }
    },





    // Clean files and directories
    //
    clean: {
      dist: ['dist'],

      dist_copied: [
        'dist/assets/css/*',
        'dist/assets/css/scss/',
        'dist/assets/css/sass/',
        'dist/assets/scss/',
        'dist/assets/sass/',
        'dist/assets/js/*',
        '!dist/assets/css/*.min.css',
        '!dist/assets/js/*.min.js',
      ]
    },





    // Copy files
    //
    copy: {

      dist: {
        files: [
          {expand: true, cwd: 'src/', src: ['**'], dest: 'dist'}
        ],
      },

    },






    // Import file for script.js
    //
    neuter: {
      options: {
        template: "{%= src %}"
      },
      js: {
        src: 'public/src/assets/js/script/main.js',
        dest: 'public/src/assets/js/script.js'
      },
    },





    // Uglify JS files
    //
    uglify: {
      options: {
        mangle: true,
        preserveComments: /^!|@preserve|@license|@cc_on/i,
        banner: '<%= banner %>'
      },
      script: {
        src:  'public/src/assets/js/script.js',
        dest: 'public/src/assets/js/script.min.js'
      }
    },




    // Do some post processing on CSS files
    //
    postcss: {
      options: {
        processors: [
          autoprefixer,
          require('postcss-flexbugs-fixes')
        ]
      },
      style: {
        src: 'public/src/assets/css/style.min.css'
      },
    },





    // Minify CSS files
    //
    cssmin: {
      options: {
        sourceMap: false,
        advanced:  false
      },
      core: {
        src:  'public/src/assets/css/style.css',
        dest: 'public/src/assets/css/style.min.css'
      }
    },



    // -------------------------------------------------------------------------------
    // END Task configuration

  });


  // These plugins provide necessary tasks.
  require('load-grunt-tasks')(grunt, { scope: 'devDependencies', pattern: ['grunt-*'] });
  require('autoprefixer')(grunt);
  //require('time-grunt')(grunt);


  // Run "grunt" to watch SCSS and JS files as well as running browser-sync
  grunt.registerTask('default', ['serve']);
  grunt.registerTask('dist', ['build']);
  grunt.registerTask('serve', ['browserSync', 'watch']);


  // Run "grunt build" to publish the template in a ./dist folder
  grunt.registerTask('build',
    [
      'clean:dist',
      'sass',
      'css',
      'js',
      'copy:dist',
      'clean:dist_copied'
    ]
  );

  grunt.registerTask( 'css', ['cssmin', 'postcss'] );

  grunt.registerTask( 'js', ['neuter:js', 'uglify'] );


};
