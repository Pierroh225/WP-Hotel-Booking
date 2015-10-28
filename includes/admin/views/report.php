<?php
	/* allow hook */
	$report_tabs = apply_filters( 'tp_hotel_booking_report_tab', array(
		array(
				'id'		=> 'price',
				'title'		=> __( 'Price', 'tp-hotel-booking' ),
			),
		array(
				'id'		=> 'date',
				'title'		=> __( 'Room availability', 'tp-hotel-booking' ),
			)
	));

	$currenttab = 'price';
	if( isset($_REQUEST['tab']) && $_REQUEST['tab'] )
		$currenttab = $_REQUEST['tab'];
?>

<ul class="tp_hotel_booking subsubsub">
	<?php $html = array(); ?>
	<?php foreach( $report_tabs as $key => $tab ): ?>

		<?php $html[] =
			'<li>
				<a '.( $tab['id'] === $currenttab ? 'class="current" ' : '' ).'href="'.admin_url( 'admin.php?page=tp_hotel_booking_report&tab='.$tab['id'] ).'" >'.sprintf( '%s', $tab['title'] ).'</a>
			</li>';
 		?>
	<?php endforeach; ?>
	<?php echo implode( ' | ', $html); ?>
</ul>

<p style="clear:both"></p>

<?php $rooms = hb_get_rooms(); ?>
<?php
	$date = apply_filters( 'tp_hotel_booking_report_date', array(
		array(
				'id'	=> 'year',
				'title'	=> __('Year', 'tp-hotel-booking')
			),
		array(
				'id'	=> 'last_month',
				'title'	=> __('Last Month', 'tp-hotel-booking')
			),
		array(
				'id'	=> 'current_month',
				'title'	=> __('This Month', 'tp-hotel-booking')
			),
		array(
				'id'	=> '7day',
				'title'	=> __('Last 7 Days', 'tp-hotel-booking')
			)
	));

?>
<?php
	$currentRang = '7day';
	if( isset($_REQUEST['range']) && $_REQUEST['range'] )
		$currentRang = $_REQUEST['range'];
?>
<div id="tp-hotel-booking-report" class="postbox">

	<div id="poststuff">
		<h3>
			<a href="<?php echo admin_url( 'admin.php?page=tp_hotel_booking_report&action=export' ) ?>" class="export_csv"><?php _e( 'Export CSV', 'tp-hotel-booking' ) ?></a>
			<ul>
				<?php foreach( $date as $key => $d ): ?>
					<li <?php echo $d['id'] === $currentRang ? 'class="active"' : '' ?>>
						<a href="<?php echo admin_url( 'admin.php?page=tp_hotel_booking_report&tab='.$currenttab.'&range='.$d['id'] ) ?>">
							<?php printf( '%s', $d['title'] ) ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</h3>

	</div>

	<!-- booking_page_tp_hotel_booking_report -->
	<div id="tp-hotel-booking-report-main">
		<div id="chart-sidebar">
			<?php do_action( 'tp_hotel_booking_chart_sidebar', $currenttab, $currentRang ) ?>
		</div>

		<div id="tp-hotel-booking-chart-content">
			<?php do_action( 'tp_hotel_booking_chart_canvas', $currenttab, $currentRang ) ?>
		</div>
	</div>

</div>