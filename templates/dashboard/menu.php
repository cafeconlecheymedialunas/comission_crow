<div class="user-menu card">
    <ul class="list-unstyled">
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('dashboard')); ?>"><i class="fa fa-fw fa-home"></i>Dashboard</a>
        </li>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('profile')); ?>"><i class="fa fa-fw fa-user"></i>Profile</a>
        </li>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('chat')); ?>"><i class="fa fa-fw fa-comments"></i>Messages</a>
        </li>
        <?php if (in_array("commercial_agent", $current_user->roles) ): ?>
        <li>
            <a href="<?php echo esc_url(home_url('/find-opportunities')); ?>"><i class="fa-solid fa-magnifying-glass-dollar"></i>Find Opportunities</a>
        </li>
        <?php endif; ?>
        <?php if (in_array("company", $current_user->roles) ): ?>
        <li>
            <a href="<?php echo esc_url(home_url('/find-agents')); ?>"><i class="fa-brands fa-searchengin"></i>Find Agents</a>
        </li>
        <?php endif; ?>
        <?php if (in_array("company", $current_user->roles) ): ?>
        <li class="nav-item">
            <a class="" data-bs-toggle="collapse" href="#collapseopportunity" role="button" aria-expanded="false" aria-controls="collapseopportunity">
                <i class="fa fa-fw fa-briefcase"></i>
                <span class="flex-fill">Opportunities</span>
                <i class="fa fa-fw fa-caret-down float-end caret-collapse float-left"></i>
            </a>
            <ul class="collapse" id="collapseopportunity">
                <li><a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('opportunity_create')); ?>"><i class="fa fa-fw fa-plus"></i>Create</a></li>
                <li><a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('opportunity_list')); ?>"><i class="fa fa-fw fa-list"></i>All</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown">
            <a class="" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                <i class="fa fa-fw fa-file-contract "></i><span class=""><?php echo in_array("company", $current_user->roles)?"My Agents":"My Companies";?></span>
                <i class="fa fa-fw fa-caret-down caret-collapse float-left"></i>
            </a>
            <ul class="collapse" id="collapseExample">
                <li class="dropdown-item"><a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('contract_all')); ?>"><i class="fa fa-fw fa-file-alt"></i>All</a></li>
                <li class="dropdown-item"><a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('contract_ongoing')); ?>"><i class="fa fa-fw fa-spinner"></i>Ongoing</a></li>
                <li class="dropdown-item"><a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('contract_requested')); ?>"><i class="fa fa-fw fa-paper-plane"></i>Requested</a></li>
                <li class="dropdown-item"><a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('contract_received')); ?>"><i class="fa fa-fw fa-inbox"></i>Received</a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('commission')); ?>"><i class="fa fa-fw fa-percent"></i> Commission Request</a>
        </li>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('disputes')); ?>"><i class="fa-solid fa-scale-balanced"></i>Disputes</a>
        </li>
        <?php if (in_array("commercial_agent", $current_user->roles)): ?>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('deposit_list')); ?>"><i class="fa-solid fa-money-check-dollar"></i>Payouts</a>
        </li>
        <?php endif; ?>
        <?php if (in_array("company", $current_user->roles)): ?>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('payment_list')); ?>"><i class="fa-solid fa-money-check-dollar"></i>Payments</a>
        </li>
        <?php endif; ?>
        <?php if (in_array("commercial_agent", $current_user->roles)): ?>
        <li>
            <a href="<?php echo esc_url($dasboard->get_role_url_link_dashboard_page('reviews')); ?>"><i class="fa fa-fw fa-star"></i>Reviews</a>
        </li>
        <?php endif; ?>
    </ul>
</div>
