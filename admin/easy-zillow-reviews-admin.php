<?php

// Block direct access to plugin PHP files
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Load admin scripts
function load_ezrwp_admin_style($hook) {
        // Load only on ?page=ezrwp
        if($hook != 'settings_page_easy-zillow-reviews') {
                return;
        }
        wp_enqueue_script( 'ezrwp_admin_js',  plugin_dir_url( __FILE__ ) . 'js/admin-scripts.js' );
        wp_enqueue_style( 'ezrwp_admin_css',  plugin_dir_url( __FILE__ ) . 'css/admin-styles.css' );
}
add_action( 'admin_enqueue_scripts', 'load_ezrwp_admin_style' );

// Add custom options and settings for the plugin
function ezrwp_settings_init(){
    
    // Register Zillow Professionals Reviews Settings
    register_setting(
        'ezrwp_professional_reviews', //option group
        'ezrwp_options' // option name
    );
    
    // Register Zillow Lender Reviews Settings
    register_setting(
        'ezrwp_lender_reviews', //option group
        'ezrwp_options' // option name
    );
    
    // Register General Settings
    register_setting(
        'ezrwp_general', //option group
        'ezrwp_options' // option name
    );


    // Register sections in the settings page
    add_settings_section(
        'ezrwp_section_for_zillow_professional_parameters', // id
        __( 'Zillow Professional Profile', 'ezrwp_professional_reviews' ), // title
        'ezrwp_section_for_zillow_professional_parameters_cb', // callback
        'ezrwp_professional_reviews' // page
    );
    add_settings_section(
        'ezrwp_section_for_defaults', // id
        __( 'Default Plugin Settings', 'ezrwp_general' ), // title
        'ezrwp_section_for_defaults_cb', // callback
        'ezrwp_general' // page
    );
    add_settings_section(
        'ezrwp_section_for_appearance', // id
        __( 'Appearance', 'ezrwp_general' ), // title
        'ezrwp_section_for_appearance_cb', // callback
        'ezrwp_general' // page
    );
    add_settings_section(
        'ezrwp_section_for_support', // id
        __( 'Support', 'ezrwp_general' ), // title
        'ezrwp_section_for_support_cb', // callback
        'ezrwp_general' // page
    );

    // Register fields in the section
    add_settings_field(
        'ezrwp_zwsid', // id. Used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Zillow Web Services ID', 'ezrwp_professional_reviews' ), // title
        'ezrwp_zwsid_text_field_cb', // callback
        'ezrwp_professional_reviews', // page
        'ezrwp_section_for_zillow_professional_parameters', [
            'label_for' => 'ezrwp_zwsid', // used for the "for" attribute of the <label>.
            'class' => 'ezrwp_row', // used for the "class" attribute of the <tr> containing the field.
            'ezrwp_custom_data' => 'custom', // custom key value pairs to be used inside your callbacks.
        ] // args
    );
    
    add_settings_field(
        'ezrwp_screenname',
        __( 'Zillow Screenname', 'ezrwp_professional_reviews' ),
        'ezrwp_screenname_text_field_cb',
        'ezrwp_professional_reviews',
        'ezrwp_section_for_zillow_professional_parameters', [
            'label_for' => 'ezrwp_screenname',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_count',
        __( 'Review Count', 'ezrwp_general' ),
        'ezrwp_count_number_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_count',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_layout',
        __( 'Reviews Layout', 'ezrwp_general' ),
        'ezrwp_layout_select_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_layout',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_cols',
        __( 'Grid Columns', 'ezrwp_general' ),
        'ezrwp_cols_number_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_cols',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_disclaimer',
        __( 'Mandatory Disclaimer', 'ezrwp_general' ),
        'ezrwp_disclaimer_pill_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_disclaimer',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_hide_date',
        __( 'Hide Review Date', 'ezrwp_general' ),
        'ezrwp_hide_date_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_hide_date',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
   add_settings_field(
        'ezrwp_hide_stars',
        __( 'Hide Review Stars', 'ezrwp_general' ),
        'ezrwp_hide_stars_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_hide_stars',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_hide_reviewer_summary',
        __( 'Hide Reviewer Summary', 'ezrwp_general' ),
        'ezrwp_hide_reviewer_summary_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_hide_reviewer_summary',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_hide_view_all_link',
        __( 'Hide "View All" Link', 'ezrwp_general' ),
        'ezrwp_hide_view_all_link_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_hide_view_all_link',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_hide_zillow_logo',
        __( 'Hide Zillow Logo', 'ezrwp_general' ),
        'ezrwp_hide_zillow_logo_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_defaults', [
            'label_for' => 'ezrwp_hide_zillow_logo',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_quote_font_size',
        __( 'Review Quote Font Size', 'ezrwp_general' ),
        'ezrwp_quote_font_size_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_appearance', [
            'label_for' => 'ezrwp_quote_font_size',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_review_description_font_size',
        __( 'Reviewer Description Font Size', 'ezrwp_general' ),
        'ezrwp_reviewer_description_font_size_field_cb',
        'ezrwp_general',
        'ezrwp_section_for_appearance', [
            'label_for' => 'ezrwp_reviewer_description_font_size',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
}

// Register settings to the admin_init action hook
add_action( 'admin_init', 'ezrwp_settings_init' );

// Callback Functions
// Section callbacks accept an $args parameter, which is an array.
// $args have the following keys defined: id, title, callback.
// the values are defined at the add_settings_section() function.
function ezrwp_section_for_zillow_professional_parameters_cb( $args ) {
    ?>
    <hr />
    <p id="<?php echo esc_attr( $args['id'] ); ?>">
        For Professional Reviews, sign-up for a <strong>Zillow Web Services ID (ZWSID)</strong> at <a href="https://www.zillow.com/howto/api/APIOverview.htm" target="_blank">https://www.zillow.com/howto/api/APIOverview.htm</a>. <span class="dashicons dashicons-external" style="font-size: 14px;"></span>
    </p>
    <?php
}
function ezrwp_section_for_zillow_lender_parameters_cb( $args ) {
    ?>
    <hr />
    <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
        For Lender Reviews, sign-up for a <strong>Zillow Mortgages Partner ID</strong> at <a href="https://www.zillow.com/mortgage/api/#/" target="_blank">https://www.zillow.com/mortgage/api/#/</a>. <span class="dashicons dashicons-external" style="font-size: 14px;"></span> Select <strong>Zillow Lender Reviews</strong> when asked to select your API.
    </p>
    <?php
}
function ezrwp_section_for_defaults_cb( $args ) {
    ?>
    <hr />
    <p id="<?php echo esc_attr( $args['id'] ); ?>">
        You may override the Default Plugin Settings below using the widget and shortcode options.
    </p>
    <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
       Shortcode with Default Plugin Settings: <code>[ez-zillow-reviews]</code>
    </p>
    <p id="<?php echo esc_attr( $args['id'] ); ?>-3">
       Shortcode Example with Optional Overrides: <code>[ez-zillow-reviews layout="grid" columns="2" count="4"]</code>
    </p>
    <?php
}
function ezrwp_section_for_appearance_cb( $args ) {
    ?>
    <hr />
    <?php
}
function ezrwp_section_for_support_cb( $args ) {
    ?>
    <hr />
    <p>Your PHP version is <?php echo PHP_VERSION;?></p>
    <hr />
    <?php
}
// ZWSID callback
// Field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function ezrwp_zwsid_text_field_cb( $args ) {
    
    // Get the value of the setting we've registered with register_setting()
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = ''; // Zillow Web Services ID (ZWSID)
    if( isset( $options[$args['label_for']] ) ){
        $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Zillow Web Services ID</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

    <p>For Professional Reviews.</p>

    <?php
}
// ZMPID callback
function ezrwp_zmpid_text_field_cb( $args ) {
    
    // Get the value of the setting we've registered with register_setting()
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = ''; // Zillow Mortgages Partner ID (ZWPID)
    if( isset( $options[$args['label_for']] ) ){
        $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Zillow Mortgages Partner ID</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

    <p>For Lender Reviews.</p>

    <?php
}
// Zillow Screenname callback
function ezrwp_screenname_text_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Zillow Screenname</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

    <p>The screenname of the user whose reviews you want to display.</p>

    <?php
}
// Zillow NMLSID callback
function ezrwp_nmlsid_text_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">NMLS Number</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

    <p>The NMLS # of the lender whose reviews you want to display.</p>

    <?php
}
// Zillow Lender Company Name callback
function ezrwp_company_name_text_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Lender Company Name</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" />

    <p>The Company Name of the lender whose reviews you want to display, exactly as it appears on Zillow. Example "Great  Loans, LLC".</p>

    <?php
}
// Zillow Review Count callback
function ezrwp_count_number_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = 1;
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Review Count</label>
    <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="1" max="10" required />

    <p>The count of reviews you would like to return. 10 is maximum.</p>
    <?php
}

// Zillow Review Layout callback
function ezrwp_layout_select_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>
        
    <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
            class="ezrwp-setting ezrwp_layout"
            data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>"
            name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
        <option value="list" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'list', false ) ) : ( '' ); ?>>
        <?php esc_html_e( 'List', 'ezrwp' ); ?>
        </option>
        <option value="grid" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'grid', false ) ) : ( '' ); ?>>
        <?php esc_html_e( 'Grid', 'ezrwp' ); ?>
        </option>
    </select>

    <?php
}

// Zillow Reviews Grid Columns callback
function ezrwp_cols_number_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = 3;
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Grid Columns</label>
    <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="2" max="6" required />

    <?php
}

// Zillow Mandatory Disclaimer callback
function ezrwp_disclaimer_pill_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>
        
    <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
            class="ezrwp-setting"
            data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>"
            name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
        <option value="0" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '0', false ) ) : ( '' ); ?>>
        <?php esc_html_e( 'On', 'ezrwp' ); ?>
        </option>
        <option value="1" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '1', false ) ) : ( '' ); ?>>
        <?php esc_html_e( 'Off', 'ezrwp' ); ?>
        </option>
    </select>

    <div id="disclaimer-warning">
        <p><strong>Notice</strong>: Please add the disclaimer below somewhere on your website if you turn off the Disclaimer Setting on this page. According to Zillow's <a href="https://www.zillow.com/howto/api/BrandingRequirements.htm">Branding Requirements</a>, <em>All pages that contain Zillow Data or tools must include the following text, typically at the bottom of the page</em>:<p>

        <blockquote>
            © Zillow, Inc., 2006-2016. Use is subject to <a href="https://www.zillow.com/corp/Terms.htm">Terms of Use</a><br />
            <a href="https://www.zillow.com/wikipages/What-is-a-Zestimate/">What's a Zestimate?</a>
        </blockquote>
    </div>
 <?php
}

// Zillow Hide Date callback
function ezrwp_hide_date_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Date</label>
    <input name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The time elapsed since the review was written. Example: 6 days ago.
 <?php
}

// Zillow Hide Stars callback
function ezrwp_hide_stars_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Stars</label>
    <input name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The overall star rating for the specific review.
 <?php
}

// Zillow Hide Reviewer Summary callback
function ezrwp_hide_reviewer_summary_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Reviewer Summary</label>
    <input name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The summary around the reviewer. Example: "Sold a Single Family home in 2013 for approximately $500K in Roswell, GA."
 <?php
}

// Zillow Hide View All Link callback
function ezrwp_hide_view_all_link_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide View All Link</label>
    <input name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> The link to your Zillow profile with text "View All Reviews".
 <?php
}

// Zillow Hide View All Reviews Link callback
function ezrwp_hide_zillow_logo_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Hide Zillow Logo</label>
    <input name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked('1', $setting); ?> /> Important: Consult the <a href="https://www.zillow.com/howto/api/BrandingRequirements.htm" target="_blank">Zillow Branding Requirements</a> <span class="dashicons dashicons-external" style="font-size: 14px;"></span> before you hide the Zillow logo.
 <?php
}
// Zillow Review Quote Font Size callback
function ezrwp_quote_font_size_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Review Text Font Size</label>
    <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="1" />

    <p>The font size for the review quote text in pixels. Default is 18px.</p>

    <?php
}

// Zillow Reviewer Description Font Size callback
function ezrwp_reviewer_description_font_size_field_cb( $args ) {
    
    $options = $GLOBALS['ezrwp_options'];
    
    $setting = '';
    if( isset( $options[$args['label_for']] ) ){
         $setting = $options[$args['label_for']];
    };
    ?>

    <label for="<?php echo esc_attr( $args['label_for'] ); ?>" class="screen-reader-text">Review Text Font Size</label>
    <input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" class="ezrwp-setting ezrwp_cols" data-custom="<?php echo esc_attr( $args['ezrwp_custom_data'] ); ?>" name="ezrwp_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo $setting ?>" min="1" />

    <p>The font size for the reviewer description text in pixels. Default is 16px.</p>

    <?php
}


// Add a new Sub-menu to WordPress Administration
function ezrwp_options_page_html() {
    
    // Check user capabilities
     if ( ! current_user_can( 'manage_options' ) ) {
        return;
     }
 ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                 
        <?php
            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'professional_reviews';
        ?>
         
        <h2 class="nav-tab-wrapper">
            <a href="?page=easy-zillow-reviews&tab=professional_reviews" id="ezrwp-nav-tab-0" class="nav-tab <?php echo $active_tab == 'professional_reviews' ? 'nav-tab-active' : ''; ?>">Professional Reviews</a>
            <a href="?page=easy-zillow-reviews&tab=lender_reviews" id="ezrwp-nav-tab-1" class="nav-tab <?php echo $active_tab == 'lender_reviews' ? 'nav-tab-active' : ''; ?>">Lender Reviews</a>
            <a href="?page=easy-zillow-reviews&tab=settings" id="ezrwp-nav-tab-2" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
        </h2>
        <form action="../../wp-admin/options.php" method="post">
            <?php
    
            if( $active_tab == 'professional_reviews' ) {
                settings_fields( 'ezrwp_professional_reviews' );
                do_settings_sections( 'ezrwp_professional_reviews' );
            } else if( $active_tab == 'lender_reviews' ) {
                settings_fields( 'ezrwp_lender_reviews' );
                do_settings_sections( 'ezrwp_lender_reviews' );
            } else {
                settings_fields( 'ezrwp_general' );
                do_settings_sections( 'ezrwp_general' );
            }
    
            // Output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
 <?php
}

function ezrwp_options_page(){
    add_submenu_page(
        'options-general.php', //$parent_slug
        'Easy Zillow Reviews', //$page_title
        'Easy Zillow Reviews', //$menu_title
        'manage_options', //$capability
        'easy-zillow-reviews', //$menu_slug
        'ezrwp_options_page_html' //$function
    );
}
add_action('admin_menu', 'ezrwp_options_page');