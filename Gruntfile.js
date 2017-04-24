module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

		makepot: {
					target: {
						options: {
							domainPath: '/languages/',             // Where to save the POT file.
							exclude: ['lib/edd_templates/.*'],
							mainFile: 'style.css',                 // Main project file.
							potFilename: 'reussite-personnelle.pot',              // Name of the POT file.
							type: 'wp-theme',                      // Type of project (wp-plugin or wp-theme).
							processPot: function( pot, options ) {
								pot.headers['x-poedit-sourcecharset'] = 'utf-8\n';
								pot.headers['x-poedit-keywordslist'] = '__;_e;_x;esc_html_e;esc_html__;esc_attr_e;esc_attr__;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;_x:1,2c;_n:1,2;_n_noop:1,2;__ngettext_noop:1,2;_c,_nc:4c,1,2;\n';
								pot.headers['x-textdomain-support'] = 'yes\n';
								return pot;
							}
						}
					}
				}
  });

  // Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

  // Default task(s).
  grunt.registerTask('default', ['makepot']);

};
