<div class="hbs_form_container">
    <div class="hbs_link_options">
        <p class="hbs_text_italic">Enter the destination URL </p>
        <div class="hbs_form">
            <form>
                <div class="hbs_opts hbs_block">
                    <label class="hbs_label" for="url">
                        <span class="hbs_label_text">URL</span>
                        <input type="text" class="hbs_input" id="hbs_url">
                    </label>

                    <label class="hbs_label" for="link-text">
                        <span class="hbs_label_text">Link Text</span>
                        <input type="text" class="hbs_input" id="hbs_link_text">
                    </label>

                    <label class="hbs_label" for="hbs_link_check">
                        <input type="checkbox" id="hbs_link_check"> Open link in a new tab
                    </label>
                </div>

                <div class="hbs_search">
                    <p class="hbs_text_italic hbs_text">Or link to existing content</p>
                    <label class="hbs_label" for="hbs_search">
                        <span class="hbs_label_text">Search</span>
                        <input type="text" class="hbs_input" id="hbs_search">
                    </label>
                </div>
            </form>
        </div>
    </div>

    <div class="hbs_query_block">
        <div class="hbs_query_results">
            <table id="hbs_blog_list">
                <?php
                $res = $GLOBALS['results'];
                if ($res):

                    if (count($res) > 0): ?>
                        <?php foreach ($res as $blog_post): ?>
                            <tr class="hbs_alternate" data-url="<?php echo $blog_post['url'] ?>">
                                <td align="left" class="item_title"><?php echo $blog_post['title'] ?></td>
                                <td align="right" class="item_type">
                                    <?php 
                                        if($blog_post['type'] == 'BLOG_POST'){
                                            $type = 'Blog Post';
                                        } elseif($blog_post['type'] == 'LANDING_PAGE'){
                                            $type = 'Landing Page';
                                        } elseif($blog_post['type'] == 'SITE_PAGE'){
                                            $type = 'Site Page';
                                        }
                                        echo $type; 
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="hbs_alternate" data-url="">
                            <td><p class="hbs_text_italic">No links found in your HubSpot portal.</p></td>
                        </tr>
                <?php endif; ?>
            <?php endif; ?>
            </table>
        </div>
    </div>

</div>
<script type="text/javascript">
    var h2wSelectedText = '';
    jQuery(function ($) {
        h2wSelectedText = tinyMCE.activeEditor.selection.getContent({format: 'text'});
        $("#hbs_link_text").val(h2wSelectedText);

        // get url, text and show in an input field.
        $(document).on('click', '.hbs_alternate', function () {
            var h2wSelectedUrl = $(this).data('url');
            $("#hbs_url").val(h2wSelectedUrl);
            if (h2wSelectedText == '') {
                $("#hbs_link_text").val($(this).children('td').text());
            }
        });

        // search page or post from list
        $(document).on('keyup', '#hbs_search', function () {
            filter(this);
        });

        // filter from list
        function filter(element) {
            var value = $(element).val().toLowerCase();
            $("#hbs_blog_list > tbody > tr").each(function () {
                if ($(this).children().text().toLowerCase().indexOf(value) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });
</script>