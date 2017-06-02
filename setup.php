<?PHP

//Setup the plugin
register_activation_hook($spfs_plugin_dir.'/sp-form-storage.php', function(){

  //create upload folder for storing the files
  $uploaddir = wp_upload_dir();
  $uploaddir = $uploaddir['basedir'];
  @mkdir($uploaddir.'/sp-form-storage');

  //create .htaccess file
  @file_put_contents($uploaddir.'/sp-form-storage/.htaccess','deny from all');

  //create table for storing files in db
  global $wpdb;

  $wpdb->query('

    CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'spfs_files` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `original_name` varchar(255) NOT NULL,
      `download_hash` varchar(255) NOT NULL,
      `request_key` varchar(255) NOT NULL,
      `wordpress_post` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

  ');

});
