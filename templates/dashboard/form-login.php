<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link active" id="simple-tab-0" data-bs-toggle="tab" href="#simple-tabpanel-0" role="tab" aria-controls="simple-tabpanel-0" aria-selected="true">Login</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="simple-tab-1" data-bs-toggle="tab" href="#simple-tabpanel-1" role="tab" aria-controls="simple-tabpanel-1" aria-selected="false">Register as a Agent</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link" id="simple-tab-2" data-bs-toggle="tab" href="#simple-tabpanel-2" role="tab" aria-controls="simple-tabpanel-2" aria-selected="false">Register a Company</a>
  </li>
</ul>

<div class="tab-content pt-2" id="tab-content">
  <!-- Tab 1 Content -->
  <div class="tab-pane active" id="simple-tabpanel-0" role="tabpanel" aria-labelledby="simple-tab-0">
    <h1 class="site-title">Login</h1>
    <div id="login_errors"></div>
    <form id="login_form" class="form">
      <div class="mb-3 form-floating">
        <input type="email" class="form-control" id="user_login" name="user_login" placeholder="Email">
        <label for="user_login">Email</label>
        <div class="error-message"></div>
      </div>
      <div class="mb-3 form-floating">
        <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="Password">
        <label for="user_pass">Password</label>
        <div class="error-message"></div>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="true">
        <label class="form-check-label" for="remember_me">Remember me</label>
        <div class="error-message"></div>
      </div>
      <input type="hidden" name="security" value="<?php echo wp_create_nonce('login-nonce'); ?>"/>
      <button type="submit" class="btn btn-primary">Login</button>
      <p class="another-pages mt-2">
        <a href="?action=password_reset">Lost your password?</a>
      </p>
    </form>
  </div>

  <!-- Tab 2 Content -->
  <div class="tab-pane" id="simple-tabpanel-1" role="tabpanel" aria-labelledby="simple-tab-1">
    <h2 class="site-title" id="registration-title">Register as a Agent</h2>
    <div id="registration_errors"></div>
    <form id="registration-agent-form">
      <div class="row gx-1">
        <div class="col-md-6 mb-3 form-floating">
          <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name">
          <label for="first_name">First Name</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
          <label for="last_name">Last Name</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Email">
          <label for="user_email">Email</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="Password">
          <label for="user_pass">Password</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="password" class="form-control" id="user_pass_confirm" name="user_pass_confirm" placeholder="Repeat Password">
          <label for="user_pass_confirm">Repeat Password</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3" id="company_name_container">
          <div class="form-floating">
            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name">
            <label for="company_name">Company Name</label>
            <div class="error-message"></div>
          </div>
        </div>
      </div>
      <div class="form-check mb-3">
        <input type="radio" class="form-check-input" id="terms_and_conditions" name="terms_and_conditions" value="yes">
        <label class="form-check-label" for="terms_and_conditions">
          Accept <a href="<?php echo home_url('/terms-and-conditions'); ?>">Terms and Conditions</a>
        </label>
        <div class="error-message"></div>
      </div>
      <button type="submit" class="btn btn-primary">Save</button>
      <input type="hidden" name="role" value="commercial_agent">
      <input type="hidden" name="security" value="<?php echo wp_create_nonce('register-nonce'); ?>">
    </form>
  </div>

  <!-- Tab 3 Content -->
  <div class="tab-pane" id="simple-tabpanel-2" role="tabpanel" aria-labelledby="simple-tab-2">
    <h1 class="site-title" id="registration-title">Register as a Company</h1>
    <div id="registration_errors"></div>
    <form id="registration-company-form">
      <div class="row gx-1">
        <div class="col-md-6 mb-3 form-floating">
          <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name">
          <label for="first_name">First Name</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
          <label for="last_name">Last Name</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Email">
          <label for="user_email">Email</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="Password">
          <label for="user_pass">Password</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3 form-floating">
          <input type="password" class="form-control" id="user_pass_confirm" name="user_pass_confirm" placeholder="Repeat Password">
          <label for="user_pass_confirm">Repeat Password</label>
          <div class="error-message"></div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="form-floating">
            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name">
            <label for="company_name">Company Name</label>
            <div class="error-message"></div>
          </div>
        </div>
      </div>
      <div class="form-check mb-3">
        <input type="radio" class="form-check-input" id="terms_and_conditions" name="terms_and_conditions" value="yes">
        <label class="form-check-label" for="terms_and_conditions">
          Accept <a href="<?php echo home_url('/terms-and-conditions'); ?>">Terms and Conditions</a>
        </label>
        <div class="error-message"></div>
      </div>
      <button type="submit" class="btn btn-primary">Save</button>
      <input type="hidden" name="role" value="company">
      <input type="hidden" name="security" value="<?php echo wp_create_nonce('register-nonce'); ?>">
    </form>
  </div>
</div>
