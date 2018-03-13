<?php

/**
 * Admin View: Modal search booking item.
 *
 * @version     2.0
 * @package     WP_Hotel_Booking/Views
 * @category    View
 * @author      Thimpress, leehld
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

hb_admin_view( 'booking/modal/add' );
hb_admin_view( 'booking/modal/update' );
?>

<script type="text/x-template" id="tmpl-admin-booking-modal">

    <div id="booking-modal-search">


        <wphb-booking-modal-update :item="existItem" v-if="this.type === 'update'"
                                   @closeModal="closeModal"></wphb-booking-modal-update>

        <wphb-booking-modal-add v-else="" :item="newItem" @checkAvailable="checkAvailable" @addItem="addItem"
                                @closeModal="closeModal"></wphb-booking-modal-add>

    </div>

</script>

<script type="text/javascript">

    (function (Vue, $store) {

        Vue.component('wphb-booking-modal', {
            template: '#tmpl-admin-booking-modal',
            props: ['type', 'newItem', 'existItem'],
            computed: {},
            methods: {
                checkAvailable: function (item) {
                    this.$emit('checkAvailable', item);
                },
                addItem: function (item) {
                    this.$emit('addItem', item);
                },
                closeModal: function () {
                    this.$emit('closeModal');
                }
            }
        });

    })(Vue, WPHB_Booking_Store);

</script>