<?php
$commercial_agent = CommercialAgent::get_instance();
$commercial_agent_post = $commercial_agent->get_commercial_agent();

$company = Company::get_instance();
$company_post = $company->get_company();

$key = $commercial_agent_post ? "commercial_agent" : "company";


$deal_requested_text = $commercial_agent_post && !$company_post ? "Sended" : "Received";


$routes = [
    "profile_company" => "dashboard/company/profile/",
    "profile_commercial_agent" => "dashboard/company/profile/",
    "opportunity_create" => "dashboard/company/opportunity/create",
    "opportunity_list" => "dashboard/company/opportunity/list",
    "opportunity_edit" => "dashboard/company/opportunity/edit",
    "chat_commercial_agent" => "dashboard/company/chat/",
    "chat_company" => "dashboard/company/chat/",
    "deal_company_accepted" => "dashboard/company/deal/accepted",
    "deal_company_requested" => "dashboard/company/deal/requested",
    "deal_company_refused" => "dashboard/company/deal/refused",
    "deal_company_finished" => "dashboard/company/deal/finished",
	"deal_company_all" => "dashboard/commercial-agent/deal/all",
	"deal_commercial_agent_accepted" => "dashboard/commercial-agent/deal/accepted",
    "deal_commercial_agent_requested" => "dashboard/commercial-agent/deal/requested",
    "deal_commercial_agent_refused" => "dashboard/commercial-agent/deal/refused",
    "deal_commercial_agent_finished" => "dashboard/commercial.agent/deal/finished",
	"deal_commercial_agent_all" => "dashboard/commercial-agent/deal/all",
    "commercial_agent_company_accepted" => "dashboard/company/commercial-agent/accepted",
    "commercial_agent_company_requested" => "dashboard/company/commercial-agent/requested",
    "commercial_agent_company_refused" => "dashboard/company/commercial-agent/refused",
    "commercial_agent_company_finished" => "dashboard/company/commercial-agent/finished",
    "payments_company" => "dashboard/company/payments/",
    "payments_commercial_agent" => "dashboard/company/payments/",
    "disputes_company" => "dashboard/company/disputes/",
    "disputes_commercial_agent" => "dashboard/company/disputes/",
    "requested_commission" => "dashboard/company/requested-commission/",
    "request_commission" => "dashboard/company/request-commission/",
    "reviews_company" => "dashboard/company/reviews/",
    "reviews_commercial_agent" => "dashboard/company/reviews/",
];

?>

<div class="user-menu card">
    <ul class="list-unstyled">
        <li>
            <a href="<?php echo esc_url(home_url()); ?>"><i class="fal fa-fw fa-home"></i>Dashboard</a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["profile_" . $key])); ?>"><i class="fal fa-fw fa-user"></i>Profile</a>
        </li>
        <li><a href="<?php echo esc_url(home_url($routes["chat_" . $key])); ?>"><i class="fal fa-fw fa-comments-alt"></i>Messages</a></li>
        <?php if ($company_post): ?>
        <li class="dropdown">
            <i class="fal fa-fw fa-tasks-alt"></i>Opportunities
            <ul>
                <li><a href="<?php echo esc_url(home_url($routes["opportunity_create"])); ?>">Create Opportunity</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["opportunity_list"])); ?>">View Opportunities</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <li class="dropdown">
            <i class="fal fa-fw fa-thumbtack"></i>Deals
            <ul>
				<li><a href="<?php echo esc_url(home_url($routes["deal_" . $key . "_all"])); ?>">All Deals</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["deal_" . $key . "_accepted"])); ?>">Accepted Deals</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["deal_" . $key . "_refused"])); ?>">Refused Deals</a></li>
                <li><a href="<?php echo esc_url(home_url($routes["deal_" . $key . "_requested"])); ?>"><?php echo $deal_requested_text;?> Deals</a></li>
				<li><a href="<?php echo esc_url(home_url($routes["deal_" . $key . "_finished"])); ?>">Finished Deals</a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["payments_" . $key])); ?>"><i class="fal fa-fw fa-university"></i>Payments</a>
        </li>
        <li>
            <a href="<?php echo esc_url(home_url($routes["disputes_" . $key])); ?>"><i class="fal fa-fw fa-university"></i>Disputes</a>
        </li>
        <?php if ($company_post): ?>
        <li>
            <a href="<?php echo esc_url(home_url($routes["requested_commission"])); ?>"><i class="fal fa-fw fa-university"></i>Requested Commissions</a>
        </li>
        <?php endif; ?>
        <?php if ($commercial_agent_post): ?>
        <li>
            <a href="<?php echo esc_url(home_url($routes["request_commission"])); ?>"><i class="fal fa-fw fa-university"></i>Request a Commission</a>
        </li>
        <?php endif; ?>
        <?php if ($commercial_agent_post): ?>
        <li>
            <a href="<?php echo esc_url(home_url($routes["reviews_" . $key])); ?>"><i class="fal fa-fw fa-university"></i>Reviews</a>
        </li>
        <?php endif; ?>
    </ul>
</div>
