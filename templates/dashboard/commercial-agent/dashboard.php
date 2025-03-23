<?php
function get_user_income_by_month($user_id)
{
    // Obtener los commission requests del usuario
    $commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();

    // Extraer los IDs de los commission requests
    $commission_request_ids = array_map(function ($post) {
        return $post->ID;
    }, $commission_requests);

    // Obtener todos los pagos del usuario actual
    $payments = get_posts([
        'post_type' => 'payment',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'user', // Clave para el usuario en los metadatos del pago
                'value' => $user_id,
                'compare' => '='
            ]
        ]
    ]);

    // Array para almacenar los ingresos por mes
    $income_by_month = array_fill(1, 12, 0);

    // Recorrer los pagos y agruparlos por mes
    foreach ($payments as $payment) {
        $commission_request_id = carbon_get_post_meta($payment->ID, 'commission_request_id');

        // Verifica si el commission_request_id está en la lista de IDs de commission_requests del usuario
        if (in_array($commission_request_id, $commission_request_ids)) {
            $total_paid = carbon_get_post_meta($payment->ID, 'total_paid');
            $payment_date = carbon_get_post_meta($payment->ID, 'date');
            if ($payment_date) {
                $timestamp = strtotime($payment_date);
                $month = (int)date('n', $timestamp);
                $income_by_month[$month] += floatval($total_paid);
            }
        }
    }

    return $income_by_month;
}

$current_user_id = get_current_user_id();
$income_by_month = get_user_income_by_month($current_user_id);

// Convertir el array de ingresos por mes a una cadena de datos para JavaScript
$income_data = json_encode(array_values($income_by_month));

// Obtener commission requests y contratos
$commission_requests = ProfileUser::get_instance()->get_commission_requests_for_user();
$payments = get_posts([
    'post_type' => 'payment',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'user',
            'value' => $current_user_id,
            'compare' => '='
        ]
    ]
]);

$ongoing_contracts = ProfileUser::get_instance()->get_contracts(["accepted", "finishing"]);
$received_contracts = ProfileUser::get_instance()->get_contracts([], "received");
$sended_contracts = ProfileUser::get_instance()->get_contracts([], "requested");

// Inicializar arrays para pagos por compañía y títulos de compañías
$payments_by_company = [];
$company_titles = [];

// Iterar sobre los pagos
foreach ($payments as $payment) {
    $commission_request_id = carbon_get_post_meta($payment->ID, 'commission_request_id');
    $contract_id = carbon_get_post_meta($commission_request_id, 'contract');
    $company_id = carbon_get_post_meta($contract_id, "company");
    $total_agent = carbon_get_post_meta($payment->ID, 'total_agent');

    // Obtener el título de la compañía
    $company_title = get_the_title($company_id);

    // Verificar si la compañía ya existe en el array
    if (!isset($payments_by_company[$company_id])) {
        $payments_by_company[$company_id] = 0;
        $company_titles[$company_id] = $company_title;
    }
    
    // Acumulamos el total por compañía
    $payments_by_company[$company_id] += $total_agent;
}

// Convertir los datos a JSON
$company_titles_json = json_encode(array_values($company_titles));
$payments_by_company_json = json_encode(array_values($payments_by_company));
?>

<div class="card mb-4">
    <h2 class="mb-0"><?php echo __("Commercial Agent Dashboard"); ?></h2>
</div>
<div class="row stats mb-4">
    <?php if ($ongoing_contracts): ?>
        <div class="col-lg-4">
            <a href="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/frontend-dashboard/?fed=services"
               style="background:rgb(103 135 254 / 10%);" class="dashboard-stats-item">
                <h2 class="fs-1"><?php echo count($ongoing_contracts); ?></h2>
                <h5>Ongoing Contracts</h5>
            </a>
        </div>
    <?php endif; ?>
    <?php if ($received_contracts): ?>
        <div class="col-lg-4">
            <a href="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/frontend-dashboard/?fed=ongoing-services"
               style="background:rgb(255 0 122 / 20%);" class="dashboard-stats-item">
                <h2 class="fs-1"><?php echo count($received_contracts); ?></h2>
                <h5>Received Contracts</h5>
            </a>
        </div>
    <?php endif; ?>
    <?php if ($sended_contracts): ?>
        <div class="col-lg-4">
            <a href="https://nexfyapp-cp167.wordpresstemporal.com/subcarpeta/frontend-dashboard/?fed=completed-services"
               style="background:rgb(255 187 0 / 20%);" class="dashboard-stats-item">
                <h2 class="fs-1"><?php echo count($sended_contracts); ?></h2>
                <h5>Sent Contracts</h5>
            </a>
        </div>
    <?php endif; ?>
</div>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div>
                <canvas id="contractsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <canvas id="paymentsChart"></canvas>
        </div>
    </div>
</div>
<div class="card mb-4">
    <canvas id="walletBalanceChart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const contractsCtx = document.getElementById('contractsChart').getContext('2d');
        new Chart(contractsCtx, {
            type: 'bar',
            data: {
                labels: ['Ongoing', 'Received', 'Sent'],
                datasets: [{
                    label: 'Contracts',
                    data: [<?php echo count($ongoing_contracts); ?>, <?php echo count($received_contracts); ?>, <?php echo count($sended_contracts); ?>],
                    backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
        new Chart(paymentsCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo $company_titles_json; ?>, // Asegúrate de que esto sea un array JSON válido
                datasets: [{
                    label: 'Total Paid by Company',
                    data: <?php echo $payments_by_company_json; ?>, // Asegúrate de que esto sea un array JSON válido
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return ' $' + tooltipItem.raw.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        const walletBalanceCtx = document.getElementById('walletBalanceChart').getContext('2d');
        new Chart(walletBalanceCtx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Income by Month',
                    data: <?php echo $income_data; ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true
                }]
            },
            options: {
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
