<?php
/**
 * Created by PhpStorm.
 * User: oculus
 * Date: 2/5/2018
 * Time: 9:46 AM
 */
$id         =0;
global $wpdb;

?>

<div class="wrap">
    <?php settings_errors(); ?>
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2>Manage Settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'bp_options_group' ); ?>
        <table cellspacing="0" class="wp-list-table widefat fixed posts">
            <tbody>
                <tr>
                    <td width="20%">Slider Heading</td>
                    <td>
                        <input class="large" type="text" id="<?php echo $this->sliderTextHeading ?>" name="<?php echo $this->sliderTextHeading; ?>" value="<?php echo get_option($this->sliderTextHeading); ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="20%">Slider Description</td>
                    <td>
                        <textarea class="medium required"  id="<?php echo $this->sliderTextDescription ?>" name="<?php echo $this->sliderTextDescription ?>"><?php echo get_option($this->sliderTextDescription); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><?php  submit_button(); ?></td>
                </tr>
            </tbody>
        </table>

    </form>
</div>

