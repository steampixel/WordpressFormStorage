<?php

add_action('init',function(){

  //This is only relevant for admins
  if(is_super_admin()){

    $uploaddir = wp_upload_dir();
    $uploaddir = $uploaddir['basedir'];

    //Check if storage folder for posted files exists
    //Print a backend admin notice if necessary
    if(!is_dir($uploaddir.'/sp-form-storage')){
      add_action( 'admin_notices', function()use($uploaddir){

        ?>
        <div class="notice notice-error is-dismissible">
            <p>Form-Storage: Cannot find the directory for storing uploaded files. You should create the folder "<?=$uploaddir ?>/sp-form-storage".</p>
        </div>
        <?PHP

      } );

    }

    //Check if storage folder for send files is writeable
    //Print a backend admin notice if necessary
    if(!is_writeable($uploaddir.'/sp-form-storage')){
      add_action('admin_notices', function()use($uploaddir){

        ?>
        <div class="notice notice-error is-dismissible">
            <p>Form-Storage: The folder for storing uploaded files is not writeable! Please correct the permissions for "<?=$uploaddir ?>/sp-form-storage".</p>
        </div>
        <?PHP

      });

    }

    //Check if there is an .htaccess file in the upload dir
    //Print a backend admin notice if necessary
    if(!is_dir($uploaddir.'/sp-form-storage')){
      add_action( 'admin_notices', function()use($uploaddir){

        ?>
        <div class="notice notice-error is-dismissible">
            <p>Form-Storage: There is no .htaccess in "<?=$uploaddir ?>/sp-form-storage"! You should protect this directory by using "deny from all" in case you are using apache!</p>
        </div>
        <?PHP

      } );

    }

  }

});
