<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link       https://github.com/magicoli/multipass
 * @since      1.0.0
 *
 * @package    multipass
 * @subpackage multipass/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    multipass
 * @subpackage multipass/includes
 * @author     Magiiic <info@magiiic.com>
 */
class Mltp_Settings {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	protected $caps;
	protected $roles;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->roles = array(
			'mltp_reader'        => __( 'MultiPass Reader', 'multipass' ),
			'mltp_manager'       => __( 'MultiPass Manager', 'multipass' ),
			'mltp_administrator' => __( 'MultiPass Administrator', 'multipass' ),
		);

		$this->caps['mltp_reader'] = array(
			'view_mltp_dashboard',
			'view_mltp_calendar',
			// 'delete_mltp_prestations',
			// 'edit_mltp_prestations',
			// 'edit_others_mltp_prestations',
			// 'publish_mltp_prestations',
			// 'read_private_mltp_prestations',
		);

		$this->caps['mltp_manager']       = array_merge(
			$this->caps['mltp_reader'],
			array(
				'create_prestations',
				'delete_others_prestations',
				'delete_prestations',
				'delete_private_prestations',
				'delete_published_prestations',
				'edit_others_prestations',
				'edit_prestations',
				'edit_private_prestations',
				'edit_published_prestations',
				'publish_prestations',
				'read_private_prestations ',
			)
		);
		$this->caps['mltp_administrator'] = array_merge(
			$this->caps['mltp_manager'],
			array(
				'create_resources',
				'delete_others_resources',
				'delete_resources',
				'delete_private_resources',
				'delete_published_resources',
				'edit_others_resources',
				'edit_resources',
				'edit_private_resources',
				'edit_published_resources',
				'publish_resources',
				'read_private_resources ',
			)
		);

		$this->sanitize_roles( 'administrator', array( 'id' => 'mltp_administrator' ) );

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress action that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the action is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array  $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         The priority at which the function should be fired.
	 * @param    int    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$actions = array();

		$filters = array(
			array(
				'hook'     => 'init',
				'callback' => 'add_roles',
				'priority' => 10,
			),
			array(
				'hook'     => 'init',
				'callback' => 'add_capabilities',
				'priority' => 11,
			),
			array(
				'hook'     => 'admin_menu',
				'callback' => 'admin_menu_action',
			),
			array(
				'hook'     => 'mb_settings_pages',
				'callback' => 'register_settings_pages',
				'priority' => 5,
			),
			array(
				'hook'     => 'plugin_action_links_' . basename( MULTIPASS_DIR ) . '/' . basename( MULTIPASS_FILE ),
				'callback' => 'plugin_action_links',
			),
			array(
				'hook'     => 'rwmb_meta_boxes',
				'callback' => 'register_settings_fields',
			),
		);

		foreach ( $filters as $hook ) {
			$hook = array_merge(
				array(
					'component'     => $this,
					'priority'      => 10,
					'accepted_args' => 1,
				),
				$hook
			);
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $actions as $hook ) {
			$hook = array_merge(
				array(
					'component'     => $this,
					'priority'      => 10,
					'accepted_args' => 1,
				),
				$hook
			);
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

	function admin_menu_action() {
		// $cap = (current_user_can('manage_options')) ? 'manage_options' : 'view_mltp_dashboard';
		add_menu_page(
			'MultiPass', // string $page_title,
			'MultiPass', // string $menu_title,
			'view_mltp_dashboard', // string $capability,
			'multipass', // string $menu_slug,
			null, // [ $this, 'dashboard_callback' ], // callable $callback = '',
			'dashicons-excerpt-view', // string $icon_url = '',
			MultiPass::get_option( 'menu_position', 20 ), // int|float $position = null,
		);
		// add_submenu_page(
		// 'multipass', // string $parent_slug,
		// __( 'Dashboard', 'multipass' ), // string $page_title,
		// __( 'Dashboard', 'multipass' ), // string $menu_title,
		// 'read', // string $capability,
		// 'multipass', // string $menu_slug,
		// [ $this, 'render_dashboard' ], // callable $callback = '',
		// 1, // int|float $position = null
		// );
	}
	function render_dashboard() {
		$actions = '';
		$content = '';
		printf(
			'<div class="wrap">
				<div id="mltp-placeholder">
					<h1 class="wp-heading-inline">%s %s</h1>

				</div>
				<div id="calendar"></div>
      </div>',
			get_admin_page_title(),
			$actions,
			$content,
		);
		return;
	}
	function register_settings_pages( $settings_pages ) {
		// $settings_pages[] = [
		// 'id'          => 'multipass',
		// 'option_name' => 'MultiPass Option',
		// 'menu_title'    => __('MultiPass Menu', 'multipass' ),
		// 'position'    => 15,
		// 'submenu_title' => 'Calendar',
		// 'style'       => 'no-boxes',
		// 'columns'     => 1,
		// 'icon_url'    => 'dashicons-excerpt-view',
		// 'capability'    => 'manage_options',
		// ];
		// $settings_pages[] = [
		// ];

		$settings_pages['multipass-settings'] = array(
			'menu_title'  => __( 'Settings', 'multipass' ),
			'page_title'  => __( 'MultiPass Settings', 'multipass' ),
			'id'          => 'multipass-settings',
			'option_name' => 'multipass',
			// 'position'      => 23,
			'parent'      => 'multipass',
			'capability'  => 'manage_options',
			'style'       => 'no-boxes',
			'columns'     => 1,
			'tabs'        => array(
				'general' => __( 'General', 'multipass' ),
				'roles'   => __( 'Roles', 'multipass' ),
				// 'tos'     => __( 'Terms of Service', 'multipass' ),
			),
			'icon_url'    => 'dashicons-book',
		);
		return $settings_pages;
	}

	function register_settings_fields( $meta_boxes ) {
		if ( ! is_admin() ) {
			return $meta_boxes;
		}
		$prefix = '';

		$meta_boxes['multipass-settings'] = array(
			'title'          => __( 'General', 'multipass' ),
			'id'             => 'multipass-settings-fields',
			'settings_pages' => array( 'multipass-settings' ),
			'tab'            => 'general',
			'fields'         => array(
				'currency_options' => array(
					'name'   => __( 'Currency Options', 'multipass' ),
					'id'     => $prefix . 'currency',
					'type'   => 'group',
					'class'  => 'inline',
					'fields' => array(
						array(
							'name'    => __( 'Code', 'multipass' ),
							'id'      => $prefix . 'code',
							'type'    => 'select_advanced',
							'size'    => 3,
							'options' => MultiPass::currency_options(),
						),
						array(
							'name'    => __( 'Position', 'multipass' ),
							'id'      => $prefix . 'pos',
							'type'    => 'select',
							'size'    => 5,
							'options' => array(
								'left'        => __( 'Left', 'multipass' ),
								'right'       => __( 'Right', 'multipass' ),
								'left_space'  => __( 'Left with space', 'multipass' ),
								'right_spâce' => __( 'Right with space', 'multipass' ),
							),
							'std'     => 'right_space',
						),
						array(
							'name' => __( 'Decimals', 'multipass' ),
							'id'   => $prefix . 'num_decimals',
							'type' => 'number',
							'min'  => 0,
							'step' => 1,
							'std'  => 2,
							'size' => 3,
						),
					),
				),
			),
		);

		$meta_boxes['roles-settings'] = array(
			'title'          => __( 'Roles Settings', 'multipass' ),
			'id'             => 'roles-settings',
			'settings_pages' => array( 'multipass-settings' ),
			'tab'            => 'roles',
			'fields'         => array(
				array(
					'name'              => __( 'Reader', 'multipass' ),
					'id'                => $prefix . 'mltp_reader',
					'type'              => 'select',
					'options'           => $this->get_roles( 'mltp_reader' ),
					'desc'              => $this->get_role_users( 'mltp_reader' ),
					'default'           => 'contributor',
					'placeholder'       => __( 'Select a role', 'multipass' ),
					'sanitize_callback' => array( $this, 'sanitize_roles' ),
				),
				array(
					'name'              => __( 'Manager', 'multipass' ),
					'id'                => $prefix . 'mltp_manager',
					'type'              => 'select',
					'options'           => $this->get_roles( 'mltp_manager' ),
					'desc'              => $this->get_role_users( 'mltp_manager' ),
					'default'           => 'editor',
					'placeholder'       => __( 'Select a role', 'multipass' ),
					'sanitize_callback' => array( $this, 'sanitize_roles' ),
				),
				array(
					'name'              => __( 'Administrator', 'multipass' ),
					'id'                => $prefix . 'mltp_administrator',
					'type'              => 'select',
					'options'           => $this->get_roles( 'mltp_administrator' ),
					'desc'              => $this->get_role_users( 'mltp_administrator' ),
					'default'           => 'administrator',
					'placeholder'       => __( 'Select a role', 'multipass' ),
					'sanitize_callback' => array( $this, 'sanitize_roles' ),
				),
			),
		);

		return $meta_boxes;
	}

	function plugin_action_links( $links ) {
		$url   = esc_url( add_query_arg( 'page', 'multipass', get_admin_url() . 'admin.php' ) );
		$links = array( 'settings' => "<a href='$url'>" . __( 'Settings', 'multipass' ) . '</a>' ) + $links;

		return $links;
	}

	function roles_settings_being_updated() {
		$request = wp_unslash( $_REQUEST );
		if ( empty( $request['nonce_roles-settings'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $request['nonce_roles-settings'], 'rwmb-save-roles-settings' ) ) {
			return false;
		}

		return true;
	}

	function get_roles( $default = false ) {
		global $wp_roles;

		$roles = array();
		if ( $default ) {
			$default_role = get_role( $default );
			if ( ! $default_role ) {
				$roles['_create'] = sprintf(
					__( '&rarr; Create "%s" role', 'multipass' ),
					__( $this->roles[ $default ], 'multipass' ),
				);
			}
		}
		// error_log(print_r($this->roles, true));
		foreach ( $this->roles as $role_slug => $role_name ) {
			$role = get_role( $role_slug );
			if ( $role ) {
				$roles[ $role_slug ] = __( $role_name, 'multipass' );
			}
		}

		foreach ( $wp_roles->roles as $slug => $role ) {
			$roles[ $slug ] = __( $role['name'], 'multipass' );
		}
		if ( isset( $roles[ $default ] ) ) {
			$roles[ $default ] = sprintf(
				__( '%s (default)', 'multipass' ),
				$roles[ $default ],
				// __($roles[$default], 'multipass'),
			);
		}
		return $roles;
	}

	function get_role_users( $group ) {
		if ( $this->roles_settings_being_updated() ) {
			$request = wp_unslash( $_REQUEST );
			$role_slug = ('_create' === $request[ $group ]) ? $group : $request[ $group ];
		} else {
			$role_slug = MultiPass::get_option($group);
		}
		if(empty($role_slug)) return;

		$args  = array(
			'role__in'    => ('mltp_administrator' === $role_slug) ? [ $role_slug, 'administrator' ] : [ $role_slug ],
			'orderby' => 'user_nicename',
			'order'   => 'ASC',
		);
		$users = get_users( $args );
		$count = count($users);
		$more = '';
		if($count > 5) {
			$users = array_slice($users, 0, 5);
			$more = sprintf(
				__(', and %s more...', 'multipass'),
				$count - 5,
			);
		}

		$list = [];
		foreach ( $users as $user ) {
			$list[] = sprintf(
				'<a href="%s">%s</a>',
				get_edit_user_link( $user->ID ),
				$user->display_name,
			);
		}

		return sprintf(
			_n('Currently %s allowed user: %s%s', 'Currently %s allowed users: %s%s', $count, 'multipass'),
			$count,
			join( ', ', $list ),
			$more,
		);
	}

	/**
	 * We have to process roles and capabilities before settings page is rendered.
	 */
	function add_roles() {

		if ( $this->roles_settings_being_updated() ) {
			$request = wp_unslash( $_REQUEST );
			foreach ( $this->roles as $role_slug => $role_name ) {
				$new_role = ( isset( $request[ $role_slug ] ) ) ? $request[ $role_slug ] : MultiPass::get_option( $role_slug );
				if ( '_create' === $new_role ) {
					add_role( $role_slug, $role_name, array( 'view_admin_dashboard' => true ) );
				}
			}
		}

		return;
	}

	function add_capabilities() {
		if ( $this->roles_settings_being_updated() ) {
			$request = wp_unslash( $_REQUEST );
			$options = array( 'mltp_reader', 'mltp_manager', 'mltp_administrator' );
			$remove  = array();
			$add     = array();
			foreach ( $options as $option ) {
				$new_role      = ( isset( $request[ $option ] ) ) ? $request[ $option ] : MultiPass::get_option( $option );
				$new_role      = ( '_create' === $new_role ) ? $option : $new_role;
				$previous_role = MultiPass::get_option( $option );
				if ( $previous_role !== $new_role & ! empty( $previous_role ) && $previous_role != 'administrator' ) {
					$remove[ $previous_role ] = array_merge(
						( isset( $remove[ $previous_role ] ) ) ? $remove[ $previous_role ] : array(),
						$this->caps[ $option ],
					);
				}
				if ( ! empty( $new_role ) ) {
					$add[ $new_role ] = array_merge(
						( isset( $add[ $new_role ] ) ) ? $add[ $new_role ] : array(),
						$this->caps[ $option ],
					);
				}
			}

			foreach ( $remove as $role_slug => $caps ) {
				$role = get_role( $role_slug );
				if ( $role ) {
					foreach ( $caps as $cap ) {
						$role->remove_cap( $cap, true );
					}
				}
			}

			foreach ( $add as $role_slug => $caps ) {
				$role = get_role( $role_slug );
				if ( $role ) {
					foreach ( $caps as $cap ) {
						$role->add_cap( $cap, true );
					}
				}
			}
		}
	}

	function sanitize_roles( $new_role, $field, $old_value = null ) {
		$group = $field['id'];
		if ( isset( $request['nonce_roles-settings'] ) && 'mltp_reader' === $group ) {
			error_log( __METHOD__ . " $new_role = " . MultiPass::get_option( 'mltp_reader' ) );
		}

		if ( ! empty( $new_role ) ) {
			if ( '_create' === $new_role ) {
				$new_role = $group;
			}

			$role = get_role( $new_role );
			if ( ! $role ) {
				error_log( __CLASS__ . '::' . __METHOD__ . " could not find role $new_role" );
				return;
			}
		}

		return $new_role;
	}
}

$this->loaders[] = new Mltp_Settings();
