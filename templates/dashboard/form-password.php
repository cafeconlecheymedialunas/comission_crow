<?php
$current_user = wp_get_current_user();
?>

<form id="update-user-data" enctype="multipart/form-data">
    <h4>User</h4>
    <div class="col-md-12">
        <label for="first_name" class="form-label">First Name:</label>
        <input 
            type="text" 
            name="first_name" 
            class="form-control" 
            value="<?php echo esc_attr($current_user->first_name); ?>" 
            placeholder="First Name"
        >
        <div class="error-message"></div>
    </div>
    <div class="col-md-12">
        <label for="last_name" class="form-label">Last Name:</label>
        <input 
            type="text" 
            name="last_name" 
            class="form-control" 
            value="<?php echo esc_attr($current_user->last_name); ?>" 
            placeholder="Last Name"
        >
        <div class="error-message"></div>
    </div>
    <div class="col-md-12">
        <label for="user_email" class="form-label">Email:</label>
        <input 
            type="email" 
            name="user_email" 
            class="form-control" 
            value="<?php echo esc_attr($current_user->data->user_email); ?>" 
            placeholder="User Email"
        >
        <div class="error-message"></div>
    </div>
    <div class="col-md-12">
        <label for="password" class="form-label">Password:</label>
        <input 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder="Password"
        >
        <span class="description">Leave blank if you do not want to change the password.<span/>
        <div class="error-message"></div>
    </div>
    <input type="hidden" name="security" value="<?php echo wp_create_nonce('update-userdata'); ?>"/>
    <input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>">
    <div class="alert alert-danger general-errors" role="alert" style="display:none;"></div>
   
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
    
</form>
