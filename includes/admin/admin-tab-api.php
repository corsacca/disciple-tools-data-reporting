<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}

class DT_Data_Reporting_Tab_API
{
    public $type = 'contacts';

    public function __construct( $token, $type )
    {
        $this->token = $token;
        $this->type = $type;
        require_once( plugin_dir_path( __FILE__ ) . '../data-tools.php' );
    }

    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        $endpoint_url = get_option( "dt_data_reporting_endpoint_url" );
        $settings_link = 'admin.php?page='.$this->token.'&tab=settings';
        $endpoint_error = "<p>Endpoint URL not configured. Please update in <a href='$settings_link'>Settings</a></p>";
        switch ($this->type) {
          /*case 'contact_activity':
              [$columns, $rows] = DT_Data_Reporting_Tools::get_contact_activity(false);
              $this->export_data($columns, $rows);
              break;*/
          case 'contacts':
          default:
            [$columns, $rows] = DT_Data_Reporting_Tools::get_contacts(false);
            // todo: handle exports to other URLs for global and maarifa (if enabled)
            if ( empty( $endpoint_url ) ) {
              echo $endpoint_error;
            } else {
              $this->export_data($endpoint_url, $columns, $rows, $this->type);
            }
            break;
        }
    }
    public function export_data($url, $columns, $rows, $type ) {

      echo '<ul>';
      echo '<li>Starting export...';
      $args = [
        'method' => 'POST',
        'headers' => array(
          'Content-Type' => 'application/json; charset=utf-8'
        ),
        'body'      => json_encode([
          'columns' => $columns,
          'items' => $rows,
          'type' => $type,
        ]),
      ];

      $result = wp_remote_post( $url, $args );
      if ( is_wp_error( $result ) ){
        $error_message = $result->get_error_message() ?? '';
        dt_write_log($error_message);
        echo "<li>Error: $error_message</li>";
      } else {
        $result_body = json_decode($result['body']);
        echo "<li><pre><code>".$result['body']."</code></pre>";
//        if (!empty($result_body) && $result_body === true) {
//          return [
//            "success" => true,
//            "message" => $linked,
//          ];
//        }
      }
      echo '<li>Done exporting.</li>';
      echo '</ul>';
    }
}
