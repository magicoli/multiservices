<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Prestations
 * @subpackage Prestations/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Prestations
 * @subpackage Prestations/includes
 * @author     Your Name <email@example.com>
 */
class Prestations_Modules {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	public $locale;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->version = PRESTATIONS_VERSION;
		$this->plugin_slug = 'prestations';

		$this->locale = $this->get_locale();

		$this->load_dependencies();
		// $this->define_admin_hooks();
		// $this->define_public_hooks();

		// register_activation_hook( PRESTATIONS_FILE, __CLASS__ . '::activate' );
		// register_deactivation_hook( PRESTATIONS_FILE, __CLASS__ . '::deactivate' );
	}

	private function load_dependencies() {
		if(isset($_REQUEST['submit']) && isset($_REQUEST['page']) && $_REQUEST['page'] == 'prestations')
		$enabled = (isset($_REQUEST['modules_enable'])) ? $_REQUEST['modules_enable'] : [];
		else $enabled = Prestations::get_option('modules_enable', []);

		$this->modules = [];

		if(is_plugin_active('woocommerce/woocommerce.php')) {
			require_once PRESTATIONS_DIR . 'includes/modules/class-woocommerce.php';
			require_once PRESTATIONS_DIR . 'includes/modules/class-woocommerce-payment-product.php';
		}

		if(in_array('imap', $enabled)) {
			require_once PRESTATIONS_DIR . 'includes/class-mailbox.php';
		}

		if(in_array('lodgify', $enabled)) {
			require_once PRESTATIONS_DIR . 'includes/modules/class-lodgify.php';
		}

		if(in_array('hbook', $enabled)) {
			require_once PRESTATIONS_DIR . 'includes/modules/class-hbook.php';
		}

	}

	public function get_locale() {
		if(!empty($this->locale)) return $this->locale;

		$locale = preg_replace('/_.*/', '', get_locale());
		if(empty($locale)) $locale = 'en';

		return $locale;
	}
	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		if(!empty($this->modules) && is_array($this->modules)) {
			foreach($this->modules as $key => $loader) {
				$this->modules[$key]->run();
			}
		}

		$this->actions = array(
		);

		$this->filters = array(
			// array (
			// 	'hook' => 'mb_settings_pages',
			// 	'callback' => 'register_settings_pages',
			// ),
			array(
				'hook' => 'rwmb_meta_boxes',
				'callback' => 'register_fields'
			),
			array(
				'hook' => 'prestations_managed_list',
				'callback' => 'managed_list_filter',
			)
		);

		$defaults = array( 'component' => __CLASS__, 'priority' => 10, 'accepted_args' => 1 );

		foreach ( $this->filters as $hook ) {
			$hook = array_merge($defaults, $hook);
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			$hook = array_merge($defaults, $hook);
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

	static function register_fields( $meta_boxes ) {
		$prefix = 'modules_';

		// Modules settings in General tab
		$meta_boxes[] = [
			'title'          => __( 'Prestations Modules', 'prestations' ),
			'id'             => 'prestations-modules',
			'settings_pages' => ['prestations'],
			'tab'            => 'general',
			'fields'         => [
				[
					'name'    => __( 'Modules', 'prestations' ),
					'id'      => $prefix . 'enable',
					'type'    => 'checkbox_list',
					'options' => [
						'imap'    => __( 'Mail Processing', 'prestations' ),
						'lodgify' => __( 'Lodgify', 'prestations' ),
						'hbook' => __( 'HBook Plugin', 'prestations' ),
					],
				],
			],
		];

    return $meta_boxes;
	}

	static function managed_list_filter($html = '') {
		$title = __('External', 'prestations');
		if(empty($list)) $list = __('Empty list', 'prestations');

		global $post;
		$data = get_post_meta($post->ID, 'modules-data', true);

		if(empty($data)) $data = [];
		// if(is_array($data)) {
			$data['columns'] = array(
				'id' => __('ID', 'prestations'),
				'created' => __('Created', 'prestations'),
				'source' => __('Source', 'prestations'),
				'description' => __('Description', 'prestations'),
				'from' => __('From', 'prestations'),
				'to' => __('To', 'prestations'),
				'subtotal' => __('Subtotal', 'prestations'),
				'discount' => __('Discount', 'prestations'),
				'refunded' => __('Refunded', 'prestations'),
				'total' => __('Total', 'prestations'),
				'paid' => __('Paid', 'prestations'),
				'status' => __('Status', 'prestations'),
				'actions' => '',
			);
			$data['format'] = array(
				'created' => 'date_time',
				'from' => 'date',
				'to' => 'date',
				'subtotal' => 'price',
				'discount' => 'price',
				'refunded' => 'price',
				'total' => 'price',
				'paid' => 'price',
				'status' => 'status',
			);

			$list = new Prestations_Table($data);

			$html .= sprintf(
				'<div class="managed-list managed-list-external">
				<h3>%s</h3>
				%s
				</div>',
				$title,
				$list->render(),
			);
		// }

		return $html;
	}

}
