<h1 class="site-title"><?php echo $title; ?></h1>
        <div id="registration_errors"></div>
        <form id="registration_form">
            <div class="row gx-1">
                <div class="col-md-6 mb-3">
                    <input name="first_name" placeholder="First Name" id="first_name" class="form-control" type="text" />
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <input name="last_name" placeholder="Last Name" id="last_name" class="form-control" type="text" />
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <input name="user_email" placeholder="Email" id="user_email" class="form-control" type="email" />
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <input name="user_pass" placeholder="Password" id="user_pass" class="form-control" type="password" />
                    <div class="error-message"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <input name="user_pass_confirm" placeholder="Repeat Password" id="user_pass_confirm" class="form-control" type="password" />
                    <div class="error-message"></div>
                </div>
                <?php if ($role === "company") : ?>
                    <div class="col-md-6 mb-3">
                        <input name="company_name" placeholder="Company Name" id="company_name" class="form-control" type="text" />
                        <div class="error-message"></div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
            <p>
                <button type="submit"><?php _e('Save'); ?></button>
            </p>
            <?php echo $role;?>
            
            <input type="hidden" name="role" value="<?php echo $role; ?>" />
            <input type="hidden" name="security" value="<?php echo wp_create_nonce('register-nonce'); ?>" />
            <p class="another-pages">Already have an account? <a href="<?php echo esc_url(home_url("/auth?action=login")); ?>">Login</a></p>
        </form>