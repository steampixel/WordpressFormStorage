<?php

add_action('wp_loaded', function(){

  //Somebody wants to download a file
  if(isset($_GET['spfs_download'])){

    if(is_user_logged_in()){

      //Get the file hash
      $file_download_hash = $_GET['spfs_download'];

      global $wpdb;

      //Get file from database
      $uploaded_file = $wpdb->get_row( 'SELECT * FROM `'.$wpdb->prefix.'spfs_files` WHERE download_hash="'.$file_download_hash.'"', OBJECT );

      if($uploaded_file){

        $uploaddir = wp_upload_dir();
        $uploaddir = $uploaddir['basedir'];

        $file_path = $uploaddir.'/sp-form-storage/'.$uploaded_file->name;

        if (file_exists($file_path)) {

          //Send the file to client
          header('Content-Description: File Transfer');
          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename="'.$uploaded_file->original_name.'"');
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . filesize($file_path));

          readfile($file_path);

          exit;

        }

      }

    }

    //Something went wrong
    header("HTTP/1.0 404 Not Found");
    echo '<h1>Sorry :-(</h1>';
    echo 'File not found!';

    exit;

  }

});
