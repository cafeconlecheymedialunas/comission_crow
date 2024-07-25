<?php
$current_user = wp_get_current_user();

$key = in_array("commercial_agent", $current_user->roles) ? "commercial_agent" : "company";
$dynamic_text = in_array("commercial_agent", $current_user->roles) ? "Sended" : "Received";
$routes = [
    "profile_company" => "dashboard/company/profile/",
    "profile_commercial_agent" => "dashboard/commercial-agent/profile/",
    "opportunity_create" => "dashboard/company/opportunity/create",
    "opportunity_list" => "dashboard/company/opportunity/list",
    "chat_commercial_agent" => "dashboard/commercial-agent/chat/",
    "chat_company" => "dashboard/company/chat/",
    "agreement_company_all" => "dashboard/company/agreement/all",
    "agreement_company_ongoing" => "dashboard/company/agreement/ongoing",
    "agreement_company_received" => "dashboard/company/agreement/received",
    "agreement_company_requested" => "dashboard/company/agreement/requested",
    "agreement_commercial_agent_all" => "dashboard/commercial-agent/agreement/all",
    "agreement_commercial_agent_ongoing" => "dashboard/commercial-agent/agreement/ongoing",
    "agreement_commercial_agent_received" => "dashboard/commercial-agent/agreement/received",
    "agreement_commercial_agent_requested" => "dashboard/commercial-agent/agreement/requested",
    "payments_company" => "dashboard/company/payments/",
    "payments_commercial_agent" => "dashboard/commercial-agent/payments/",
    "disputes_company" => "dashboard/company/disputes/",
    "disputes_commercial_agent" => "dashboard/commercial-agent/disputes/",
    "commission_company" => "dashboard/company/commission",
    "commission_commercial_agent" => "dashboard/commercial-agent/commission",
    "reviews_company" => "dashboard/company/reviews/",
    "reviews_commercial_agent" => "dashboard/commercial-agent/reviews/",
];

?>

<div class="user-menu card">
    <ul class="list-unstyled">
        <li>
            <a href="<?php echo esc_url(home_url()); ?>"><i class="fa fa-fw fa-home"></i>Dashboard</a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["profile_" . $key])); ?>"><i class="fa fa-fw fa-user"></i>Profile</a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["chat_" . $key])); ?>"><i class="fa fa-fw fa-comments-alt"></i>Messages</a>
        </li>
        <?php if (in_array("company", $current_user->roles) || in_array("administrator", $current_user->roles)): ?>
        <li class="dropdown">
            <i class="fa fa-fw fa-tasks-alt"></i>Opportunities
            <ul>
                <li><a href="<?php echo esc_url(home_url($routes["opportunity_create"])); ?>">Create Opportunity</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["opportunity_list"])); ?>">View Opportunities</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <li class="dropdown">
            <i class="fa fa-fw fa-thumbtack"></i>Proposal Agreements
            <ul>
                <li><a href="<?php echo esc_url(home_url($routes["agreement_" . $key . "_all"])); ?>">All proposal agreements</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["agreement_" . $key . "_ongoing"])); ?>">Ongoing proposal agreements</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["agreement_" . $key . "_requested"])); ?>">Requested proposal agreements</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["agreement_" . $key . "_received"])); ?>">Received proposal agreements</a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["payments_" . $key])); ?>"><i class="fa fa-fw fa-university"></i>Payments</a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["disputes_" . $key])); ?>"><i class="fa fa-fw fa-university"></i>Disputes</a>
        </li>
      
      
        <li>
            <a href="<?php echo esc_url(home_url($routes["commission_" . $key])); ?>"><i class="fa fa-fw fa-university"></i><?php echo $dynamic_text;?> Commission Request</a>
        </li>
        
        <?php if (in_array("commercial_agent", $current_user->roles) || in_array("administrator", $current_user->roles)): ?>
        <li>
            <a href="<?php echo esc_url(home_url($routes["reviews_" . $key])); ?>"><i class="fa fa-fw fa-university"></i>Reviews</a>
        </li>
        <?php endif; ?>
    </ul>
</div>
