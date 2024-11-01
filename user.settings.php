<?php

class TMA_Signature_UserSettings
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
        add_action('personal_options', array( $this, 'add_profile_settings' ));
        add_action('personal_options_update', array( $this, 'tma_signature_profile_update'));

        add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_resources') );
    }
    function load_custom_wp_admin_resources() {
        wp_register_script('ace-editor', plugins_url('assets/js/ace/ace.js', __FILE__), null,'1.2.9', true);
        wp_enqueue_script('ace-editor');     
    }

    /**
     * Add options page
     */
    public function add_profile_settings($user)
    {
        ?>
        <tr>
            <th scope="row"><?php echo __("Signatur", "tma-signature") ?></th>
            <td>
                <style>
                    #tma-signature-ace {
                        height: 200px;
                        width: 100%;
                    }
                </style>
                <label for="tma_signature">
                    <?php
                    printf(
                        '<textarea id="tma_signature" name="tma_signature">%s</textarea>',
                        isset( $user->tma_signature ) ? esc_attr( $user->tma_signature) : ''
                    );
                    ?>
                    <div id="tma-signature-ace"/>
                    Add a signature to your posts!
                </label>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var editor = ace.edit("tma-signature-ace");
                editor.setTheme("ace/theme/solarized_light");
                editor.getSession().setMode("ace/mode/html");
                var textarea = document.querySelector('textarea[id="tma_signature"]');
                textarea.style.display = 'none';
                editor.getSession().setValue(textarea.value);
                editor.getSession().on('change', function(){
                    textarea.value = editor.getSession().getValue();
                });
            });
        </script>
            </td>
        </tr>
        <?php   
    }

   // Handle data that's posted and sanitize before saving it
    function tma_signature_profile_update( $user_id ) {
        $tma_signature = ( isset($_POST['tma_signature']) ? $_POST['tma_signature'] : "" );
        
        $tma_signature = wp_kses_post($tma_signature);
        
        update_usermeta( $user_id, 'tma_signature', $tma_signature );
    }
}

if( is_admin() ) {
    $tma_user_settings = new TMA_Signature_UserSettings();
}

