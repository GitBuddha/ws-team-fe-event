<?php
/**
 * Template Name: Create New Event
 * Template Description: This template for [wst_fe_event_form] shortcode if user role is WS-Team
 * Template Tags: Event, Registration
 *
 * @author 	WS-Team
 */

//needs for translation
__( 'Create Event', 'wst' );
__( 'This template for [wst_fe_event_form] shortcode if user role is WS-Team', 'wst' );
__( 'Event', 'wst' );

if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

?>

<div id="wst-wrapper">
	<?php if (  is_user_logged_in() && ! current_user_can('contributor') ) : ?>
        <h2><?php _e('You don`t have permissions to add new Event', 'wst') ?></h2>
	<?php else: ?>
    <div class="wst-event__wrapper">
        <form id="wst-event-form" enctype="multipart/form-data" method="post">

            <!-- Event Section -->
            <h2><?php _e('Event Data', 'wst') ?></h2>
            <section class="wst-event__section">
                <!-- Event Title -->
                <div class="wst-event-info info-title">
                    <p><label for="info-title"><?php _e('Event Title', 'wst') ?></label></p>
                    <input type="text" id="info-title" name="event_title" value="" required>
                </div>

                <!-- Event Description -->
                <div class="wst-event-info info-description">
                    <p><label for="info__description"><?php _e('Event Description', 'wst') ?></label></p>
                    <?php wp_editor( '', 'event_description', array(
                        'wpautop'       => 1,
                        'media_buttons' => 1,
                        'textarea_name' => 'event_description',
                        'textarea_rows' => 5,
                        'tabindex'      => null,
                        'editor_css'    => '',
                        'editor_class'  => '',
                        'teeny'         => 0,
                        'dfw'           => 0,
                        'tinymce'       => 1,
                        'quicktags'     => 1,
                        'drag_drop_upload' => false
                    ) ); ?>
                </div>

                <!-- Venue name and venue address -->
                <div class="wst-event-info venue-wrapper">
                    <!-- Venue Name -->
                    <div class="venue__element">
                        <p><label for="venue-name"><?php _e('Venue name', 'wst') ?></label></p>
                        <input type="text" id="venue-name" name="venue_name" value="" required>
                    </div>
                    <!-- Venue Location -->
                    <div class="venue__element">
                        <p><label for="venue-location"><?php _e('Venue address', 'wst') ?></label></p>
                        <input type="text" id="venue-location" name="venue_location" value="" required>
                    </div>
                </div>

                <!-- Event Start Date and Time -->
                <div class="wst-event-info date-time">
                    <!-- Start Date -->
                    <div class="venue__element">
                        <p><label for="event-start-date-time"><?php _e('Start Date', 'wst') ?></label></p>
                        <input type="text" id="event-start-date-time" name="event_start_date" data-language='en' value="" required>
                    </div>

                    <!-- Start Time -->
                    <div class="venue__element">
                        <p><label for="event-start-time"><?php _e('Start Time', 'wst') ?></label></p>
                        <select id="event-start-time" name="event_start_time">
	                        <?php if ( ! empty( $time_data ) ) : ?>
                                <?php foreach ( $time_data as $key => $time ) : ?>
                                    <option value="<?php echo $time ?>" <?php echo $key == 1 ? 'selected' : '' ?> ><?php echo $time ?></option>
                                <?php endforeach; ?>
	                        <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Event End Date and Time -->
                <div class="wst-event-info date-time">
                    <!-- End Date -->
                    <div class="venue__element">
                        <p><label for="event-end-date-time"><?php _e('End Date', 'wst') ?></label></p>
                        <input type="text" id="event-end-date-time" name="event_end_date" data-language='en' value="" required>
                    </div>

                    <!-- End Time -->
                    <div class="venue__element">
                        <p><label for="event-end-time"><?php _e('End Time', 'wst') ?></label></p>
                        <select id="event-end-time" name="event_end_time">
		                    <?php if ( ! empty( $time_data ) ) : ?>
                                <?php foreach ( $time_data as $key => $time ) : ?>
                                    <option value="<?php echo $time ?>" <?php echo $key == 1 ? 'selected' : '' ?> ><?php echo $time ?></option>
                                <?php endforeach; ?>
		                    <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Event Category -->
                <div class="wst-event-info category">
                    <p>
                        <label><?php _e('Event Category', 'wst') ?></label>
                    </p>
                    <?php
                    $taxonomy = 'event_category';
                    $terms = get_terms($taxonomy);
                    if ( $terms && !is_wp_error( $terms ) ) : ?>
                    <ul class="wst-event-category-ul">
		                <?php foreach ( $terms as $key => $term ) { ?>
                            <li>
                                <input type="checkbox" id="event-category-<?php echo $key ?>" name="event_category" value="<?php echo $term->term_id ?>">
                                <label class="non-style" for="event-category-<?php echo $key ?>"><?php echo $term->name; ?></label>
                            </li>
		                <?php } ?>
                    </ul>
                    <?php else : ?>
                    <p><?php _e('Category is not available', 'wst') ?><</p>
                    <?php endif;?>
                </div>

                <!-- Terms and Conditions -->
                <div class="wst-event-info terms-cond">
                    <p>
                        <label><?php _e('Terms and Conditions', 'wst') ?></label>
                    </p>
                    <textarea name="event_terms" id="wst-terms-cond" cols="15" rows="5"></textarea>
                </div>

                <!-- Event Logo -->
                <div class="wst-event-info event-logo">
                    <img class="wst_event_img" width="30%" height="auto" src="" >
                    <input type="file" name="event_logo_file_url" id="event_logo_file_url" accept="image/*" />
                </div>
            </section>

            <hr>

            <!--
            ===================
            Ticket Type Section
            ===================
             -->
            <h2><?php _e('Ticket Type Data', 'wst') ?></h2>
            <section id="wst-ticket-type__section">
                <div class="extra-row-counter" data-extra="0">
                    <!-- Ticket Title -->
                    <div class="wst-ticket-type tt-title">
                        <p><label for="tt-title"><?php _e('Ticket Title', 'wst') ?></label></p>
                        <input type="text" id="tt-title" name="tt_title" value="" required>
                    </div>

                    <!-- Ticket Price -->
                    <div class="wst-ticket-type tt-price">
                        <p><label for="tt-price"><?php _e('Ticket Price', 'wst') ?></label></p>
                        <input type="number" min="0" id="tt-price" name="tt_price" value="" required>
                    </div>

                    <!-- Ticket Stock -->
                    <div class="wst-ticket-type tt-stock">
                        <p><label for="tt-stock"><?php _e('Ticket Stock (number in stock)', 'wst') ?></label></p>
                        <input type="number" min="0" id="tt-stock" name="tt_stock" value="" required>
                    </div>

                    <!-- Ticket Allowed From -->
                    <div class="wst-event-info date-time">
                        <h3><?php _e('Ticket Allowed From', 'wst') ?></h3>
                        <!-- From Date -->
                        <div class="venue__element">
                            <p><label for="tt-start-date"><?php _e('Date', 'wst') ?></label></p>
                            <input type="text" class="tt-start-date" id="tt-start-date" name="tt_start_date" data-language='en' value="" required>
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
                            <p><label for="tt-end-date"><?php _e('Date', 'wst') ?></label></p>
                            <input type="text" class="tt-end-date" id="tt-end-date" name="tt_end_date" data-language='en' value="" required>
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
                    </div>
                </div>
            </section>

            <!--
		   ===================
		   User Data
		   ===================
			-->
            <?php if ( ! is_user_logged_in() ) : ?>
            <h2><?php _e('Contributor Info', 'wst') ?></h2>
            <div class="wst-event-info user-radio-registration">
                <div class="venue__element">
                    <label class="user-radio-label" for="wst-user-type__new">
                        <span><?php _e('New User ', 'wst') ?></span>
                        <input type="radio" id="wst-user-type__new" name="user_type" value="new_user" checked>
                    </label>
                </div>
                <div class="venue__element">
                    <label class="user-radio-label" for="wst-user-type__existing">
                        <span><?php _e('Existing User ', 'wst') ?></span>
                        <input type="radio" id="wst-user-type__existing" name="user_type" value="existing">
                    </label>
                </div>
            </div>

            <section id="wst-user-registration__section">
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
            </section>
            <?php endif; ?>

            <div class="wst-clearfix"></div>

            <input type="submit" id="wst-submit" class="bnt bnt-theme text-regular text-uppercase" value="<?php _e( 'Create', 'wst' ) ?>">

        </form>
    </div>

    <div id="wst-success-backgroud" class="wst-hidden"></div>
    <div id="wst-success-popup" class="wst-hidden">
        <p class="wst-close">x</p>
        <h2><?php _e( 'Your event has been successfully sent.<br>Our managers will contact you after moderation.', 'wst' ) ?></h2>
    </div>
    <?php endif; ?>
</div>
