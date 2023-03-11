<?php

class Panomity_Darkweb_Press_Dashboard_Widgets {

	public $custom_dashboard_widgets;

	//public $remove_default_widgets;

	public function __construct() {
		$this->custom_dashboard_widgets='';
		add_action(
			'wp_dashboard_setup',
			[ $this,
			'Panomity_Darkweb_Press_Dashboard_Widgets::add_dashboard_widgets',
			]
		);
	}

public static function dashboard_widget_content() {
	$service_types=['password', 'domain', 'email'];

	echo '<h3>' . esc_html__(
		'Dark Web Search Statistics',
		'panomity-darkweb-press'
	) . '</h3>';

	// Get search statistics for the last 6 months
	$stats=get_transient( 'panomity_darkweb_press_stats' );

	if ( false === $stats ) {
		$stats=[];

		for ( $i=5; $i >= 0; $i-- ) {
			$date=date( 'Y-m', strtotime( "-$i months" ) );

			foreach ( $service_types as $type ) {
				$option_name="panomity_darkweb_press_statistics_{$type}_{$date}";
				$count=intval( get_option( $option_name, 0 ) );
				$stats[ $date ][ $type ]=$count;
			}
		}

		set_transient( 'panomity_darkweb_press_stats', serialize( $stats ), DAY_IN_SECONDS );
	} else {
		$stats=unserialize( $stats );
	}

	// Display statistics in a table
	echo '<table class="widefat">';
	echo '<thead>';
	echo '<tr><th>' . esc_html__( 'Date', 'panomity-darkweb-press' )
		. '</th><th>' . esc_html__( 'Email', 'panomity-darkweb-press' )
		. '</th><th>' . esc_html__( 'Password', 'panomity-darkweb-press' )
		. '</th><th>' . esc_html__( 'Domain', 'panomity-darkweb-press' )
		. '</th></tr>';
	echo '</thead>';
	echo '<tbody>';

	foreach ( $stats as $date => $counts ) {
		echo '<tr>';
		echo '<td>' . esc_html( date( 'M Y', strtotime( $date ) ) ) . '</td>';

		foreach ( $service_types as $type ) {
			$count=isset( $counts[ $type ] ) ? intval( $counts[ $type ] ) : 0;
			echo '<td>' . esc_html( number_format_i18n( $count ) ) . '</td>';
		}
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
}

	public static function add_dashboard_widgets() {
		$custom_dashboard_widgets=[
		'panomity-darkweb-press-dashboard-widget-statistics' => [
			'title'	   => 'Panomity Darkweb Press Statistics',
			'callback' => 'Panomity_Darkweb_Press_Dashboard_Widgets::dashboard_widget_content',
			],
		];

		foreach ( $custom_dashboard_widgets as $widget_id => $options ) {
			wp_add_dashboard_widget(
				$widget_id,
				$options['title'],
				$options['callback']
			);
		}
	}
}
