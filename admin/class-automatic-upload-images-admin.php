<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.iranimij.com
 * @since      1.0.0
 *
 * @package    Automatic_Upload_Images
 * @subpackage Automatic_Upload_Images/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Automatic_Upload_Images
 * @subpackage Automatic_Upload_Images/admin
 * @author     iman  <heydari>
 */
class Automatic_Upload_Images_Admin {

    const WP_OPTIONS_KEY = 'aui-setting';
    private static $_options;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Automatic_Upload_Images_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Automatic_Upload_Images_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/automatic-upload-images-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Automatic_Upload_Images_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Automatic_Upload_Images_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/automatic-upload-images-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function savePost($data, $postarr)
    {
        if (wp_is_post_revision($postarr['ID']) ||
            wp_is_post_autosave($postarr['ID']) ||
            (defined('DOING_AJAX') && DOING_AJAX) ||
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
            return $data;
        }

        if ($content = $this->save($postarr)) {
            $data['post_content'] = $content;
        }
        return $data;
    }

    public function save($postarr)
    {
        $excludePostTypes = self::getOption('exclude_post_types');
        if (is_array($excludePostTypes) && in_array($postarr['post_type'], $excludePostTypes, true)) {
            return false;
        }

        $content = $postarr['post_content'];
        $images = $this->findAllImageUrls(stripslashes($content));

        if (count($images) == 0) {
            return false;
        }
//var_dump($images);die();
        foreach ($images as $image) {
            $uploader = new ImageUploader($image['url'], $image['alt'], $postarr);

            if ($uploadedImage = $uploader->save()) {
                $urlParts = parse_url($uploadedImage['url']);
                $base_url = $uploader::getHostUrl(null, true);
                $image_url = $base_url . $urlParts['path'];

                $content = preg_replace('/'. preg_quote($image['url'], '/') .'/', $image_url, $content);
                $content = preg_replace('/alt=["\']'. preg_quote($image['alt'], '/') .'["\']/', "alt='{$uploader->getAlt()}'", $content);
            }
        }
        return $content;
    }

    public static function getOption($key, $default = null)
    {
        $options = static::getOptions();
        if (isset($options[$key]) === false) {
            return $default;
        }
        return $options[$key];
    }

    /**
     * Returns options in an array
     * @return array
     */
    public static function getOptions()
    {
        if (static::$_options) {
            return static::$_options;
        }
        $defaults = array(
            'base_url' => get_bloginfo('url'),
            'image_name' => '%filename%',
            'alt_name' => '%image_alt%',
        );
        return static::$_options = wp_parse_args(get_option(self::WP_OPTIONS_KEY), $defaults);
    }

    public function addAdminMenu()
    {
        add_options_page(
            __('Automatic Upload Images Settings', 'automatic-upload-images'),
            __('Automatic Upload Images', 'automatic-upload-images'),
            'manage_options',
            'auto-upload',
            array($this, 'settingPage')
        );
    }

    /**
     * Settings page contents
     */
    public function settingPage()
    {
        $message = [];
        $error = false;
        if (isset($_POST['submit'])) {
            $fields = array('base_url', 'image_name', 'alt_name', 'exclude_urls', 'max_width', 'max_height', 'exclude_post_types');
            foreach ($fields as $field) {
                if (array_key_exists($field, $_POST) && $_POST[$field]) {
                    if ($field == "max_width" && !is_int($_POST[$field])){
                            $error = true;
                            $message["error"][] = "image size is not valid !";
                    }
                    if ($field == "max_height" && !is_int($_POST[$field])){
                            $error = true;
                            $message["error"][] = "image size is not valid !";
                    }
                    if ($field == "base_url" && filter_var($_POST[$field], FILTER_VALIDATE_URL) === FALSE) {
                        $error = true;
                        $message["error"][] = "url is not valid !";
                    }
                    if (!$error) {
                        if ($field != "exclude_post_types") {
                            static::$_options[$field] = sanitize_text_field($_POST[$field]);
                        } elseif ($field == "exclude_post_types") {

                            $tags = isset($_POST[$field]) ? (array)$_POST[$field] : array();
                            $tags = array_map('sanitize_text_field', $tags);
                            static::$_options[$field] = $tags;
                        }
                    }
                }
            }
            if (!$error){
            update_option(self::WP_OPTIONS_KEY, static::$_options);
            $message["success"] = __('Settings Saved.', 'automatic-upload-images');
            }
        }

        if (isset($_POST['reset']) && self::resetOptionsToDefaults()) {
            $message["success"] = __('Successfully settings reset to defaults.', 'automatic-upload-images');
        }

        include_once('setting-page.php');
    }

    /**
     * Reset options to default options
     * @return bool
     */
    public static function resetOptionsToDefaults()
    {
        $defaults = array(
            'base_url' => get_bloginfo('url'),
            'image_name' => '%filename%',
            'alt_name' => '%image_alt%',
        );
        static::$_options = $defaults;
        return update_option(self::WP_OPTIONS_KEY, $defaults);
    }

    /**
     * Find image urls in content and retrieve urls by array
     * @param $content
     * @return array
     */
    public function findAllImageUrls($content)
    {
        $urls1 = array();
        preg_match_all('/<img[^>]*srcset=["\']([^"\']*)[^"\']*["\'][^>]*>/i', $content, $srcsets, PREG_SET_ORDER);
        if (count($srcsets) > 0) {
            $count = 0;
            foreach ($srcsets as $key => $srcset) {
                preg_match_all('/https?:\/\/[^\s,]+/i', $srcset[1], $srcsetUrls, PREG_SET_ORDER);
                if (count($srcsetUrls) == 0) {
                    continue;
                }
                foreach ($srcsetUrls as $srcsetUrl) {
                    $urls1[$count][] = $srcset[0];
                    $urls1[$count][] = $srcsetUrl[0];
                    $count++;
                }
            }
        }

        preg_match_all('/<img[^>]* src=["\']([^"\']*)[^"\']*["\'][^>]*>/i', $content, $urls, PREG_SET_ORDER);
        $urls = array_merge($urls, $urls1);

        if (count($urls) == 0) {
            return array();
        }
        foreach ($urls as $index => &$url) {
            $images[$index]['alt'] = preg_match('/<img[^>]*alt=["\']([^"\']*)[^"\']*["\'][^>]*>/i', $url[0], $alt) ? $alt[1] : null;
            $images[$index]['url'] = $url = $url[1];
        }
        foreach (array_unique($urls) as $index => $url) {
            $unique_array[] = $images[$index];
        }
        return $unique_array;
    }
}
