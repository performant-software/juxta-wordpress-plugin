<?php
/*
Plugin Name: Juxta
Plugin URI: http://juxtacommons.org
Description: Allow easy embedding of a Juxta Commons shared ressource. Format: [jx key]
Author: Performant Software
Version: 1.0
Author URI: http://www.performantsoftware.com
*/

if (isset($juxta)) return false;

$juxta = new Juxta();

class Juxta {
    var $juxta_url;
    var $opt_name = 'jx_commons_url';
    
    function __construct() {
        // setup style sheets
        add_action( 'wp_enqueue_scripts', array($this, 'prefix_add_jx_stylesheet') );
        
        // setop settings page
        add_action( 'admin_menu', array($this, 'juxta_plugin_menu') );
        
        // add the shortcode used to trigger the juxta iframe 
        add_shortcode( 'jx', array($this, 'add_juxta_iframe') );
        
        // load in the settings:
        $this->juxta_url = get_option( $this->opt_name );
        if ( strlen($this->juxta_url) == 0 ) {
            $this->juxta_url = "http://juxtacommons.org";
        }
        
    }
    
    function add_juxta_iframe($atts) {
       $val = $atts[0];
       if ( strlen($val) != 6 ) {
          return '<p style="color:#a00;font-weight:bold;font-style:italic;">Invalid Juxta Commons Key<p/>';
       }
    
       return "<iframe class='jx-wp-iframe' src='{$this->juxta_url}/shares/{$val}'></iframe>";
    }


    function prefix_add_jx_stylesheet() {
       // Respects SSL, Style.css is relative to the current file
       wp_register_style( 'jx-prefix-style', plugins_url('jx.css', __FILE__) );
       wp_enqueue_style( 'jx-prefix-style' );
    }

    function juxta_plugin_menu() {
    	add_options_page( 'Juxta Plugin Options', 'Juxta', 'manage_options', 'juxta-opts-id', array($this, 'juxta_plugin_options') );
    }
    
    function juxta_plugin_options() {
    	if ( !current_user_can( 'manage_options' ) )  {
    		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    	}
        
        $saved = FALSE;
        if( isset($_POST[ 'submitted' ]) && $_POST[ 'submitted' ] == 'y' ) {
            // grab the new data and save it
            $this->juxta_url = $_POST[ $this->opt_name ];
            update_option( $this->opt_name, $this->juxta_url );
            $saved = TRUE;
        } 

        // show the settings form:
?>
<?php screen_icon(); ?>
<h2>Juxta Commons Sharing</h2>
<div class="wrap">
    <br/>
    <h3>Usage</h3>
    <hr />
    <p>To embed shared Juxta Commons resources in your posts, use the following command:<br/>
       <b>[jx Gtx6ei]</b> - where 'Gtx6ei' is the public share key generated by Juxta Commons.
    </p>
    <br/>
    <h3>Settings</h3>
    <hr />
    <form name="juxta-settings" method="post" action="">
        <input type="hidden" name="submitted" value="y" /> 
        <p>Juxta Commons URL: 
            <input type="text" name="<?php echo $this->opt_name; ?>" value="<?php echo $this->juxta_url; ?>" size="80">
        </p>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
            <?php if ($saved==TRUE) echo("<span style='margin-left: 10px;font-style:italic;color:#aaa'>Juxta Commons Sharing settings have been saved</span>"); ?>
        </p>
    </form>
</div>

<?php
    }
}

?>
