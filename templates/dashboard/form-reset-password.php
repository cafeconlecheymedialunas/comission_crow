<h1 class="site-title"><?php _e('Reset Password'); ?></h1>
        <div id="reset_errors"></div>
        <form id="reset_form" class="form">
            <fieldset>
                <p>
                    <input name="user_email" placeholder="Email" id="user_email" class="form-control" type="email"/>
                </p>
                <p>
                    <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
                    <input type="hidden" name="security" value="<?php echo wp_create_nonce('reset-nonce'); ?>"/>
                    <button type="submit"><?php _e('Save'); ?></button>
                </p>
            </fieldset>
        </form>