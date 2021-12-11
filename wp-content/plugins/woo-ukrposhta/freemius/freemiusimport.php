<?php


if ( ! function_exists( 'wu_fsk' ) ) {
    // Create a helper function for easy SDK access.
    function wu_fsk() {
        global $wu_fsk;

        if ( ! isset( $wu_fsk ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/start.php';

            $wu_fsk = fs_dynamic_init( array(
                'id'                  => '3509',
                'slug'                => 'woo-ukrposhta',
                'premium_slug'        => 'nova-poshta-ttn-premium',
                'type'                => 'plugin',
                'public_key'          => 'pk_ca8dbc8f7d6e567355cf59530da68',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'first-path'     => 'admin.php?page=morkvaup_plugin',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $wu_fsk;
    }

    // Init Freemius.
    wu_fsk();
    // Signal that SDK was initiated.
    do_action( 'wu_fsk_loaded' );
}
