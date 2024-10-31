<?php
/**
 * Oboxmedia Wordpress Plugin Oboxads Ad Widget
 * @version 1.0.0
 * @package oboxmedia-ads-plugin
 */

class Oboxads_Ad_Widget extends WP_Widget {

	/**
	 * Unique identifier for this widget.
	 *
	 * Will also serve as the widget class.
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $widget_slug = 'oboxads-ad-widget';


	/**
	 * Widget name displayed in Widgets dashboard.
	 * Set in __construct since __() shouldn't take a variable.
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $widget_name = '';


	/**
	 * Default widget title displayed in Widgets dashboard.
	 * Set in __construct since __() shouldn't take a variable.
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $default_widget_title = '';


	/**
	 * Shortcode name for this widget
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected static $shortcode = 'oboxads-ad-widget';


	/**
	 * Construct widget class.
	 *
	 * @since 1.0.0
	 * @return  null
	 */
	public function __construct() {

		$this->widget_name          = esc_html__( 'Oboxmedia Ad Widget', 'oboxads' );
		$this->default_widget_title = '';

		parent::__construct(
			$this->widget_slug,
			$this->widget_name,
			array(
				'classname'   => $this->widget_slug,
				'description' => esc_html__( 'Put ads in your sidebars', 'oboxads' ),
			)
		);

		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		add_shortcode( self::$shortcode, array( __CLASS__, 'get_widget' ) );
	}


	/**
	 * Delete this widget's cache.
	 *
	 * Note: Could also delete any transients
	 * delete_transient( 'some-transient-generated-by-this-widget' );
	 *
	 * @since  1.0.0
	 * @return  null
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_slug, 'widget' );
	}


	/**
	 * Front-end display of widget.
	 *
	 * @since  1.0.0
	 * @param  array  $args      The widget arguments set up when a sidebar is registered.
	 * @param  array  $instance  The widget settings as set by user.
	 * @return  null
	 */
	public function widget( $args, $instance ) {

		echo self::get_widget( array(
			'before_widget' => $args['before_widget'],
			'after_widget'  => $args['after_widget'],
			'before_title'  => $args['before_title'],
			'after_title'   => $args['after_title'],
			'title'         => $instance['title'],
			'section'       => $instance['section'],
			'context'       => isset($instance['context']) ? $instance['context'] : '',
			'post-id'    => isset($instance['post-id']) ? $instance['post-id'] : '',
			'categories'    => isset($instance['categories']) ? $instance['categories'] : '',
			'tags'    => isset($instance['tags']) ? $instance['tags'] : '',
			'playlistid'    => isset($instance['playlistid']) ? $instance['playlistid'] : '',
			'playerid'    => isset($instance['playerid']) ? $instance['playerid'] : '',
			'playertitle'    => isset($instance['playertitle']) ? $instance['playertitle'] : '',
			'autoplay'    => isset($instance['autoplay']) ? $instance['autoplay'] : '',
		) );

	}


	/**
	 * Return the widget/shortcode output
	 *
	 * @since  1.0.0
	 * @param  array  $atts Array of widget/shortcode attributes/args
	 * @return string       Widget output
	 */
	public static function get_widget( $atts ) {
		$widget = '';

		// Set up default values for attributes
		$atts = shortcode_atts(
			array(
				// Ensure variables
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
				'title'         => '',
				'section'          => 'side',
				'context'          => '',
				'post-id'          => '',
				'categories'          => '',
				'tags'          => '',
				'playlistid'          => '',
				'playerid'          => '',
				'playertitle'          => '',
				'autoplay'          => '',
				
			),
			(array) $atts,
			self::$shortcode
		);

		// Before widget hook
		$widget .= $atts['before_widget'];

		// Title
		$widget .= ( $atts['title'] ) ? $atts['before_title'] . esc_html( $atts['title'] ) . $atts['after_title'] : '';

        $widget .= oboxadsGetAd($atts['section'], array(
			'context' => $atts['context'], 
			'playlistid' => $atts['playlistid'],
			'playerid' => $atts['playerid'],
			'post-id' => $atts['post-id'],
			'categories' => $atts['categories'],
			'tags' => $atts['tags'],
			'playertitle' => $atts['playertitle'],
			'autoplay' => $atts['autoplay'],
		));

		// After widget hook
		$widget .= $atts['after_widget'];

		return $widget;
	}


	/**
	 * Update form values as they are saved.
	 *
	 * @since  1.0.0
	 * @param  array  $new_instance  New settings for this instance as input by the user.
	 * @param  array  $old_instance  Old settings for this instance.
	 * @return array  Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {

		// Previously saved values
		$instance = $old_instance;

		// Sanitize title before saving to database
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['section'] = force_balance_tags( $new_instance['section'] );
		} else {
			$instance['section'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['section'] ) ) );
		}

		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['context'] = force_balance_tags( $new_instance['context'] );
		} else {
			$instance['context'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['context'] ) ) );
		}


		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['context'] = force_balance_tags( $new_instance['context'] );
		} else {
			$instance['context'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['context'] ) ) );
		}
		
		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['playlistid'] = force_balance_tags( $new_instance['playlistid'] );
		} else {
			$instance['playlistid'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['playlistid'] ) ) );
		}

		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['post-id'] = force_balance_tags( $new_instance['post-id'] );
		} else {
			$instance['post-id'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['post-id'] ) ) );
		}

		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['categories'] = force_balance_tags( $new_instance['categories'] );
		} else {
			$instance['categories'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['categories'] ) ) );
		}
		
		// Sanitize text before saving to database
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['tags'] = force_balance_tags( $new_instance['tags'] );
		} else {
			$instance['tags'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['tags'] ) ) );
		}


		// Flush cache
		$this->flush_widget_cache();

		return $instance;
	}


	/**
	 * Back-end widget form with defaults.
	 *
	 * @since  1.0.0
	 * @param  array  $instance  Current settings.
	 * @return  null
	 */
	public function form( $instance ) {

		// If there are no settings, set up defaults
		$instance = wp_parse_args( (array) $instance,
			array(
				'title' => $this->default_widget_title,
				'section'  => 'side',
				'context'  => '',
				'post-id'  => '',
				'categories' => '',
				'tags' => ''
			)
		);

        $sections = array(
            "header" => "Header",
            "side" => "Side Rail",
            "content" => "Content",
            "footer" => "Footer",
			"instream" => "Video",
		);
		

		?>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'oboxads' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $instance['title'] ); ?>" placeholder="optional" /></p>

		<p><label for="<?php echo $this->get_field_id( 'section' ); ?>"><?php _e( 'Section:', 'oboxads' ); ?></label>
        <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'section' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'section' ) ); ?>" onchange="onOboxSectionChanged(this.value)">
        <?php foreach ($sections as $key => $name): ?>
            <option value="<?php echo esc_attr($key);?>" <?php echo ($key == $instance['section'] ? 'selected' : ''); ?>><?php esc_html_e($name); ?></option>
        <?php endforeach; ?>
        </select></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'context' ) ); ?>"><?php esc_html_e( 'Context:', 'oboxads' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'context' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'context' ) ); ?>" type="text" value="<?php echo esc_html( $instance['context'] ); ?>" placeholder="optional" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'post-id' ) ); ?>"><?php esc_html_e( 'Post ID:', 'oboxads' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post-id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post-id' ) ); ?>" type="text" value="<?php echo esc_html( $instance['post-id'] ); ?>" placeholder="optional" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'categories' ) ); ?>"><?php esc_html_e( 'Categories:', 'oboxads' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'categories' ) ); ?>" type="text" value="<?php echo esc_html( $instance['categories'] ); ?>" placeholder="optional. Seprate categories with commas. Ex: cat1, cat2, cat3" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>"><?php esc_html_e( 'Tags:', 'oboxads' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags' ) ); ?>" type="text" value="<?php echo esc_html( $instance['tags'] ); ?>" placeholder="optional. Seprate tags with commas. Ex: tag1, tag2, tag3" /></p>

		<style>
			.instream-options {
				display: none;
			}
			.instream-options.active {
				display: block;
			}
		</style>
		<script>
			var onOboxSectionChanged = (selectedSection) =>
			{
				if (selectedSection === 'instream') {
					document.querySelectorAll('.instream-options').forEach(element => {
						element.classList.add("active");
					});
				} else {
					document.querySelectorAll('.instream-options').forEach(element => {
						element.classList.remove("active");
					});
					document.querySelectorAll('.instream-options input, .instream-options select').forEach(element => {
						element.value = ''
					});
				} 
			}
		</script>

		<?php
	}
}


/**
 * Register this widget with WordPress. Can also move this function to the parent plugin.
 *
 * @since  1.0.0
 * @return  null
 */
function oboxads_register_ad_widget() {
	register_widget( 'Oboxads_Ad_Widget' );
}
add_action( 'widgets_init', 'oboxads_register_ad_widget' );
