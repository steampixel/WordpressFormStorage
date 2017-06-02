<?php

add_action('init', function(){

  //Register a new post type
  //So we are able to view the form submissions in the backend
  register_post_type(
    'sp_form_post',//This is the name of our new post type
    array (
      'can_export'          => true,
      'exclude_from_search' => true,
      'has_archive'         => false,
      'hierarchical'        => false,
      'description'         => 'All form submissions',//Description
      'label'               => 'Posted forms',//Plural
      'labels'              => array(
        'name'                => 'Posted forms'//Plural
      ),
      'menu_position'       => 5,//Position in menu
      'public'              => true,
      'publicly_queryable'  => true,
      'query_var'           => 'cpttest',
      'rewrite'             => array ( 'slug' => 'submitted-forms' ),//Create a slug for this post type
      'show_ui'             => true,
      'show_in_menu'        => true,//Show this type in backend menu
      'show_in_nav_menus'   => true,
      'supports'            => array (),
      'show_ui'             => true,
      'show_in_admin_bar'   => true,
      'capability_type'     => 'post',
      'capabilities' => array(
        //'create_posts' => 'do_not_allow'
      ),
      'menu_icon'           => 'dashicons-email-alt',//Create icon
      'register_meta_box_cb'=> function(){
        add_meta_box('dp_form_storage_files', 'Send files', function(){

          //Funktion um das meta-box-html zu generieren
          global $post;
          global $wpdb;
          $uploaded_files = $wpdb->get_results( 'SELECT * FROM `'.$wpdb->prefix.'spfs_files` WHERE wordpress_post='.$post->ID, OBJECT );
          if($uploaded_files){
            foreach($uploaded_files as $uploaded_file){
              echo '<b>'.$uploaded_file->request_key.':</b> <a href="'.get_site_url().'?spfs_download='.$uploaded_file->download_hash.'">'.$uploaded_file->original_name.'</a><br />';
            }
          }

        }, 'sp_form_post', 'side', 'default');
      }
    )
  );
});

//Describe the crud column fields for our new post type
add_action('manage_sp_form_post_posts_custom_column', function($column){

  switch( $column ) {

    case 'spfs_date' :
      the_date();
      break;

    case 'spfs_link' :
      global $post;
      edit_post_link('View this submission', '', '', $post->ID );
      break;

    case 'spfs_values' :
      the_content();
      break;

    case 'spfs_files' :
      //Generate download links
      global $post;
      global $wpdb;
      $uploaded_files = $wpdb->get_results( 'SELECT * FROM `'.$wpdb->prefix.'spfs_files` WHERE wordpress_post='.$post->ID, OBJECT );
      if($uploaded_files){
        foreach($uploaded_files as $uploaded_file){
          echo '<b>'.$uploaded_file->request_key.':</b> <a href="'.get_site_url().'?spfs_download='.$uploaded_file->download_hash.'">'.$uploaded_file->original_name.'</a><br />';
        }
      }
      break;

    default :
      break;
  }

});

//Describe the crud column header fields for our new post type
add_action('manage_edit-sp_form_post_columns', function($columns){

  $columns = array(
    'cb' => '<input type="checkbox" />',//Check all function
    'spfs_date' => 'Date',
    'spfs_link' => '',
    'spfs_values' => 'Content',
    'spfs_files' => 'Files'
  );

  return $columns;

});
