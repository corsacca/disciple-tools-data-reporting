<?php

if ( defined( 'ABSPATH' ) ) {
    return; // return unless accessed directly
}
if ( ! function_exists( 'dt_write_log' ) ) {
    /**
     * A function to assist development only.
     * This function allows you to post a string, array, or object to the WP_DEBUG log.
     *
     * @param $log
     */
    function dt_write_log( $log ) {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
// @codingStandardsIgnoreLine
require( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-load.php' ); // loads the wp framework when called
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-tools.php' );

$data_type = isset( $_GET['type'] ) ? esc_url_raw( wp_unslash( $_GET['type'] ) ) : '';
switch ( $data_type ) {
    case 'contact_activity':
        [ $columns, $items ] = DT_Data_Reporting_Tools::get_contact_activity();
        break;
    case 'contacts':
    default:
        [ $columns, $items ] = DT_Data_Reporting_Tools::get_contacts();
        break;
}


// output headers so that the file is downloaded rather than displayed
header( 'Content-Type: text/json; charset=utf-8' );
header( 'Content-Disposition: attachment; filename=data.json' );

// create a file pointer connected to the output stream
$output = fopen( 'php://output', 'w' );

// loop over the rows, outputting them
foreach ($items as $row ) {
    fwrite( $output, json_encode( $row ).PHP_EOL );
}
fclose( $output );
