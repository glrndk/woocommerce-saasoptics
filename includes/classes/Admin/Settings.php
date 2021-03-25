<?php

class BoilerplateSettings {
	
	public $option_prefix = 'boilerplate_';
	
	public function __construct() {
		$this->init();
	}
	
	public function init() {
		add_action( 'admin_menu', [ $this, 'add_settings_menu' ] );
		add_action( 'admin_menu', [ $this, 'add_settings_sub_menu' ] );
		add_action( 'admin_menu', [ $this, 'add_options_menu' ] );
		
		add_action( 'admin_init', [ $this, 'register_wp_settings' ] );
		
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_footer', [ $this, 'custom_scripts' ] );
	}
	
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}
	
	public function custom_scripts() {
		echo '<script>
		jQuery(document).ready(function($){
			var custom_uploader;
			
			$(\'.remove-image\').on(\'click\', function(e) {
				var object_id = $(this).data(\'id\');
				var $input_object = $(\'#\'+object_id);
				
				$input_object.val(\'\');
				$(\'#\'+object_id+\'-preview .image\').html(\'\');
				$(this).hide(0);
			});
			
			$(\'.upload-image\').on(\'click\', function(e) {
				e.preventDefault();
				
				var object_id = $(this).data(\'id\');
				var $input_object = $(\'#\'+object_id);
				var $remove_button = $(this).next();
				
				//If the uploader object has already been created, reopen the dialog
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
				
				//Extend the wp.media object
				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: \'Choose Image\',
					button: {
						text: \'Choose Image\'
					},
					multiple: false
				});
				
				//When a file is selected, grab the URL and set it as the text field\'s value
				custom_uploader.on(\'select\', function() {
					attachment = custom_uploader.state().get(\'selection\').first().toJSON();
					
					var image_url = attachment.url;
					
					if(attachment.sizes.medium !== undefined){
						image_url = attachment.sizes.medium.url;
					}
					
					$input_object.val(attachment.id);
					$(\'#\'+object_id+\'-preview .image\').html(\'<img src="\'+image_url+\'" alt="">\');
					$remove_button.show(0);
					console.log(attachment);
					console.log(\'#\'+object_id+\'-preview .image\');
				});
				
				//Open the uploader dialog
				custom_uploader.open();
			});
			
			$(\'.color-picker\').wpColorPicker();
		});
		</script>';
	}
	
	public function register_wp_settings() {
		// Init array
		$settings = [];
		
		// General settings
		$settings['general'] = [
			[
				'title' => __( 'General extra', 'boilerplate' ),
				// 'description' => [ $this, 'general_description' ], // Custom callback
				'fields' => [
					[
						'id' => 'custom_general_checkbox',
						'type' => 'checkbox',
						'label' => __( 'Custom Checkbox Label', 'boilerplate' ),
						'description' => __( 'Description of the checkbox field', 'boilerplate' ),
						'default' => '1',
					],
					[
						'id' => 'custom_general_text',
						'type' => 'text',
						'label' => __( 'Custom Text Label', 'boilerplate' ),
						'description' => __( 'Description of the text field', 'boilerplate' ),
						'default' => 'Default text',
					],
					[
						'id' => 'custom_general_image',
						'type' => 'image',
						'label' => __( 'Custom Image Label', 'boilerplate' ),
						'description' => __( 'Description of the image field', 'boilerplate' ),
					],
					[
						'id' => 'custom_general_select',
						'type' => 'select',
						'label' => __( 'Custom Select Label', 'boilerplate' ),
						'description' => __( 'Description of the select field', 'boilerplate' ),
						'options' => [
							'option' => 'Option',
						],
						'default' => '',
					],
					[
						'id' => 'custom_general_radio',
						'type' => 'radio',
						'label' => __( 'Custom Radio Label', 'boilerplate' ),
						'description' => __( 'Description of the radio fields', 'boilerplate' ),
						'options' => [
							'option1' => 'Option 1',
							'option2' => 'Option 2',
							'option3' => 'Option 3',
						],
						'default' => 'option2',
					],
					[
						'id' => 'custom_general_multi_checkbox',
						'type' => 'checkbox_multi',
						'label' => __( 'Custom Multi Checkbox Label', 'boilerplate' ),
						'description' => __( 'Description of the multi checkbox fields', 'boilerplate' ),
						'options' => [
							'option1' => 'Option 1',
							'option2' => 'Option 2',
							'option3' => 'Option 3',
							'option4' => 'Option 4',
						],
						'default' => 'option2',
					],
					[
						'id' => 'custom_general_multi_select',
						'type' => 'select_multi',
						'label' => __( 'Custom Multi Select Label', 'boilerplate' ),
						'description' => __( 'Description of the multi select fields', 'boilerplate' ),
						'options' => [
							'option1' => 'Option 1',
							'option2' => 'Option 2',
							'option3' => 'Option 3',
							'option4' => 'Option 4',
						],
						'default' => [ 'option2', 'option4' ],
					],
					[
						'id' => 'custom_general_color_picker',
						'type' => 'color',
						'label' => __( 'Custom Color Picker Label', 'boilerplate' ),
						'description' => __( 'Description of the color picker field', 'boilerplate' ),
						'default' => '#303030',
					],
					[
						'id' => 'custom_general_textarea',
						'type' => 'textarea',
						'label' => __( 'Custom Textarea Label', 'boilerplate' ),
						'description' => __( 'Description of the textarea field', 'boilerplate' ),
						'default' => '',
					],
					[
						'id' => 'custom_general_wysiwyg',
						'type' => 'wysiwyg',
						'label' => __( 'Custom Wysiwyg Label', 'boilerplate' ),
						'description' => __( 'Description of the wysiwyg editor', 'boilerplate' ),
						'default' => '',
					],
					[
						'id' => 'custom_general_number',
						'type' => 'number',
						'label' => __( 'Custom Number Label', 'boilerplate' ),
						'description' => __( 'Description of the number field', 'boilerplate' ),
						'min' => '3',
						'max' => '999',
						'step' => '.1',
						'default' => 123,
					],
					[
						'id' => 'custom_general_password',
						'type' => 'password',
						'label' => __( 'Custom Password Label', 'boilerplate' ),
						'description' => __( 'Description of the password field', 'boilerplate' ),
					],
				],
			],
		];
		
		// Writing settings
		$settings['writing'] = [];
		
		// Reading settings
		$settings['reading'] = [];
		
		// Discussion settings
		$settings['discussion'] = [];
		
		// Permalink settings
		$settings['permalink'] = [];
		
		// Custom settings
		$settings['boilerplate-settings'] = [];
		
		// Custom sub settings
		$settings['boilerplate-sub-settings'] = [];
		
		// Custom options
		$settings['boilerplate-options'] = [];
		
		$this->register_settings_fields( $settings );
	}
	
	public function add_settings_menu() {
		add_menu_page( 'Plugin Settings', 'Plugin Settings', 'manage_options', 'boilerplate-settings', [ $this, 'render_custom_settings_page' ], 'dashicons-admin-generic', 90 );
	}
	
	public function add_settings_sub_menu() {
		add_submenu_page( 'boilerplate-settings', 'Sub Settings', 'Sub Settings', 'manage_options', 'boilerplate-sub-settings', [ $this, 'render_custom_sub_settings_page' ] );
	}
	
	public function add_options_menu() {
		add_options_page( 'Custom Settings', 'Custom Settings', 'manage_options', 'boilerplate-custom-options', [ $this, 'render_custom_options_page' ] );
	}
	
	public function render_custom_settings_page() {
		echo '<h1>' . __( 'Boilerplate Settings Page', 'boilerplate' ) . '</h1>
		<form method="post" action="options.php" novalidate="novalidate">';
		
		$this->add_tabs( [
			[
				'url' => '?page=boilerplate-settings',
				'title' => 'Settings',
				'active' => true,
			],
			[
				'url' => '?page=boilerplate-sub-settings',
				'title' => 'Sub Settings',
				'active' => false,
			],
		] );
		
		settings_fields( 'boilerplate-settings' );
		do_settings_sections( 'boilerplate-settings' );
		// submit_button();
		
		echo '</form>';
	}
	
	public function render_custom_sub_settings_page() {
		echo '<h1>' . __( 'Boilerplate Sub Settings Page', 'boilerplate' ) . '</h1>
		<form method="post" action="options.php" novalidate="novalidate">';
		
		$this->add_tabs( [
			[
				'url' => '?page=boilerplate-settings',
				'title' => 'Settings',
				'active' => false,
			],
			[
				'url' => '?page=boilerplate-sub-settings',
				'title' => 'Sub Settings',
				'active' => true,
			],
		] );
		
		settings_fields( 'boilerplate-sub-settings' );
		do_settings_sections( 'boilerplate-sub-settings' );
		// submit_button();
		
		echo '</form>';
	}
	
	public function render_custom_options_page() {
		echo '<h1>' . __( 'Boilerplate Options Page', 'boilerplate' ) . '</h1>
		<form method="post" action="options.php" novalidate="novalidate">';
		
		settings_fields( 'boilerplate-options' );
		do_settings_sections( 'boilerplate-options' );
		// submit_button();
		
		echo '</form>';
	}
	
	public function add_tabs( $links ) {
		echo'<h2 class="nav-tab-wrapper">' . "\n";

		foreach ( $links as $link ) {
			$url = $link['url'];
			$title = $link['title'];
			$active = $link['active'] ?? null;

			$class = 'nav-tab';
			if ( ! empty( $active ) ) {
				$class .= ' nav-tab-active';
			}

			echo '<a href="' . esc_attr( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $title ) . '</a>' . "\n";
		}

		echo '</h2>' . "\n";
	}
	
	public function register_settings_fields( $settings ) {
		foreach ( $settings as $location => $groups ) {
			foreach ( $groups as $group ) {
				$group_title = $group['title'];
				$group_id = sanitize_title( $group_title );
				$description_callback = $group['description'] ?? '__return_empty_string';
				$fields = $group['fields'];
				
				add_settings_section( $group_id, $group_title, $description_callback, $location );
				
				foreach ( $fields as $field ) {
					$field['id'] = $this->option_prefix . $field['id'];
					
					register_setting( $location, $field['id'] );
					add_settings_field( $field['id'], $field['label'], [ $this, 'render_settings_field' ], $location, $group_id, $field );
				}
			}
		}
	}
	
	public function render_settings_field( $field ) {
		switch ( $field['type'] ) {
			case 'checkbox':
				$default = get_option( $field['id'], $field['default'] );
				
				echo '<input name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" value="1"' . ( ! empty( $default ) ? ' checked' : '' ) . '>';
				
				if ( ! empty( $field['description'] ) ) {
					echo ' <label for="' . esc_attr( $field['id'] ) . '">' . esc_html( $field['description'] ) . '</label>';
				}
				
				break;
			case 'image':
				$image_id = get_option( $field['id'] );
				$image_object = '';
				
				if ( ! empty( $image_id ) ) {
					$image_object = wp_get_attachment_image( $image_id, 'medium' );
				}
				
				echo '<input name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" type="hidden" value="' . esc_attr( $image_id ) . '">
				<style>#' . esc_attr( $field['id'] ) . '-preview .image img{margin-bottom: 10px;}</style>
				<div id="' . esc_attr( $field['id'] ) . '-preview"><div class="image">' . $image_object . '</div></div>
				<input class="upload-image button" type="button" data-id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr__( 'Choose image', 'boilerplate' ) . '">
				<input class="remove-image button" type="button" data-id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr__( 'Remove image', 'boilerplate' ) . '"' . ( empty( $image_id ) ? ' style="display: none;"' : '' ) . '>';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'select':
				echo '<select name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '">';
				
				if ( ! empty( $field['options'] ) ) {
					$default = get_option( $field['id'], $field['default'] );
					
					foreach ( $field['options'] as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . ( $key === $default ? ' selected' : '' ) . '>' . esc_html( $value ) . '</option>';
					}
				}
				
				echo '</select>';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'select_multi':
				echo '<select name="' . esc_attr( $field['id'] ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple>';
				
				if ( ! empty( $field['options'] ) ) {
					$default = get_option( $field['id'], $field['default'] );
					
					if ( ! is_array( $default ) ) {
						$default = [ $default ];
					}
					
					foreach ( $field['options'] as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . ( in_array( $key, $default ) ? ' selected' : '' ) . '>' . esc_html( $value ) . '</option>';
					}
				}
				
				echo '</select>';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'radio':
				echo '<fieldset>';
				
				if ( ! empty( $field['options'] ) ) {
					$default = get_option( $field['id'], $field['default'] );
					
					$i = 0;
					foreach ( $field['options'] as $key => $value ) {
						if ( ++$i > 1 ) {
							echo '<br>';
						}
						
						echo '<label><input name="' . esc_attr( $field['id'] ) . '" id="' . $field['id'] . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $key ) . '"' . ( $key === $default ? ' checked' : '' ) . '> ' . esc_html( $value ) . '</label>';
					}
				}
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				echo '</fieldset>';
				
				break;
			case 'checkbox_multi':
				echo '<fieldset>';
				
				if ( ! empty( $field['options'] ) ) {
					$default = get_option( $field['id'], $field['default'] );
					
					if ( ! is_array( $default ) ) {
						$default = [ $default ];
					}
					
					$i = 0;
					foreach ( $field['options'] as $key => $value ) {
						if ( ++$i > 1 ) {
							echo '<br>';
						}
						
						echo '<label><input name="' . esc_attr( $field['id'] ) . '[]" id="' . esc_attr( $field['id'] ) . '" type="checkbox" value="' . $key . '"' . ( in_array( $key, $default ) ? ' checked' : '' ) . '> ' . esc_html( $value ) . '</label>';
					}
				}
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				echo '</fieldset>';
				
				break;
			case 'color':
				$default = get_option( $field['id'], $field['default'] );
				
				echo '<input name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" type="text" value="' . esc_attr( $default ) . '" data-default-color="' . esc_attr( $default ) . '" class="regular-text color-picker">';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'textarea':
				$default = get_option( $field['id'], $field['default'] );
				
				echo '<textarea name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" rows="6" class="regular-text code">' . esc_html( $default ) . '</textarea>';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'wysiwyg':
				$default = get_option( $field['id'] );

				wp_editor( $default, esc_attr( $field['id'] ) );
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'number':
				$default = get_option( $field['id'], $field['default'] );
				
				$min = $field['min'] ?? '0';
				$max = $field['max'] ?? null;
				$step = $field['step'] ?? '1';
				
				echo '<input name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $default ) . '" min="' . esc_attr( $min ) . '"' . ( ! empty( $max ) ? ' max="' . esc_attr( $max ) . '"' : '' ) . ' step="' . esc_attr( $step ) . '" class="regular-text">';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			case 'password':
				$default = get_option( $field['id'] );
				
				echo '<input name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $default ) . '" class="regular-text">';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
				
				break;
			default:
				$default = get_option( $field['id'], $field['default'] );
				
				echo '<input name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $default ) . '" class="regular-text">';
				
				if ( ! empty( $field['description'] ) ) {
					echo '<p class="description" id="' . esc_attr( $field['id'] ) . '-description">' . esc_html( $field['description'] ) . '</p>';
				}
		}
	}
	
}