<?php

class TMA_Signature_SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_resources') );
    }
    function load_custom_wp_admin_resources() {
        wp_register_script('ace-editor', plugins_url('assets/js/ace/ace.js', __FILE__), null,'1.2.9', true);
        wp_enqueue_script('ace-editor');        

    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            __("TMA Signature", "tma-signature"), 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'tma_signature_options' );
        ?>
        <div class="wrap">
            <h1>TMA-Signature settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <style>
            #tma-signature-ace {
                height: 200px;
                width: 100%;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var editor = ace.edit("tma-signature-ace");
                editor.setTheme("ace/theme/solarized_light");
                editor.getSession().setMode("ace/mode/html");
                var textarea = document.querySelector('textarea[id="tma-signature"]');
                textarea.style.display = 'none';
                editor.getSession().setValue(textarea.value);
                editor.getSession().on('change', function(){
                    textarea.value = editor.getSession().getValue();
                });
            });
        </script>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'tma_signature_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );


        add_settings_section(
            'setting_section_id', // ID
            __("TMA Signature", "tma-signature"), // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  

        add_settings_field(
            'signatur', // ID
            __("Signature", "tma-signature"), // Title 
            array( $this, 'signature_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );       

        add_settings_field(
            'priority', // ID
            __("Priority", "tma-signature"), // Title 
            array( $this, 'priority_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );  
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['signature'] ) ) {
            $new_input['signature'] = wp_kses_post($input['signature']);
        }
        if( isset( $input['priority'] ) ) {
            $new_input['priority'] = intval($input['priority']) ;
        } else {
            $new_input['priority'] = 10;
        }
            

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }


    /** 
     * Get the settings option array and print one of its values
     */
    public function signature_callback()
    {
        printf(
            '<textarea id="tma-signature" name="tma_signature_options[signature]">%s</textarea><div id="tma-signature-ace"/>',
            isset( $this->options['signature'] ) ? esc_attr( $this->options['signature']) : ''
        );
    }

    public function priority_callback () {
        printf(
            '<input type="number" id="tma-priority" name="tma_signature_options[priority]" value="%s"/>',
            isset( $this->options['priority'] ) ? esc_attr( $this->options['priority']) : ''
        );
    }
}

if( is_admin() ) {
    $my_settings_page = new TMA_Signature_SettingsPage();
}

