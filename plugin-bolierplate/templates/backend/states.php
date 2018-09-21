<?php
/**
 * Created by PhpStorm.
 * User: oculus
 * Date: 2/5/2018
 * Time: 4:46 PM
 */
$message    ="";
$msg_class  ="";
if(isset($_POST['update'])){
    $stateLinkArr   = $_POST['state_link'];
    $data           =array();
    foreach( $this->USAStates as $key => $value ) {
            $data[$key] = $stateLinkArr[$key];
    }
    update_option($this->stateLinkOptionName, $data);
    $msg_class="success";
    $message="Url(s) updated successfully.";
}
$results    =get_option($this->stateLinkOptionName,true);
?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2>Manage States</h2>
    <?php if(isset($message) && $message!=''){ ?>
        <div id="message" class="updated <?php echo $msg_class; ?>"><p><?php echo $message; ?></p></div>
    <?php } ?>

    <div class="fullwidth-panel">
        <form name="form" id="form" method="post" action="">
            <table cellspacing="0" class="wp-list-table widefat fixed pages">
                <thead>
                <tr>
                    <th>States</th>
                    <th>Url</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="2" class="right_align">
                        <input type="submit" name="update" id="update" class="button" value="Update">
                    </td>
                </tr>

                <?php if(count($this->USAStates)>0){
                    $count=0;
                    foreach($this->USAStates as $k=>$v) {?>
                        <tr <?php echo ($count%2==0?"class='alternate'":''); $count++; ?>>
                            <td>
                                <?php echo $v; ?>
                            </td>
                            <td>
                                <input type="url" name="state_link[<?php echo $k; ?>]" class="large" value="<?php echo @$results[$k]; ?>" />
                            </td>
                        </tr>
                    <?php }} else { ?>
                    <tr><td colspan="1" class='empty'>No Records found.</td></tr>
                <?php } ?>
                <tr>
                    <td colspan="2"  class="right_align">
                        <input type="submit" name="search" id="search" class="button" value="Update">
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th>States</th>
                    <th>Url</th>
                </tr>
                </tfoot>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">
    $jQu('#submit').on('click', function (e) {
        var form = $jQu(this);
        $jQu("#form").validate({
            rules: {
                'state_link[]': {
                    url: true
                }
            }
        });
    });
</script>