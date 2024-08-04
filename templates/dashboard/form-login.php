<h1 class="site-title"><?php _e('Login'); ?></h1>
        <div id="login_errors"></div>
        <form id="login_form" class="form">
            <fieldset>
                <p>
                    <input name="user_login" placeholder="Email" id="user_login" class="form-control" type="text"/>
                </p>
                <p>
                    <input name="user_pass" placeholder="Password" id="user_pass" class="form-control" type="password"/>
                </p>
                <p>
                    <input type="checkbox" name="remember_me" id="remember_me" value="true"/>
                    <label for="remember_me"><?php _e('Remember me'); ?></label>
                </p>
                <p>
                    <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
                    <input type="hidden" name="security" value="<?php echo wp_create_nonce('login-nonce'); ?>"/>
                    <button id="login_submit" type="submit"><?php _e('Login'); ?></button>
                </p>
                <p class="another-pages">
                    <a href="<?php echo esc_url(home_url("/auth?action=register&role=commercial_agent")); ?>"><?php _e('Register as a Agent'); ?></a> | 
                    <a href="<?php echo esc_url(home_url("/auth?action=password_reset")); ?>"><?php _e('Lost your password?'); ?></a> | 
                    <a href="<?php echo esc_url(home_url("/auth?action=register&role=company")); ?>"><?php _e('Register your company'); ?></a>
                </p>
            </fieldset>
        </form>