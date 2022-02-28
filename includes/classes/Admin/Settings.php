<?php

class BoilerplateSettings
{

	public $option_prefix = 'gl_saas_optics_';
	public $settings = [];

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$this->settings = $this->get_settings();

		add_action('admin_init', [$this, 'register_settings']);
		add_action('admin_menu', [$this, 'add_settings_menus']);

		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	public function enqueue_scripts()
	{
		$boilerplate_assets = plugin_dir_url(BOILERPLATE_FILE) . 'assets';
		$cdnjs_assets = 'https://cdnjs.cloudflare.com/ajax/libs';

		wp_enqueue_media();

		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style('boilerplate', $boilerplate_assets . '/css/admin.css', array(), BOILERPLATE_VERSION);
		wp_enqueue_style('select2', $cdnjs_assets . '/select2/4.0.13/css/select2.min.css', false, BOILERPLATE_VERSION);

		wp_register_script('boilerplate', $boilerplate_assets . '/js/admin.js', array('jquery'), BOILERPLATE_VERSION, true);
		wp_localize_script(
			'boilerplate',
			'meta_image',
			array(
				'title' => __('Choose or Upload Media', 'boilerplate'),
				'button' => __('Use this image', 'boilerplate'),
			)
		);

		wp_enqueue_script('select2', $cdnjs_assets . '/select2/4.0.13/js/select2.min.js', array('jquery'), BOILERPLATE_VERSION, true);
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('boilerplate');
	}

	public function get_settings()
	{
		// Init array
		$settings = [];

		// Custom settings
		$settings['gl-saasoptics-settings'] = [
			'full_title' => 'SaasOptics Order integration settings',
			'menu_title' => 'SaasOptics',
			'tab_title' => 'Settings',
			'groups' => [
				[
					'title' => __('Standard fields', 'gl_saasoptics'), // Leave title empty if you don't need it
					// 'description' => [ $this, 'general_description' ], // Custom callback
					'fields' => [
						[
							'id' => 'api_key', // The $option_prefix will automatically be prepended to the id. So this option will become "boilerplate_custom_text" in the options table.
							'type' => 'text',
							'label' => __('SaasOptics API key', 'gl_saasoptics'),
							'description' => __('We need the api key to be able to communicate with SaasOptics', 'gl_saasoptics'),
							'default' => 'Please input your api key here',
						],
						[
							'id' => 'api_url', // The $option_prefix will automatically be prepended to the id. So this option will become "boilerplate_custom_text" in the options table.
							'type' => 'text',
							'label' => __('SaasOptics API url', 'gl_saasoptics'),
							'description' => __('We need the api url to be able to communicate with SaasOptics', 'gl_saasoptics'),
							'default' => 'Please input the url here (https://t56.saasoptics.com/saasoptics_demo/api/v1.0/)',
						],
						[
							'id' => 'register',
							'type' => 'select',
							'label' => __('Select register', 'boilerplate'),
							'description' => '',
							'options' =>
							$this->get_so_register_options(),
							'default' => get_option('gl_saas_optics_register', ''),
						],
						[
							'id' => 'monthly',
							'type' => 'select',
							'label' => __('Select monthly product', 'boilerplate'),
							'description' => '',
							'options' =>
							$this->get_so_items(),
							'default' => get_option('gl_saas_optics_monthly', ''),
						],
						[
							'id' => 'yearly',
							'type' => 'select',
							'label' => __('Select yearly product', 'boilerplate'),
							'description' => '',
							'options' =>
							$this->get_so_items(),
							'default' => get_option('gl_saas_optics_yearly', ''),
						],
					],
				],
			]
		];
		// echo "<pre>";
		// var_dump(get_option('gl_saas_optics_yearly', ''));
		// echo "</pre>";
		// exit();
		// Custom sub settings
		/* $settings['boilerplate-advanced-settings'] = [
			'full_title' => 'Advanced settings fields',
			'menu_title' => 'Advanced',
			'tab_title' => 'Advanced',
			'groups' => [
				[
					'title' => __('Advanced fields', 'boilerplate'),
					'fields' => [
						[
							'id' => 'custom_image',
							'type' => 'image',
							'label' => __('Image Field', 'boilerplate'),
							'description' => __('Image uploads through the media library.', 'boilerplate'),
						],
						[
							'id' => 'custom_multi_checkbox',
							'type' => 'checkbox_multi',
							'label' => __('Multi Checkbox Field', 'boilerplate'),
							'description' => __('Default value can be either a single option, or an array of options', 'boilerplate'),
							'options' => [
								'option1' => 'Option 1',
								'option2' => 'Option 2',
								'option3' => 'Option 3',
								'option4' => 'Option 4',
							],
							'default' => 'option2',
						],
						[
							'id' => 'custom_multi_select',
							'type' => 'select_multi',
							'label' => __('Multi Select Field', 'boilerplate'),
							'description' => '',
							'options' => [
								'option1' => 'Option 1',
								'option2' => 'Option 2',
								'option3' => 'Option 3',
								'option4' => 'Option 4',
							],
							'default' => ['option2', 'option4'],
						],
						[
							'id' => 'custom_color_picker',
							'type' => 'color',
							'label' => __('Color Picker Field', 'boilerplate'),
							'description' => '',
							'default' => '#303030',
						],
						[
							'id' => 'custom_text_group',
							'type' => 'text_group',
							'label' => __('Text Group', 'zencode'),
							'description' => '',
							'options' => [
								'field_1' => 'Field 1',
								'field_2' => 'Field 2',
								'field_3' => 'Field 3',
							],
						],
						[
							'id' => 'custom_general_wysiwyg',
							'type' => 'wysiwyg',
							'label' => __('Wysiwyg Field', 'boilerplate'),
							'description' => __('This is the full featured WordPress editor you know.', 'boilerplate'),
							'default' => '',
						],
					],
				],
			]
		]; */

		return $settings;
	}

	public function register_settings()
	{
		$this->register_settings_fields($this->settings);
	}

	public function add_settings_menus()
	{
		$counter = 1;
		$main_settings = '';

		foreach ($this->settings as $key => $data) {
			$full_title = $data['full_title'];
			$menu_title = $data['menu_title'];

			if ($counter === 1) {
				$main_settings = $key;
				add_menu_page($full_title, $menu_title, 'manage_options', $key, [$this, 'render_boilerplate_settings_page'], 'dashicons-admin-generic', 90);
			} else {
				add_submenu_page($main_settings, $full_title, $menu_title, 'manage_options', $key, [$this, 'render_boilerplate_settings_page']);
			}

			++$counter;
		}
	}

	public function render_boilerplate_settings_page()
	{
		$current_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

		if (!empty($this->settings[$current_page])) {
			$settings = $this->settings[$current_page];

			echo '<div class="wrap">
				<h1>' . $settings['full_title'] . '</h1>
				<form method="post" action="options.php" novalidate="novalidate">';

			$this->add_tabs();

			settings_fields($current_page);
			do_settings_sections($current_page);
			submit_button();

			echo '</form>
			</div>';
		}
	}

	public function add_tabs()
	{
		echo '<h2 class="nav-tab-wrapper">' . "\n";

		$current_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
		foreach ($this->settings as $key => $section) {
			$url = admin_url('/admin.php?page=' . $key);
			$title = $section['tab_title'];
			$active = $current_page == $key ? true : false;

			$class = 'nav-tab';
			if (!empty($active)) {
				$class .= ' nav-tab-active';
			}

			echo '<a href="' . esc_attr($url) . '" class="' . esc_attr($class) . '">' . esc_html($title) . '</a>' . "\n";
		}

		echo '</h2>' . "\n";
	}

	public function register_settings_fields($settings)
	{
		foreach ($settings as $location => $data) {
			foreach ($data['groups'] as $key => $group) {
				$group_title = $group['title'];
				$group_id = sanitize_title($group_title);
				$description_callback = $group['description'] ?? '__return_empty_string';
				$fields = $group['fields'];

				add_settings_section($group_id, $group_title, $description_callback, $location);

				foreach ($fields as $field) {
					if (empty($field['id'])) {
						$id = md5((!empty($field['description']) ? $field['description'] : $field['type']) . microtime(true));
						$field['id'] = $this->option_prefix . $id;
					} else {
						$field['id'] = $this->option_prefix . $field['id'];
					}

					register_setting($location, $field['id']);
					add_settings_field($field['id'], $field['label'], [$this, 'render_settings_field'], $location, $group_id, $field);
				}
			}
		}
	}

	public function render_settings_field($field)
	{
		switch ($field['type']) {
			case 'checkbox':
				echo '<fieldset>';

				$default = get_option($field['id'], $field['default']);

				echo '<label for="' . esc_attr($field['id']) . '"><input name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" type="checkbox" value="1"' . (!empty($default) ? ' checked' : '') . '> ' . esc_html($field['description']) . '</label>';

				echo '</fieldset>';

				break;
			case 'image':
				$image_id = get_option($field['id']);
				$image_object = '';

				if (!empty($image_id)) {
					$image_object = wp_get_attachment_image($image_id, 'medium');
				}

				echo '<input name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" type="hidden" value="' . esc_attr($image_id) . '">
				<style>#' . esc_attr($field['id']) . '-preview .image img{margin-bottom: 10px;}</style>
				<div id="' . esc_attr($field['id']) . '-preview"><div class="image">' . $image_object . '</div></div>
				<input class="upload-image button" type="button" data-id="' . esc_attr($field['id']) . '" value="' . esc_attr__('Choose image', 'boilerplate') . '">
				<input class="remove-image button" type="button" data-id="' . esc_attr($field['id']) . '" value="' . esc_attr__('Remove image', 'boilerplate') . '"' . (empty($image_id) ? ' style="display: none;"' : '') . '>';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'select':
				echo '<select name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" class="regular-text styled-select">';

				if (!empty($field['options'])) {
					$default = get_option($field['id'], $field['default']);

					foreach ($field['options'] as $key => $value) {
						echo '<option value="' . esc_attr($key) . '"' . ((string) $key === $default ? ' selected' : '') . '>' . esc_html($value) . '</option>';
					}
				}

				echo '</select>';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'select_multi':
				echo '<select name="' . esc_attr($field['id']) . '[]" id="' . esc_attr($field['id']) . '" class="regular-text styled-select" multiple>';

				if (!empty($field['options'])) {
					$default = get_option($field['id'], $field['default']);

					if (!is_array($default)) {
						$default = [$default];
					}

					foreach ($field['options'] as $key => $value) {
						echo '<option value="' . esc_attr($key) . '"' . (in_array($key, $default) ? ' selected' : '') . '>' . esc_html($value) . '</option>';
					}
				}

				echo '</select>';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'radio':
				echo '<fieldset>';

				if (!empty($field['options'])) {
					$default = get_option($field['id'], $field['default']);

					$i = 0;
					foreach ($field['options'] as $key => $value) {
						if (++$i > 1) {
							echo '<br>';
						}

						echo '<label for="' . esc_attr($field['id']) . '-' . $i . '"><input name="' . esc_attr($field['id']) . '" id="' . $field['id'] . '-' . $i . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($key) . '"' . ($key === $default ? ' checked' : '') . '> ' . esc_html($value) . '</label>';
					}
				}

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				echo '</fieldset>';

				break;
			case 'checkbox_multi':
				echo '<fieldset>';

				if (!empty($field['options'])) {
					$default = get_option($field['id'], $field['default']);

					if (!is_array($default)) {
						$default = [$default];
					}

					$i = 0;
					foreach ($field['options'] as $key => $value) {
						if (++$i > 1) {
							echo '<br>';
						}

						echo '<label for="' . esc_attr($field['id']) . '-' . $i . '"><input name="' . esc_attr($field['id']) . '[]" id="' . esc_attr($field['id']) . '-' . $i . '" type="checkbox" value="' . $key . '"' . (in_array($key, $default) ? ' checked' : '') . '> ' . esc_html($value) . '</label>';
					}
				}

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				echo '</fieldset>';

				break;
			case 'color':
				$default = get_option($field['id'], $field['default']);

				echo '<input name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" type="text" value="' . esc_attr($default) . '" data-default-color="' . esc_attr($default) . '" class="regular-text color-picker">';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'textarea':
				$default = get_option($field['id'], $field['default']);

				echo '<textarea name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" rows="6" class="regular-text code">' . esc_html($default) . '</textarea>';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'number':
				$default = get_option($field['id'], $field['default']);

				$min = $field['min'] ?? '0';
				$max = $field['max'] ?? null;
				$step = $field['step'] ?? '1';

				echo '<input name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($default) . '" min="' . esc_attr($min) . '"' . (!empty($max) ? ' max="' . esc_attr($max) . '"' : '') . ' step="' . esc_attr($step) . '" class="regular-text">';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'password':
				$default = get_option($field['id']);

				echo '<input name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($default) . '" class="regular-text">';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'wysiwyg':
				$default = get_option($field['id']);

				wp_editor($default, esc_attr($field['id']));

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				break;
			case 'message':
				if (!empty($field['description'])) {
					echo '<div class="description">' . esc_html($field['description']) . '</div>';
				}

				break;
			case 'text_group':
				echo '<fieldset>';

				if (!empty($field['options'])) {
					$saved = get_option($field['id'], []);

					$i = 0;
					foreach ($field['options'] as $key => $value) {
						$default = $saved[$key] ?? '';

						if (++$i > 1) {
							echo '<br>';
						}

						echo '<label for="' . esc_attr($field['id']) . '-' . $i . '" class="text-group">' . esc_html($value) . '</label>';
						echo '<input name="' . esc_attr($field['id']) . '[' . $key . ']" id="' . esc_attr($field['id']) . '-' . $i . '" type="text" value="' . esc_attr($default) . '" class="regular-text">';
					}
				}

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}

				echo '</fieldset>';

				break;
			default:
				$default = get_option($field['id'], $field['default']);

				echo '<input name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($default) . '" class="regular-text">';

				if (!empty($field['description'])) {
					echo '<p class="description" id="' . esc_attr($field['id']) . '-description">' . esc_html($field['description']) . '</p>';
				}
		}
	}

	public function get_so_register_options()
	{
		$api_key = get_option('gl_saas_optics_api_key');
		$api_url = get_option('gl_saas_optics_api_url');

		if (!empty($api_key) && !empty($api_url)) {
			$so_client = new SaasOpticsClient($api_key, $api_url);
			$response = $so_client->get('registers');
			$options['empty'] = 'Please choose register';
			foreach (json_decode($response['body'])->results as $register) {
				$options[$register->id] = $register->name;
			}

			return $options;
		}
	}

	public function get_so_items()
	{
		$api_key = get_option('gl_saas_optics_api_key');
		$api_url = get_option('gl_saas_optics_api_url');

		if (!empty($api_key) && !empty($api_url)) {
			$so_client = new SaasOpticsClient($api_key, $api_url);
			$response = $so_client->get('items');
			$options['empty'] = 'Please choose item';
			foreach (json_decode($response['body'])->results as $item) {
				$options[$item->id] = $item->name;
			}

			return $options;
		}
	}
}