<style>
    .ext-icon {
        color: rgba(0, 0, 0, 0.5);
        margin-left: 10px;
    }

    .installed {
        color: #00a65a;
        margin-right: 10px;
    }

    .card {
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .case-item:hover {
        background-color: rgb(254, 254, 254);
    }
</style>
<div class="card mb-4 mb-md-5 border-0">
    <!--begin::Header-->
    <div class="d-flex justify-content-between px-3 px-md-4">
        <h3>
            <b>Requests Statistics - {{ count($labels) }} Days Overview</b>
        </h3>
        <div>
            <a href="{{ admin_url('all-requests') }}" class="btn btn-sm btn-primary mt-md-4 mt-4">
                View All Requests
            </a>
        </div>
    </div>

    <div class="card-body py-2 py-md-3">
        <canvas id="requestsFrequencyChart" style="width: 100%;"></canvas>
    </div>
</div>

<script>
    $(function() {
        const chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: '#277C61',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        const chartData = {
            labels: JSON.parse('<?php echo json_encode($labels); ?>'),
            datasets: [{
                    type: 'line',
                    label: 'Materials Requests',
                    backgroundColor: chartColors.red,
                    borderColor: chartColors.red,
                    data: {{ json_encode($materials) }},
                    fill: false,
                    tension: 0.4,
                },
                {
                    type: 'line',
                    label: 'Personnel Requests',
                    backgroundColor: chartColors.blue,
                    borderColor: chartColors.blue,
                    data: {{ json_encode($personels) }},
                    fill: false,
                    tension: 0.4,
                },
                {
                    type: 'bar',
                    label: 'Vehicle Requests',
                    backgroundColor: chartColors.green,
                    data: {{ json_encode($vehicles) }},
                    borderRadius: 5,
                    barPercentage: 0.6,
                },
            ]
        };

        const ctx = document.getElementById('requestsFrequencyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Requests Frequency Over Time',
                        font: {
                            size: 18,
                            weight: 'bold',
                        },
                        padding: {
                            top: 10,
                            bottom: 20,
                        },
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`;
                            },
                        },
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                    y: {
                        grid: {
                            color: 'rgba(200, 200, 200, 0.2)',
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
            },
        });
    });
</script>
