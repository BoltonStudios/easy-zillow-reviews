<?php
// Load admin scripts
function load_ezrwp_premium_admin_style($hook) {
        // Load only on ?page=ezrwp
        if($hook != 'settings_page_easy-zillow-reviews') {
                return;
        }
        wp_enqueue_style( 'ezrwp_premium_admin_css',  plugin_dir_url( __FILE__ ) . 'admin-styles.css' );
}

// Add admin options
function ezrwp_premium_upgrade_callout_init(){
    // Zillow Lender Profile
    add_settings_section(
        'eezrwp_section_for_premium_callout', // id
        __( 'Upgrade to Easy Zillow Reviews Premium', 'ezrwp_lender_reviews' ), // title
        'ezrwp_section_for_premium_callout_cb', // callback
        'ezrwp_lender_reviews' // page
    );
}
function ezrwp_premium_settings_init(){
    // Zillow Lender Profile
    add_settings_section(
        'ezrwp_section_for_zillow_lender_parameters', // id
        __( 'Zillow Lender Profile', 'ezrwp_lender_reviews' ), // title
        'ezrwp_section_for_zillow_lender_parameters_cb', // callback
        'ezrwp_lender_reviews' // page
    );
    // Zillow Lender Fields
    add_settings_field(
        'ezrwp_zmpid', // id. Used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Zillow Mortgages Partner ID', 'ezrwp_lender_reviews' ), // title
        'ezrwp_zmpid_text_field_cb', // callback
        'ezrwp_lender_reviews', // page
        'ezrwp_section_for_zillow_lender_parameters', [
            'label_for' => 'ezrwp_zmpid', // used for the "for" attribute of the <label>.
            'class' => 'ezrwp_row', // used for the "class" attribute of the <tr> containing the field.
            'ezrwp_custom_data' => 'custom', // custom key value pairs to be used inside your callbacks.
        ] // args
    );
    add_settings_field(
        'ezrwp_nmlsid',
        __( 'NMLS#', 'ezrwp_lender_reviews' ),
        'ezrwp_nmlsid_text_field_cb',
        'ezrwp_lender_reviews',
        'ezrwp_section_for_zillow_lender_parameters', [
            'label_for' => 'ezrwp_nmlsid',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
    add_settings_field(
        'ezrwp_company_name',
        __( 'Company Name', 'ezrwp_lender_reviews' ),
        'ezrwp_company_name_text_field_cb',
        'ezrwp_lender_reviews',
        'ezrwp_section_for_zillow_lender_parameters', [
            'label_for' => 'ezrwp_company_name',
            'class' => 'ezrwp_row',
            'ezrwp_custom_data' => 'custom',
        ]
    );
}

function ezrwp_section_for_premium_callout_cb( $args ) {
    ?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>-0">The Premium License unlocks the following features:</p>
    <ul id="<?php echo esc_attr( $args['id'] ); ?>-1">
        <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Team Reviews</li>
        <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Lender Reviews</li>
        <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Individual Loan Officer Reviews</li>
        <li><span class="dashicons dashicons-yes" style="font-size: 14px;"></span> Company Profile Reviews</li>
    </ul>
    <p id="<?php echo esc_attr( $args['id'] ); ?>-2">
        <a href="<?php echo ezrwp_fs()->get_upgrade_url(); ?>" class="button">Upgrade to Premium</a>
    </p>
    <?php
}
?>