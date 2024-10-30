<div class="hbs_form_container">
    <?php
    $response = $GLOBALS['response'];
    if ($response):
        $forms = json_decode($response['response'], true);
        if (isset($forms['status']) && $forms['status'] == 'error'):
            ?>
            <p class="hbs_text_italic">Can't find your form? <br /><?php echo $forms['message']; ?></p>
        <?php else: ?>
            <div class="hbs_form_title"><h3>Insert A Form</h3></div>

            <p class="hbs_text">Select a form below to add it to your post or page.</p>

            <select class="hbs_select" id="hubspotFormId" name="formName"><option>Select a Form</option>';
                <?php foreach ($forms as $form) : ?>
                    <option value="<?php echo $form['guid'] ?>" mytag="<?php echo $form['portalId'] ?>"><?php echo $form['name'] ?></option>
                <?php endforeach; ?>
            </select> 
            
        <?php endif; ?>
    <?php else: ?>
        <p class="hbs_text_italic">Can't find your form? Make sure it is published.</p>
    <?php endif; ?>
</div>
