<?php
/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @author	 Sascha Endlicher, M.A. <support@panomity.com>
 */
class Panomity_Darkweb_Press_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since	8.1
	 *
	 * @var array the actions registered with WordPress to fire when the plugin loads
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since	8.1
	 *
	 * @var array the filters registered with WordPress to fire when the plugin loads
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since	8.1
	 */
	public function __construct() {
		$this->actions=[];
		$this->filters=[];
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since	8.1
	 *
	 * @param string $hook		  the name of the WordPress action that is being registered
	 * @param object $component	 a reference to the instance of the object on which the action is defined
	 * @param string $callback	  the name of the function definition on the $component
	 * @param int	$priority	  Optional. he priority at which the function should be fired. Default is 10.
	 * @param int	$accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority=10, $accepted_args=1 ) {
		$this->actions=$this->add(
			$this->actions,
			$hook,
			$component,
			$callback,
			$priority,
			$accepted_args
		);
	}

	public function panomity_darkweb_press_enqueue_styles() {
		wp_enqueue_style(
			'font-awesome',
			plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
			[],
			'5.15.4',
			'all'
		);
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since	8.1
	 *
	 * @param string $hook		  the name of the WordPress filter that is being registered
	 * @param object $component	 a reference to the instance of the object on which the filter is defined
	 * @param string $callback	  the name of the function definition on the $component
	 * @param int	$priority	  Optional. he priority at which the function should be fired. Default is 10.
	 * @param int	$accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority=10, $accepted_args=1 ) {
		$this->filters=$this->add(
			$this->filters,
			$hook,
			$component,
			$callback,
			$priority,
			$accepted_args
		);
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since	8.1
	 *
	 * @param array  $hooks		 the collection of hooks that is being registered (that is, actions or filters)
	 * @param string $hook		  the name of the WordPress filter that is being registered
	 * @param object $component	 a reference to the instance of the object on which the filter is defined
	 * @param string $callback	  the name of the function definition on the $component
	 * @param int	$priority	  the priority at which the function should be fired
	 * @param int	$accepted_args the number of arguments that should be passed to the $callback
	 *
	 * @return array the collection of actions and filters registered with WordPress
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[]=[
			'hook'							   => $hook,
			'component'		   => $component,
			'callback'			   => $callback,
			'priority'			   => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since	8.1
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				[ $hook['component'], $hook['callback'] ],
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				[ $hook['component'], $hook['callback'] ],
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		// Register shortcode
		add_shortcode(
			'panomity_darkweb_press',
			['Panomity_Darkweb_Press', 'panomity_darkweb_press_shortcode']
		);

		$widget=new Panomity_Darkweb_Press_Dashboard_Widgets();

		add_action( 'rest_api_init', function () {
			$routes=[
				[
					'path'			   => '/check-password',
					'callback'  => function ( $request ) {
						$check_value=$request->get_param( 'check_password' );

						return Panomity_Darkweb_Press::panomity_check( 'password', $check_value );
					},
				],
				[
					'path'			   => '/check-domain',
					'callback'  => function ( $request ) {
						$check_value=$request->get_param( 'check_domain' );

						return Panomity_Darkweb_Press::panomity_check( 'domain', $check_value );
					},
				],
				[
					'path'			   => '/check-email',
					'callback'  => function ( $request ) {
						$check_value=$request->get_param( 'check_email' );

						return Panomity_Darkweb_Press::panomity_check( 'email', $check_value );
					},
				],
			];

			foreach ( $routes as $route ) {
				register_rest_route(
					'panomity-darkweb-press/v1',
					"/{$route['path']}",
					[
						'methods'									   => 'POST',
						'callback'								   => $route['callback'],
						'permission_callback'=> function () {
							return true; // allow anonymous access
						},
					]
				);
			}
		} );
	}
}
