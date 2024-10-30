<?php
$res = $GLOBALS['files'];
?>
<div class="file_manager_modal">
    <div class="hbs_folder_container" style="overflow-y: scroll;">
        <?php if ($res): ?>
            <?php
            $response = json_decode($res['response'], true);
            if (isset($response['status']) && $response['status'] == 'error'):
                ?>
                <p class="hbs_text_italic"><?php echo $response['message']; ?></p>
            <?php else: ?>
                <ul class="hbs_folder_list">
                    <?php
                    if ($response['total_count'] > 0):
                        foreach ($response['objects'] as $key => $folder) {
                            $mil = $folder['created'];
                            $seconds = $mil / 1000;
                            $created = date('M d  Y', $seconds);
                            ?>
                            <li class="hbs_folder_list_item">
                                <a href="javascript:void(0)" data-id="<?php echo $folder['id'] ?>" data-name="<?php echo $folder['name']; ?>" class="hbs-folder">
                                    <span class="wp-menu-image dashicons-before dashicons dashicons-category"></span>
                                    <span class="hbs_folder_name"><?php echo $folder['name']; ?></span>
                                    <span class="hbs_folder_date"><?php echo $created; ?></span>
                                </a>
                            </li>

                        <?php } ?>
                    <?php else: ?>
                        <li class="hbs_folder_list_item">File not uploading yet according to this account.</li>
                    <?php endif; ?>
                </ul>

            <?php endif; ?>
        <?php else: ?>
            <p class="hbs_text_italic">Can't find? Make sure it is active.</p>
        <?php endif; ?>
    </div>
</div>