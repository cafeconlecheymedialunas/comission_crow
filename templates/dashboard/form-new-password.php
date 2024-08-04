<h1 class="site-title"><?php _e('Set a New Password'); ?></h1>
        <div id="new_password_errors"></div>
        <form id="new_password_form" class="form">
            <fieldset>
                <p>
                    <input name="new_password" placeholder="Password" id="new_password" class="form-control" type="password"/>
                </p>
                <p>
                    <input name="confirm_password" placeholder="Confirm Password" id="confirm_password" class="form-control" type="password"/>
                </p>
                <p>
                    <input type="hidden" name="reset_key" value="<?php echo esc_attr($_GET['key']); ?>"/>
                    <input type="hidden" name="reset_login" value="<?php echo esc_attr($_GET['login']); ?>"/>
                    <input type="hidden" name="security" value="<?php echo wp_create_nonce('new-password-nonce'); ?>"/>
                    <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
                    <button type="submit"><?php _e('Save'); ?></button>
                </p>
            </fieldset>
        </form>