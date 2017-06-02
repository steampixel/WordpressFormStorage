<?php

add_action('admin_menu',function(){

  //create new top-level menu
  add_submenu_page('edit.php?post_type=sp_form_post', 'Options', 'Optionen', 'manage_options', 'spfs_options_menu', function () {

  ?>

    <div class="wrap">
      <h1>Form Storage Options</h1>

      <form method="post" action="options.php">

          <?php settings_fields( 'spfs-settings-group' ); ?>
          <?php do_settings_sections( 'spfs-settings-group' ); ?>

          <table class="form-table" style="width:100%;">

              <tr valign="top">
                <th scope="row">Request storage triggers (seperate by comma)</th>
                <td>
                  <input style="width:100%;" type="text" name="spfs_record_trigger_keys" value="<?php echo esc_attr( get_option('spfs_record_trigger_keys','store_form') ); ?>" />
                  This request keys will trigger the form storage. One of these must be present while sending the form.
                </td>
              </tr>

              <tr valign="top">
                <th scope="row">Allowed File Extensions (seperate by comma)</th>
                <td>
                  <input style="width:100%;" type="text" name="spfs_allowed_file_extensions" value="<?php echo esc_attr( get_option('spfs_allowed_file_extensions','pdf, doc, docx, odt, txt, jpg, png, gif') ); ?>" />
                  This file extensions will be stored and available for download after form submission. Any other file extension will be ignored.
                </td>
              </tr>

              <tr valign="top">
                <th scope="row">Allowed request keys</th>
                <td>
                  <input style="width:100%;" type="text" name="spfs_allowed_request_keys" value="<?php echo esc_attr( get_option('spfs_allowed_request_keys','vorname, nachname, email') ); ?>" />
                  This keys will be recorded after form submission.
                </td>
              </tr>

          </table>

          <?php submit_button(); ?>

      </form>
    </div>

  <?PHP

  });

});

add_action('admin_init',function(){

  register_setting( 'spfs-settings-group', 'spfs_allowed_file_extensions' );
  register_setting( 'spfs-settings-group', 'spfs_allowed_request_keys' );
  register_setting( 'spfs-settings-group', 'spfs_record_trigger_keys' );

});
