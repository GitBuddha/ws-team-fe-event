<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . WPINC .'/registration.php';

/**
 * WST Admin
 *
 * @class       WST_Admin
 * @version     1.0.0
 * @package     WST/Classes
 * @category    Class
 */
class WST_Admin {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'wst_scripts' ), 99 );
		add_shortcode( 'wst_fe_event_form', array( &$this, 'wst_fe_event_form' ) );

		// ajax
		add_action( 'wp_ajax_nopriv_get_extra_tt', array( &$this, 'ajax_get_extra_tt' ) );
		add_action( 'wp_ajax_get_extra_tt', array( &$this, 'ajax_get_extra_tt' ) );

		add_action( 'wp_ajax_nopriv_front_end_create_events', array( &$this, 'ajax_front_end_create_events' ) );
		add_action( 'wp_ajax_front_end_create_events', array( &$this, 'ajax_front_end_create_events' ) );

		add_action( 'wp_ajax_nopriv_get_registration_form', array( &$this, 'ajax_get_registration_form' ) );
		add_action( 'wp_ajax_get_registration_form', array( &$this, 'ajax_get_registration_form' ) );
	}


	/**
	 * Admin Scripts
	 */
	public function wst_scripts() {

	    // add datepicker
		wp_enqueue_style('wst-datepicker-ui', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css?ver=1.11.4', array(), WST_VERSION, 'all');
		wp_enqueue_style('wst-datepicker-css', WST_ASSETS . '/css/datepicker.min.css', array(), WST_VERSION, 'all');
		wp_enqueue_script( 'wst-datepicker-js', WST_ASSETS . '/js/datepicker.min.js', array('jquery'), WST_VERSION, true );
		wp_enqueue_script( 'wst-datepicker-en-js', WST_ASSETS . '/js/datepicker-en.js', array('wst-datepicker-js'), WST_VERSION, true );

		wp_enqueue_style('wst-style', WST_ASSETS . '/css/style.css', array(), WST_VERSION, 'all');
		wp_enqueue_script( 'wst-ajax-scripts', WST_ASSETS . '/js/ajax-scripts.js', array('jquery'), WST_VERSION, true );

		wp_enqueue_media();
		wp_localize_script( 'wst-ajax-scripts', 'wst_ajax',
			array(
				'url' => admin_url('admin-ajax.php')
			)
		);
	}

	/**
	 * Get other templates (e.g. files table) passing attributes and including the file.
	 *
	 * @access public
	 * @param string $template_name
	 * @param string $path (default: '')
	 * @param array $t_args (default: array())
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	function get_template( $template_name, $path = '', $t_args = array(), $echo = false ) {
		return WST()->templates()->get_template( $template_name, $path, $t_args, $echo );
	}

	/**
     * FE Event Shortcode
	 * @param $atts
	 *
	 * @return string|void
	 */
	public function wst_fe_event_form( $atts ) {
		$data['time_data'] = $this->get_time_data();
		return $this->get_template( '/event/event-registration.php', WST_TEMPLATES, $data );
	}

	/**
	 * Ajax Create new Ticket Type Form
	 */
	public function ajax_get_extra_tt() {
		if ( ! isset( $_POST['extra_row_id'] ) ) {
			wp_send_json( array( 'status' => false ) );
		}
		$extra_row_id = isset( $_POST['extra_row_id'] ) ? ++$_POST['extra_row_id'] : 0;
		ob_start();
		$time_data = $this->get_time_data();
		?>
		<div class="extra-row-counter" data-extra="<?php echo $extra_row_id ?>">
			<!-- Ticket Title -->
			<div class="wst-ticket-type tt-title">
				<p><label for="tt-title"><?php _e('Ticket Title', 'wst') ?></label></p>
				<input type="text" id="tt-title" name="tt_title" value="">
			</div>

			<!-- Ticket Price -->
			<div class="wst-ticket-type tt-price">
				<p><label for="tt-price"><?php _e('Ticket Price', 'wst') ?></label></p>
				<input type="number" min="0" id="tt-price" name="tt_price" value="">
			</div>

			<!-- Ticket Stock -->
			<div class="wst-ticket-type tt-stock">
				<p><label for="tt-stock"><?php _e('Ticket Stock (number in stock)', 'wst') ?></label></p>
				<input type="number" min="0" id="tt-stock" name="tt_stock" value="">
			</div>

            <!-- Ticket Allowed From -->
            <div class="wst-event-info date-time">
                <h3><?php _e('Ticket Allowed From', 'wst') ?></h3>
                <!-- From Date -->
                <div class="venue__element">
                    <p><label for="tt-start-date-<?php echo $extra_row_id ?>"><?php _e('Date', 'wst') ?></label></p>
                    <input type="text" id="tt-start-date-<?php echo $extra_row_id ?>" name="tt_start_date" data-language='en' value="" required>
                </div>
                <!-- From Time -->
                <div class="venue__element">
                    <p><label for="tt-start-time"><?php _e('Time', 'wst') ?></label></p>
                    <select id="tt-start-time" name="tt_start_time">
						<?php if ( ! empty( $time_data ) ) : ?>
                            <?php foreach ( $time_data as $key => $time ) : ?>
                                <option value="<?php echo $time ?>" <?php echo $key == 1 ? 'selected' : '' ?> ><?php echo $time ?></option>
                            <?php endforeach; ?>
						<?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Ticket Allowed Until -->
            <div class="wst-event-info date-time">
                <h3><?php _e('Ticket Allowed Until', 'wst') ?></h3>
                <!-- Until Date -->
                <div class="venue__element">
                    <p><label for="tt-end-date-<?php echo $extra_row_id ?>"><?php _e('Date', 'wst') ?></label></p>
                    <input type="text" id="tt-end-date-<?php echo $extra_row_id ?>" name="tt_end_date" data-language='en' value="" required>
                </div>
                <!-- Until Time -->
                <div class="venue__element">
                    <p><label for="tt-end-time"><?php _e('Time', 'wst') ?></label></p>
                    <select id="tt-end-time" name="tt_end_time">
						<?php if ( ! empty( $time_data ) ) : ?>
                            <?php foreach ( $time_data as $key => $time ) : ?>
                                <option value="<?php echo $time ?>" <?php echo $key == 1 ? 'selected' : '' ?> ><?php echo $time ?></option>
                            <?php endforeach; ?>
						<?php endif; ?>
                    </select>
                </div>
            </div>

			<!-- Add New Ticket Type -->
			<div id="add_delete_row">
				<span class="add_new_row"><?php _e( '+', 'wst' ) ?></span>
				<span class="delete_new_row"><?php _e( '-', 'wst' ) ?></span>
			</div>
            <script>
                jQuery(document).ready(function() {
                    console.log('second');
                    jQuery('#tt-start-date-<?php echo $extra_row_id ?>').datepicker({
                        dateFormat: 'yyyy-mm-dd',
                        language: 'en',
                    });
                    jQuery('#tt-end-date-<?php echo $extra_row_id ?>').datepicker({
                        dateFormat: 'yyyy-mm-dd',
                        language: 'en',
                    });
                });
            </script>
		</div>

		<?php
		$html = ob_get_clean();
		wp_send_json( array( 'status' => true, 'content' => $html ) );
	}

	/**
	 * Ajax Get Form for Contributor
	 */
	public function ajax_get_registration_form() {
		if ( ! isset( $_POST['user_type'] ) ) {
			wp_send_json( array( 'status' => false ) );
		}
		ob_start(); ?>
        <?php if ( 'existing' === $_POST['user_type'] ) : ?>
            <h3><?php _e('Registered User', 'wst') ?></h3>
            <div class="wst-event-info">
                <!-- User Email -->
                <div class="venue__element">
                    <p><label for="user-email-registered"><?php _e('User Email', 'wst') ?></label></p>
                    <input type="email" id="user-email-registered"" name="user_email_registered" value="" pattern="^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$" required>
                </div>
                <!-- User Password -->
                <div class="venue__element">
                    <p><label for="user-password-registered"><?php _e('User Password', 'wst') ?></label></p>
                    <input type="password" id="user-password-registered" name="user_password_registered" value="" pattern=".{6,}" title="Six or more characters" required>
                </div>
            </div>
        <?php else : ?>
            <h3><?php _e('New User Registration', 'wst') ?></h3>
            <div class="wst-event-info">
                <!-- User Company -->
                <div class="venue__element">
                    <p><label for="user-company"><?php _e('Organizer Company', 'wst') ?></label></p>
                    <input type="text" id="user-company" name="user_company" value="" required>
                </div>
                <!-- User Name -->
                <div class="venue__element">
                    <p><label for="user-name"><?php _e('User Name', 'wst') ?></label></p>
                    <input type="text" id="user-name" name="user_name" value="" required>
                </div>
            </div>

            <div class="wst-event-info">
                <!-- User Email -->
                <div class="venue__element">
                    <p><label for="user-email"><?php _e('User Email', 'wst') ?></label></p>
                    <input type="email" id="user-email" name="user_email" value="" pattern="^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$" required>
                </div>
                <!-- User Password -->
                <div class="venue__element">
                    <p><label for="user-password"><?php _e('User Password', 'wst') ?></label></p>
                    <input type="password" id="user-password" name="user_password" value="" pattern=".{6,}" title="Six or more characters" required>
                </div>
            </div>

            <div class="wst-event-info">
                <!-- User Phone -->
                <div class="venue__element">
                    <p><label for="user-phone"><?php _e('User Phone', 'wst') ?></label></p>
                    <input type="text" id="user-phone" name="user_phone" value="" required>
                </div>

                <!-- User Website -->
                <div class="venue__element">
                    <p><label for="user-web"><?php _e('User Website', 'wst') ?></label></p>
                    <input type="text" id="user-web" name="user_web" value="" pattern="^((https?|ftp)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})(\/?)$" required>
                </div>
            </div>
        <?php endif; ?>
        <?php
		$html = ob_get_clean();
		wp_send_json( array( 'status' => true, 'content' => $html ) );
    }

	/**
     * Main Ajax
     * Create all types of posts
	 * @throws Exception
	 */
	public function ajax_front_end_create_events() {
		$options = array();
		if ( ! empty( $_POST['form_data'] ) ) {
		    $form_data = explode( '&', str_replace( '+', ' ', urldecode( $_POST['form_data'] ) ) );
		    foreach ( $form_data as $fd_key => $form_value ) {
		        $value = explode('=', $form_value );
		        if ( stripos( $value[0], 'event_' ) !== false || stripos( $value[0], 'venue_' ) !== false || stripos( $value[0], 'user_' ) !== false ) {
			        if ( 'event_category' === $value[0] ) {
				        $options[$value[0]][] = $value[1];
			        } else {
				        $options[$value[0]] = $value[1];
                    }
                } else {
                    $options[$value[0]][] = $value[1];
                }
            }
		}

		if ( ! empty( $_POST['event_description'] ) ) {
			$options['event_description'] = urldecode( $_POST['event_description'] );
		}

		if ( ! empty( $_POST['geo_location'] ) ) {
			$options['geo_location'] = urldecode( $_POST['geo_location'] );
        }

		// Geo location
		WST()->google()->setMapApiKey( 'AIzaSyDsDg91ttiKxcMGzz5l3dyEsN2984NsNbs' );
		$address = WST()->google()->urlencode_address( $options['venue_name'] . ' ' . $options['venue_location'] );
		$options['geo_location'] = WST()->google()->getCoordinates( $address );
		$options['existing'] = 0;

        // Create New Contributor
		if ( is_user_logged_in() ) {
			$options['post_author'] = get_current_user_id();
			$options['existing'] = 1;
		} elseif( 'existing' === $options['user_type'] ) {
		    // get data of current user from DB
			$registered_user_id = email_exists( $options['user_email_registered'] );
			if ( $registered_user_id ) {
				$user = get_userdata( $registered_user_id );
				if ( wp_check_password( $options['user_password_registered'], $user->data->user_pass ) ) {
					$options['post_author'] = $registered_user_id;
					$options['existing'] = 1;
				} else {
					wp_send_json( array( 'status' => false, 'content' => __( 'Password is Incorrect', 'wst' ) ) );
                }
            } else {
				wp_send_json( array( 'status' => false, 'content' => __( 'Login or Password is Incorrect', 'wst' ) ) );
            }
        } else {
		    $user_login = str_replace( ' ', '.', $options['user_name'] );
		    $user_nicename = str_replace( ' ', '-', $options['user_name'] );

		    $ext_name = explode(' ', $options['user_name'] );
		    $first_name = $ext_name[0] ? $ext_name[0] : '';
		    $last_name = $ext_name[01] ? $ext_name[1] : '';

			$userdata = array(
				'user_pass'       => $options['user_password'],
				'user_login'      => $user_login,
				'user_nicename'   => $user_nicename,
				'user_url'        => $options['user_web'],
				'user_email'      => $options['user_email'],
				'display_name'    => wp_strip_all_tags( $options['user_name'] ),
				'nickname'        => $first_name . $last_name,
				'first_name'      => $first_name,
				'last_name'       => $last_name,
				'description'     => '',
				'rich_editing'    => 'true',
				'user_registered' => current_time('mysql'),
				'role'            => 'contributor',
				'jabber'          => '',
				'aim'             => '',
				'yim'             => '',
			);

			$options['post_author'] = wp_insert_user( $userdata );
			update_user_meta( $options['post_author'], 'billing_phone', $options['user_phone'] );
			update_user_meta( $options['post_author'], 'billing_company', $options['user_company'] );
			update_user_meta( $options['post_author'], 'billing_first_name', $first_name );
			update_user_meta( $options['post_author'], 'billing_last_name', $last_name );
			update_user_meta( $options['post_author'], 'billing_email', $options['user_email'] );
			update_user_meta( $options['post_author'], 'full_name', strtolower( $options['user_name'] ) );
			update_user_meta( $options['post_author'], 'show_admin_bar_front', true );
        }

		$max_price_index = array_search( max( $options['tt_price'] ), $options['tt_price'] );
		$options['max_price_index'] = $options['tt_price'][$max_price_index];

		// create event post
		$options['event_name'] = $this->create_tc_event_post($options);
		wp_set_post_terms( $options['event_name'], $options['event_category'], 'event_category', true );

		// Add event id ro the list of contributor events
		$event_list = array();
		if ( $options['existing'] ) {
			$event_list = maybe_unserialize( get_user_meta( $options['post_author'], 'hs_contributor_event_list', true ) );
        }
		$event_list[] = $options['event_name'];
		update_user_meta( $options['post_author'], 'hs_contributor_event_list', $event_list );

		// Event Logo
		$options['event_image_id'] = '';
		$options['event_image_url'] = '';
		if ( ! empty( $_FILES ) ) {
			$event_image = $this->download_image( $_FILES, $options['event_name'] );
			$options['event_image_id'] = $event_image[0];
			$options['event_image_url'] = $event_image[1];
			update_post_meta( $options['event_name'], 'event_logo_file_url', $options['event_image_url'] );
        }

		// create woo event product
		$woo_event_product_id = $this->create_tc_event_wc_product( $options );

		// create ticket-type products and variation products of event
        $tickets_length = count( $options['tt_title'] );
        for ( $i = 0; $i < $tickets_length; $i++ ) {

	        $tt_wc_product = $this->create_ticket_type_wc_product( $i, $options );

            // add category to woo ticket type | add biljettyp - id: 323
            $options['event_category'] = 323;
            wp_set_post_terms( $tt_wc_product, $options['event_category'], 'product_cat', true );

	        // create variable products
	        $this->create_tc_event_wc_variation_product( $woo_event_product_id, $i, $options );
        }

        $this->create_page_with_map_widget( $options, $woo_event_product_id );

        // Send notification to admin
        $this->send_mail_to_admin( $options, $woo_event_product_id );
        $this->send_mail_to_contributor( $options );

		wp_send_json( array( 'status' => true ) );
	}

	/**
	 * Create TC Event and Meta data
	 * @return int|WP_Error
	 */
	public function create_tc_event_post( $options ) {
		$new_post = array(
			'post_author'      => $options['post_author'],
			'post_title'       => wp_strip_all_tags($options['event_title']),
			'post_content'     => $options['event_description'],
			'post_status'      => 'publish',
			'post_type'        => 'tc_events',
			'post_category'    => $options['event_category'],
			'post_date'        => date( 'Y-m-d H:i:s' ),
		);

		$event_post_id = wp_insert_post( $new_post );

		update_post_meta( $event_post_id, 'event_date_time', $options['event_start_date'] . ' ' . $options['event_start_time'] );
		update_post_meta( $event_post_id, 'event_end_date_time', $options['event_end_date'] . ' ' . $options['event_end_time'] );
		update_post_meta( $event_post_id, 'event_location', $options['venue_name'] . ', ' . $options['venue_location'] );
		update_post_meta( $event_post_id, 'event_terms', $options['event_terms'] );
		update_post_meta( $event_post_id, 'show_tickets_automatically', 1 );
		update_post_meta( $event_post_id, 'hide_event_after_expiration', '' );
		update_post_meta( $event_post_id, 'sponsors_logo_file_url', '' );

		return $event_post_id;
	}

	/**
     * Create Ticket as a Product
	 * @param $i
	 * @param $options
	 *
	 * @return int|WP_Error
	 */
	public function create_ticket_type_wc_product( $i, $options ) {
	    $post_name =  str_replace( ' ', '-', strtolower( $options['event_title'] ) ) . '-' . str_replace( ' ', '-', strtolower( $options['tt_title'][$i] ) );
		$new_product = array(
			'post_author'  => $options['post_author'],
			'post_title'   => $options['event_title'] . ' - ' . $options['tt_title'][$i],
			'post_name'    => $post_name,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_parent'  => '',
			'post_type'    => 'product',
		);

		$tt_wc_product = wp_insert_post( $new_product );

		update_post_meta( $tt_wc_product, '_regular_price', $options['tt_price'][$i] );
		update_post_meta( $tt_wc_product, '_price', $options['tt_price'][$i] );
		update_post_meta( $tt_wc_product, '_event_name', $options['event_name'] ); // event id
		update_post_meta( $tt_wc_product, '_ticket_template', 3149 ); // default template
		update_post_meta( $tt_wc_product, '_tc_is_ticket', 'yes' );
		update_post_meta( $tt_wc_product, '_stock_status', 'instock' );
		update_post_meta( $tt_wc_product, '_stock', $options['tt_stock'][$i] );
		update_post_meta( $tt_wc_product, '_virtual', 'no' );
		update_post_meta( $tt_wc_product, '_downloadable', 'no' );
		update_post_meta( $tt_wc_product, '_manage_stock', 'yes' );
		update_post_meta( $tt_wc_product, '_backorders', 'no' );
		update_post_meta( $tt_wc_product, '_sold_individually', 'no' );
		update_post_meta( $tt_wc_product, '_tax_status', 'taxable' );
		update_post_meta( $tt_wc_product, '_download_limit', -1 );
		update_post_meta( $tt_wc_product, '_download_expiry', -1 );
		update_post_meta( $tt_wc_product, '_available_checkins_per_ticket', 1 );
		update_post_meta( $tt_wc_product, '_ticket_checkin_availability', 'open_ended' );
		update_post_meta( $tt_wc_product, '_ticket_availability', 'range' );
		update_post_meta( $tt_wc_product, '_owner_form_template', 3686 ); // Attendee Name
		update_post_meta( $tt_wc_product, '_seat_color', '#000000' );
		update_post_meta( $tt_wc_product, '_ticket_availability_from_date', $options['tt_start_date'][$i] . ' ' . $options['tt_start_time'][$i] );
		update_post_meta( $tt_wc_product, '_ticket_availability_to_date', $options['tt_end_date'][$i] . ' ' . $options['tt_end_time'][$i] );
		update_post_meta( $tt_wc_product, '_wcfm_product_approved_notified', 'yes' );
		update_post_meta( $tt_wc_product, '_enable_role_based_price', 1 );
		update_post_meta( $tt_wc_product, '_role_based_price', array (
			'administrator'         => array ( 'regular_price' => '0', 'selling_price' => '',),
			'editor'                => array ( 'regular_price' => '', 'selling_price' => '',),
			'author'                => array ( 'regular_price' => '', 'selling_price' => '',),
			'contributor'           => array ( 'regular_price' => '0', 'selling_price' => '',),
			'subscriber'            => array ( 'regular_price' => '', 'selling_price' => '',),
			'customer'              => array ( 'regular_price' => '', 'selling_price' => '',),
			'shop_manager'          => array ( 'regular_price' => '', 'selling_price' => '',),
			'staff'                 => array ( 'regular_price' => '', 'selling_price' => '',),
			'disable_vendor'        => array ( 'regular_price' => '', 'selling_price' => '',),
			'dc_pending_vendor'     => array ( 'regular_price' => '', 'selling_price' => '',),
			'dc_rejected_vendor'    => array ( 'regular_price' => '', 'selling_price' => '',),
			'dc_vendor'             => array ( 'regular_price' => '', 'selling_price' => '',),
			'translator'            => array ( 'regular_price' => '', 'selling_price' => '',),
			'css_js_designer'       => array ( 'regular_price' => '', 'selling_price' => '',),
			'logedout'              => array ( 'regular_price' => '', 'selling_price' => '',),
		) );
		update_post_meta( $tt_wc_product, '_tc_used_for_seatings', 'no' );
		update_post_meta( $tt_wc_product, '_wc_review_count', 0 );
		update_post_meta( $tt_wc_product, '_wc_rating_count', array() );
		update_post_meta( $tt_wc_product, '_sku', '' );

		update_post_meta( $tt_wc_product, '_wc_average_rating', 0 );
		update_post_meta( $tt_wc_product, '_thumbnail_id', $options['event_image_id'] );
		update_post_meta( $tt_wc_product, 'product-fee-name', 'Avgift' );
		update_post_meta( $tt_wc_product, 'product-fee-amount', '6.25%' );
		update_post_meta( $tt_wc_product, 'product-fee-multiplier', 'yes' );
		update_post_meta( $tt_wc_product, 'myticket_title', $options['venue_name'] );
		update_post_meta( $tt_wc_product, 'myticket_address', $options['venue_location'] );
		update_post_meta( $tt_wc_product, 'myticket_datetime', $options['event_start_date'] . ' ' . $options['event_start_time'] );

		return $tt_wc_product;
    }

	/**
     * Create Event Product
	 * @param $options
	 *
	 * @return int|WP_Error
	 */
    public function create_tc_event_wc_product( $options ) {
        global $wpdb;

	    $post_name = str_replace( ' ', '-', strtolower( $options['event_title'] ) );

	    $new_product = array(
		    'post_author'  => $options['post_author'],
		    'post_title'   => $options['event_title'],
		    'post_content' => $options['event_description'],
		    'post_name'    => $post_name,
            'post_status'  => 'publish',
		    'post_parent'  => '',
		    'post_type'    => 'product',
	    );

	    $product_id = wp_insert_post( $new_product );

	    update_post_meta( $product_id, 'myticket_title', $options['venue_name'] );
	    update_post_meta( $product_id, 'myticket_address', $options['venue_location'] );
	    update_post_meta( $product_id, 'myticket_datetime', strtotime( $options['event_start_date'] . ' ' . $options['event_start_time'] ) );
	    update_post_meta( $product_id, 'myticket_coordinates', $options['geo_location'] );
	    update_post_meta( $product_id, '_thumbnail_id', $options['event_image_id'] );
	    update_post_meta( $product_id, '_product_image_gallery', $options['event_image_id'] ); // set the same img to gallery

	    update_post_meta( $product_id, '_wc_review_count', 0 );
	    update_post_meta( $product_id, '_wc_average_rating', 0 );
	    update_post_meta( $product_id, '_tax_status', 'taxable' );
	    update_post_meta( $product_id, '_manage_stock', 'no' );
	    update_post_meta( $product_id, '_backorders', 'no' );
	    update_post_meta( $product_id, '_sold_individually', 'no' );
	    update_post_meta( $product_id, '_virtual', 'no' );
	    update_post_meta( $product_id, '_downloadable', 'no' );
	    update_post_meta( $product_id, '_download_limit', -1 );
	    update_post_meta( $product_id, '_download_expiry', -1 );
	    update_post_meta( $product_id, '_stock', null );
	    update_post_meta( $product_id, '_stock_status', 'instock' );
	    update_post_meta( $product_id, 'total_sales', 0 );
	    update_post_meta( $product_id, '_tax_class', '' );
	    update_post_meta( $product_id, '_product_version', WooCommerce::instance()->version );
	    update_post_meta( $product_id, '_edit_last', $options['post_author'] );
	    update_post_meta( $product_id, '_commission_per_product', '' );
	    update_post_meta( $product_id, '_wcmp_cancallation_policy', '' );
	    update_post_meta( $product_id, '_wcmp_refund_policy', '' );
	    update_post_meta( $product_id, '_wcmp_shipping_policy', '' );
	    update_post_meta( $product_id, '_wcfm_product_views', 1 );


	    $tickets_length = count( $options['tt_title'] );
	    if ( $tickets_length > 1 ) {
		    update_post_meta( $product_id, '_product_attributes', array (
			    'biljettyp' =>
				    array (
					    'name' => 'Biljettyp',
					    'value' => 'Early Bird | Inträde',
					    'position' => 0,
					    'is_visible' => 1,
					    'is_variation' => 1,
					    'is_taxonomy' => 0,
				    ),
		    ) );

		    // Add to terms
		    $wpdb->insert(
			    $wpdb->prefix . 'term_relationships',
			    array( 'object_id' => $product_id, 'term_taxonomy_id' => 4, 'term_order' => 0 ));
        }

	    update_post_meta( $product_id, 'myticket_action', 'product' );
	    update_post_meta( $product_id, '_regular_price', '' );
	    update_post_meta( $product_id, '_sale_price', '' );
	    update_post_meta( $product_id, 'myticket_link', get_site_url() . '/' . $post_name );


	    return $product_id;
    }

	/**
     * Create variation product (iteration)
	 * @param $woo_event_product_id
	 * @param $i
	 * @param $options
	 *
	 * @return int|WP_Error
	 */
    public function create_tc_event_wc_variation_product( $woo_event_product_id, $i, $options ) {
	    $tickets_length = count( $options['tt_title'] );
	    if ( $tickets_length > 1 ) {
		    $title = $options['tt_price'][$i] ==  $options['max_price_index'] ? $options['event_title'] . ' - Inträde' : $options['event_title'] . ' - Early Bird';
		    $post_excerpt = $options['tt_price'][$i] ==  $options['max_price_index'] ? $options['event_title'] . ': Inträde' : $options['event_title'] . ': Early Bird';
	    } else {
	        $title = $options['event_title'];
	        $post_excerpt = $options['event_title'];
        }

        $new_product = array(
            'post_author'  => $options['post_author'],
            'post_title'   => $title,
            'post_excerpt' => $post_excerpt,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_parent'  => $woo_event_product_id,
            'post_type'    => 'product_variation',
            'menu_order'   => $i,
        );

	    $product_id = wp_insert_post( $new_product );

	    add_post_meta( $woo_event_product_id, '_price', $options['tt_price'][$i], false );

	    update_post_meta( $product_id, '_variation_description', '' );
	    update_post_meta( $product_id, '_sku', '' );
	    update_post_meta( $product_id, '_regular_price', $options['tt_price'][$i] );
	    update_post_meta( $product_id, '_sale_price', '' );
	    update_post_meta( $product_id, '_sale_price_dates_from', '' );
	    update_post_meta( $product_id, '_sale_price_dates_to', '' );
	    update_post_meta( $product_id, '_tax_status', 'taxable' );
	    update_post_meta( $product_id, '_tax_class', 'parent' );
	    update_post_meta( $product_id, '_manage_stock', 'no' );
	    update_post_meta( $product_id, '_backorders', 'no' );
	    update_post_meta( $product_id, '_low_stock_amount', '' );
	    update_post_meta( $product_id, '_sold_individually', 'no' );
	    update_post_meta( $product_id, '_weight', '' );
	    update_post_meta( $product_id, '_length', '' );
	    update_post_meta( $product_id, '_width', '' );
	    update_post_meta( $product_id, '_height', '' );
	    update_post_meta( $product_id, '_purchase_note', '' );
	    update_post_meta( $product_id, '_virtual', 'no' );
	    update_post_meta( $product_id, '_downloadable', 'no' );
	    update_post_meta( $product_id, '_download_limit', -1 );
	    update_post_meta( $product_id, '_download_expiry', -1 );
	    update_post_meta( $product_id, '_stock', null );
	    update_post_meta( $product_id, '_stock_status', 'instock' );
	    update_post_meta( $product_id, '_wc_average_rating', 0 );
	    update_post_meta( $product_id, '_wc_review_count', 0 );

	    if ( $tickets_length > 1 ) {
		    $biljettyp = $options['tt_price'][$i] ==  $options['max_price_index'] ? 'Inträde' : 'Early Bird';
		    update_post_meta( $product_id, 'attribute_biljettyp', $biljettyp );
	    }

	    update_post_meta( $product_id, '_price', $options['tt_price'][$i] );
	    update_post_meta( $product_id, '_product_vendors_commission', '' );

	    return $product_id;

    }

	/**
     * Create Page with Google Map
	 * @param $options
	 * @param $product_event_id
	 */
    public function create_page_with_map_widget( $options, $product_event_id ) {
	    $new_page = array(
		    'post_author'      => $options['post_author'],
		    'post_title'       => wp_strip_all_tags($options['event_title']),
		    'post_content'     => $options['event_description'],
		    'post_status'      => 'publish',
		    'post_type'        => 'page',
		    'post_date'        => date( 'Y-m-d H:i:s' ),
	    );

	    $page_map_widget_id = wp_insert_post( $new_page );

	    update_post_meta( $page_map_widget_id, 'um_content_restriction', array (
		    '_um_custom_access_settings' => '0',
		    '_um_accessible' => '0',
		    '_um_noaccess_action' => '0',
		    '_um_restrict_by_custom_message' => '0',
		    '_um_restrict_custom_message' => '',
		    '_um_access_redirect' => '0',
		    '_um_access_redirect_url' => '',
		    '_um_access_hide_from_queries' => '0',
	    ) );
	    update_post_meta( $page_map_widget_id, 'sfsi-social-media-image', wp_get_attachment_image_url( $options['event_image_id'] ) );
	    update_post_meta( $page_map_widget_id, 'sfsi-fbGLTw-title', $options['event_title'] );
	    update_post_meta( $page_map_widget_id, 'sfsi-fbGLTw-description', $options['event_title'] );
	    update_post_meta( $page_map_widget_id, 'sfsi-pinterest-media-image', '' );
	    update_post_meta( $page_map_widget_id, 'social-pinterest-description', '' );
	    update_post_meta( $page_map_widget_id, 'social-twitter-description', '' );
	    update_post_meta( $page_map_widget_id, 'panels_data', array (
		    'widgets' =>
			    array (
				    0 =>
					    array (
						    'subtitle' => $options['event_title'] . ' - ' . date( 'd M  H:i', strtotime( $options['event_start_date'] . ' ' . $options['event_start_time'] ) ),
						    'event_id' => $product_event_id,
						    'show_header' => true,
						    'show_about' => true,
						    'show_features' => true,
						    'link' => get_permalink( $options['event_name'] ),
						    'features_content' => '<h2>Arrangör</h2>
                            <p><br />
                            <strong>web:</strong> <a href="#">#web</a><br />
                            <strong>E-post:</strong> <a href="#">#E-post</a><br />
                            <strong>Telefon:</strong> #Telefon</p>
                            ',
						    'features_content_selected_editor' => 'tmce',
						    'show_map' => true,
						    'zoom' => 15,
						    'hue' => '#ccc',
						    'saturation' => -80,
						    '_sow_form_id' => '1919905675cc099c765623341149394',
						    '_sow_form_timestamp' => '1556126273281',
						    'panels_info' =>
							    array (
								    'class' => 'myticket_events_single_widget',
								    'grid' => 0,
								    'cell' => 0,
								    'id' => 0,
								    'widget_id' => '02080142-7eaf-43c3-90ae-39e8a865a96d',
								    'style' =>
									    array (
										    'background_image_attachment' => false,
										    'background_display' => 'tile',
									    ),
							    ),
						    'type' => 'simple',
						    'a_repeater' =>
							    array (
							    ),
						    'so_sidebar_emulator_id' => 'myticket_events_single_widget-846010000',
						    'option_name' => 'widget_myticket_events_single_widget',
					    ),
			    ),
		    'grids' =>
			    array (
				    0 =>
					    array (
						    'cells' => 1,
						    'style' =>
							    array (
							    ),
					    ),
			    ),
		    'grid_cells' =>
			    array (
				    0 =>
					    array (
						    'grid' => 0,
						    'index' => 0,
						    'weight' => 1,
						    'style' =>
							    array (
							    ),
					    ),
			    ),
	    ) );
	    update_post_meta( $page_map_widget_id, '_title', 'on' );
	    update_post_meta( $page_map_widget_id, '_narrow_content', 'on' );
    }

	/**
     * Download image
	 * @param $file
	 * @param $post_id
	 *
	 * @return array
	 */
    public function download_image( $file, $post_id ) {
	    add_filter( 'upload_mimes', function( $mimes ){
		    return [
			    'jpg|jpeg|jpe' => 'image/jpeg',
			    'gif'          => 'image/gif',
			    'png'          => 'image/png',
		    ];
	    } );

	    $uploaded_imgs = array();
	    foreach( $file as $file_id => $data ){
		    $attach_id = media_handle_upload( $file_id, $post_id );
            $uploaded_imgs[0] = $attach_id;
            $uploaded_imgs[1] = wp_get_attachment_url( $attach_id );
	    }

	    return $uploaded_imgs;
    }

	/**
     * Time Array
	 * @return array
	 */
    public function get_time_data() {
        return array(
            '00:00', '00:15', '00:30', '00:45',
            '01:00', '01:15', '01:30', '01:45',
            '02:00', '02:15', '02:30', '02:45',
            '03:00', '03:15', '03:30', '03:45',
            '04:00', '04:15', '04:30', '04:45',
            '05:00', '05:15', '05:30', '05:45',
            '06:00', '06:15', '06:30', '06:45',
            '07:00', '07:15', '07:30', '07:45',
            '08:00', '08:15', '08:30', '08:45',
            '09:00', '09:15', '09:30', '09:45',
            '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '11:45',
	        '12:00', '12:15', '12:30', '12:45',
	        '13:00', '13:15', '13:30', '13:45',
	        '14:00', '14:15', '14:30', '14:45',
	        '15:00', '15:15', '15:30', '15:45',
	        '16:00', '16:15', '16:30', '16:45',
	        '17:00', '17:15', '17:30', '17:45',
	        '18:00', '18:15', '18:30', '18:45',
	        '19:00', '19:15', '19:30', '19:45',
	        '20:00', '20:15', '20:30', '20:45',
	        '21:00', '21:15', '21:30', '21:45',
	        '22:00', '22:15', '22:30', '22:45',
	        '23:00', '23:15', '23:30', '23:45',
        );
    }

	/**
     * Send Email Notification to Admin
	 * @param $options
	 * @param $product_id
	 */
    public function send_mail_to_admin( $options, $product_id ) {
	    $to = get_option('admin_email');
	    $subject = __( 'New event: '. $options['event_title'] .' has been added', 'wst' );
	    $message = __( 'New event: '. $options['event_title'] .' has been added from Front Form, click to see: ' . get_permalink( $product_id ), 'wst');
	    wp_mail( $to, $subject, $message );
    }

	/**
     * Send Email Notification to Contributor
	 * @param $options
	 */
    public function send_mail_to_contributor( $options ) {
        $to = get_userdata( $options['post_author'] )->user_email;
	    $subject = __( 'You successfully added new event: '. $options['event_title'], 'wst' );
	    $message = __( 'You successfully added new event: ' . $options['event_title'] . ' from Front Form. Login to your site: https://easytic.eu/organizer', 'wst' );
	    wp_mail( $to, $subject, $message );
    }

}