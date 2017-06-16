<?php

$spfs_store_post_tmp = false;
$spfs_store_files_tmp = [];

//This is the earliest hook point in wordpress
//We need it because most of the form plugins will use the function move_uploaded_file
//After this function was called we are not able to grab the uploaded files
add_action('plugins_loaded', function(){

  global $spfs_store_post_tmp;
  global $spfs_store_files_tmp;

  $store_this_request = false;

  //Check if this request shoud be stored
  $record_trigger_keys = explode(',',get_option('spfs_record_trigger_keys','store_form'));

  foreach($record_trigger_keys as $record_trigger_key){

    $record_trigger_key = trim($record_trigger_key);

    foreach($_REQUEST as $key => $value){
      if ($key==$record_trigger_key) {
        $store_this_request = true;
        break;
      }
    }

  }

  //Perform a simple honeypod check
  $honeypod_request_keys = explode(',',get_option('spfs_honeypod_request_keys'));

  foreach($honeypod_request_keys as $honeypod_request_key){

    $honeypod_request_key = trim($honeypod_request_key);

    foreach($_REQUEST as $key => $value){
      if ($key==$honeypod_request_key) {
        if($value!=''){//This field has to be empty!
          $store_this_request = false;
        }
        break;
      }
    }

  }

  //Get Post Vars
  if($store_this_request){//Store this request

    //Get allowed file extensions
    $allowed_file_extensions = explode(',',get_option('spfs_allowed_file_extensions','pdf, doc, docx, xls, xlsx, txt'));
    foreach($allowed_file_extensions as &$allowed_file_extension){
      $allowed_file_extension = trim($allowed_file_extension);
    }

    //Get allowed request keys
    $allowed_request_keys = explode(',',get_option('spfs_allowed_request_keys',''));

    //Remove whitespace
    foreach($allowed_request_keys as &$allowed_request_key){
      $allowed_request_key = trim($allowed_request_key);
    }

    //Build post text
    $post_text = '';
    foreach($_REQUEST as $key => $value){
      if (in_array($key,$allowed_request_keys)){//If this key is allowed
        if($value==''){
          $value='-----';
        }
        $post_text.='<b>'.$key.':</b> '.$value.'<br/>';
      }
    }

    //Temporary store the post data until the wordpress database is ready
    $spfs_store_post_tmp = [
      'post_title'    =>  'Form submission from '.date('d.m.Y').' at '.date('H:i:s'),
      'post_type'     =>  'sp_form_post',//Lets create a post type for this
      'post_excerpt'  =>  $post_text,
      'post_content'  =>  $post_text
    ];

    //Store uploaded files
    foreach ($_FILES as $key => $file) {

      if (in_array($key,$allowed_request_keys)){//If this is allowed

        if ($file['error'] == UPLOAD_ERR_OK) {

          //Get extension
          $file_extension = explode('.',$file["name"]);
          $file_extension = $file_extension[count($file_extension)-1];

          if (in_array($file_extension,$allowed_file_extensions)) {//Diese Dateiendung ist erlaubt

            $file_name = $post_id.'_'.$file["name"];//Name für die hochgeladene Datei erzeugen
            $uploaddir = wp_upload_dir();
            $uploaddir = $uploaddir['basedir'];

            //Copy the file instead of moving it because other plugins have to work with the files after it was uploaded
            //For example contactform7 will not be able to send the files if you use move_uploaded_file() here
            copy($file["tmp_name"], $uploaddir.'/sp-form-storage/'.$file_name);

            //Store the filedata temporary until the wp database is ready
            array_push($spfs_store_files_tmp, [
              'name' => $file_name,
              'original_name' => $file["name"],
              'download_hash' => md5(rand().time().'9585212882'),//Generate a hash with a seed. This is an random identifier for file downloads
              'request_key' => $key
            ]);

          }

        }

      }

    }

  }

});


add_action('init', function(){

  global $spfs_store_post_tmp;
  global $spfs_store_files_tmp;

  //Now the wp database is ready. Lets insert the post and its files
  if($spfs_store_post_tmp){

    global $wpdb;

    $post_id = wp_insert_post($spfs_store_post_tmp);

    if(count($spfs_store_files_tmp)){

      foreach($spfs_store_files_tmp as $store_file){

        $store_file['wordpress_post'] = $post_id;

        //Add post meta for this file
        add_post_meta($post_id, 'post_file', $store_file['file_name']);

        //Insert file to db
        $count = $wpdb->insert( $wpdb->prefix.'spfs_files', $store_file);

      }
    }

  }

});
