<?php
/*
Plugin Name: BCorp Visual Editor
Plugin URI: http://bcorp.com
Description: Wordpress visual editor for editing BCorp Shortcodes.
Version: 0.20
Author: Tim Brattberg
Author URI: http://bcorp.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
*/

if(function_exists('bcorp_shortcodes_init')){
	add_action( 'bcorp_start_visual_editor', 'bcorp_visual_editor_init' ); // Run after bcorp_shortcodes_init
	function bcorp_visual_editor_init() { if (is_admin()) new BCorp_Visual_Editor(); }
} else {
  if (is_admin()) {
    add_action('media_buttons', 'add_bcorp_visual_editor_button');
    add_action('admin_enqueue_scripts','bcorp_visual_editor_admin_enqueue_scripts');
  }
}

function bcorp_visual_editor_admin_enqueue_scripts() {
  wp_enqueue_style('bcorp_visual_editor_admin_css',plugins_url( 'css/bcorp-visual-editor-admin.css' , __FILE__ ));
  wp_enqueue_script('bcorp_visual_editor_admin_js',plugins_url('js/bcorp-visual-editor-admin.js', __FILE__ ),'','',true);
  $plugins = array_keys(get_plugins());
  $myplugin = 'bcorp-shortcodes';
  $installed=false;
  foreach($plugins as $plugin) if(strpos($plugin, $myplugin.'/') === 0) { $installed = true; break; }
  if ($installed) {
    $url = wp_nonce_url( self_admin_url('plugins.php?action=activate&plugin='.$plugin), 'activate-plugin_'.$plugin);
  } else {
    $plugin = 'bcorp-shortcodes';
    $plugin_name = 'BCorp Shortcodes';
    $url = wp_nonce_url(
      add_query_arg(
        array(
          'page'          => 'bcorp_shortcodes_plugin_activation',
          'plugin'        => $plugin,
          'plugin_name'   => $plugin_name,
          'plugin_source' => !empty($source) ? urlencode($source) : false,
          'bcorp-shortcodes-install' => 'install-plugin',
        ),
        admin_url( 'plugins.php' )
      ),
      'bcorp-shortcodes-install'
    );
  }
  wp_localize_script("bcorp_visual_editor_admin_js","bcorp_installer", array('url' => $url,'installed' => $installed));
}

function add_bcorp_visual_editor_button() {
  echo '<a href="#" id="bcorp-visual-editor-button" class="button">BCorp Visual Editor</a>';
}

function bcorp_visual_editor_plugin_activation_page(){
	if( !isset( $_GET[  'bcorp-shortcodes-install' ] ) ) return;

	add_plugins_page(
		__('Install BCorp Shortcodes Plugin', 'bcorp-visual-editor'),
		__('Install BCorp Shortcodes Plugin', 'bcorp-visual-editor'),
		'install_plugins',
		'bcorp_shortcodes_plugin_activation',
		'bcorp_visual_editor_shortcodes_installer_page'
	);
}
add_action('admin_menu', 'bcorp_visual_editor_plugin_activation_page');


function bcorp_visual_editor_shortcodes_installer_page(){
	?>
	<div class="wrap">
		<?php bcorp_visual_editor_shortcodes_install() ?>
	</div>
	<?php
}

function bcorp_visual_editor_shortcodes_install(){
	if (isset($_GET[sanitize_key('plugin')]) && (isset($_GET[sanitize_key('bcorp-shortcodes-install')]) && 'install-plugin' == $_GET[sanitize_key('bcorp-shortcodes-install')]) && current_user_can('install_plugins')) {
		check_admin_referer( 'bcorp-shortcodes-install' );
		$plugin_name = $_GET['plugin_name'];
		$plugin_slug = $_GET['plugin'];
		if(!empty($_GET['plugin_source'])) $plugin_source = $_GET['plugin_source']; else $plugin_source = false;
		$url = wp_nonce_url(
			add_query_arg(
				array(
					'page'          => 'bcorp_shortcodes_plugin_activation',
					'plugin'        => $plugin_slug,
					'plugin_name'   => $plugin_name,
					'plugin_source' => $plugin_source,
					'bcorp-shortcodes-install' => 'install-plugin',
				),
				admin_url( 'themes.php' )
			),
			'bcorp-shortcodes-install'
		);
		$fields = array( sanitize_key( 'bcorp-shortcodes-install' ) );
		if (false === ($creds=request_filesystem_credentials($url,'',false,false,$fields))) return true;
		if (!WP_Filesystem($creds)) {
			request_filesystem_credentials($url,'', true,false,$fields);
			return true;
		}
		require_once ABSPATH.'wp-admin/includes/plugin-install.php';
		require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
		$title = sprintf( __('Installing %s', 'bcorp-visual-editor'), $plugin_name );
		$url = add_query_arg( array('action' => 'install-plugin','plugin' => urlencode($plugin_slug)),'update.php');
		if (isset($_GET['from'])) $url .= add_query_arg('from',urlencode(stripslashes($_GET['from'])),$url);
		$nonce = 'install-plugin_' . $plugin_slug;
		$source = !empty( $plugin_source ) ? $plugin_source : 'http://downloads.wordpress.org/plugin/'.urlencode($plugin_slug).'.zip';
		$upgrader = new Plugin_Upgrader($skin = new Plugin_Installer_Skin(compact('type','title','url','nonce','plugin','api')));
		$upgrader->install($source);
		wp_cache_flush();
	}
}












class BCorp_Visual_Editor {
	public function __construct () {
		$this->bcve_setup_admin();
	}

	public function bcve_setup_admin () {
    add_action( 'wp_ajax_bcve_ajax', array(&$this,'bcve_ajax' ));
    add_action('admin_enqueue_scripts', array(&$this,'bcve_admin_enqueue_scripts'));
		add_action('edit_form_after_title', array(&$this,'bcve_hook_visual_editor'));
		add_action('save_post', array(&$this,'bcve_save_post'));
	}

  function bcve_admin_enqueue_scripts() {
		if (get_current_screen()->id === "page" || get_post_type( get_the_ID() ) === "portfolio" ) {
	    wp_enqueue_script('iris');
	    wp_enqueue_script ('jquery-ui-draggable');
	    wp_enqueue_style('bcve_css',plugins_url( 'css/bcve.css' , __FILE__ ),array(),'0.1');
	    wp_enqueue_script('bcve_js',plugins_url( 'js/bcve.js' , __FILE__ ),array(),'0.1');
			global $post;
			if (get_post_meta($post->ID, 'bcorp_visual_editor', true) != 'true') $visual_editor = 'false'; else $visual_editor = 'true';
	    wp_localize_script("bcve_js","bcve",array('nonce' => wp_create_nonce( 'data-bcve-ajax-nonce'),
																								'sc' => $GLOBALS['bcorp_shortcodes_data']->bcsc(),
																								'vars' => $GLOBALS['bcorp_shortcodes_data']->bcsc_vars()
																							));
		}
  }
	function bcve_hook_visual_editor() {
		if (get_current_screen()->id === "page" || get_post_type( get_the_ID() ) === "portfolio" )
			add_meta_box("bcorp-visual-editor-meta", 'BCorp Visual Editor', array(&$this,"bcve_meta_box"), "", "normal", "core");
	}

	function bcve_meta_box() {
		global $post;
		if (get_post_meta($post->ID, 'bcorp_visual_editor', true) != 'true') $visual_editor = 'false'; else $visual_editor = 'true';
		?>
		<div id="bcorp-visual-editor"></div>
		<input type=hidden id="bcorp-visual-editor-active" name="bcorp_visual_editor" value="<?php echo $visual_editor; ?>"><?php
		wp_editor('', 'bcve-text-editor' );
	}

	function bcve_save_post($post_id = false, $post = false) {
		  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
			if (isset($_POST["bcorp_visual_editor"]) && $_POST["bcorp_visual_editor"] == 'true')
				update_post_meta($post_id, "bcorp_visual_editor",'true');
			else update_post_meta($post_id, "bcorp_visual_editor",'false');
	}

  function bcve_ajax() {
    if (!wp_verify_nonce($_POST['bcve_nonce'],'data-bcve-ajax-nonce')) wp_die();
    $images = $_POST['images'];
    foreach ($images as $key => $value ) {
      $values = explode(',',$value);
      if (count($values)>1) {
        $images[$key]=[];
        foreach ($values as $value) {
          $images[$key][]=wp_get_attachment_thumb_url( $value );
        }
      } else {
        $images[$key]=wp_get_attachment_thumb_url( $value );
      }
    }
    header("Content-Type: application/json");
    echo json_encode(array('success'=>'true','images'=>$images));
    wp_die();
  }
} ?>
