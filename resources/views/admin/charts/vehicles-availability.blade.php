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
            <b>Vehicle Availability Overview</b>
        </h3>
        <div>
            <a href="{{ admin_url('vehicles') }}" class="btn btn-sm btn-primary mt-md-4 mt-4">
                View All Vehicles
            </a>
        </div>
    </div>

    <div class="card-body py-2 py-md-3">
        <canvas id="vehiclesAvailability" style="width: 100%;"></canvas>
    </div>
</div>

<script>
    $(function() {
        const chartColors = {
            red: 'rgb(255, 99, 132)',
            green: '#277C61'
        };

        const chartData = {
            labels: JSON.parse('<?php echo json_encode($labels); ?>'),
            datasets: [{
                label: 'Vehicle Availability',
                backgroundColor: [chartColors.red, chartColors.green],
                data: [
                    {{ $vehicles_out_count }},
                    {{ $vehicles_in_count }}
                ],
            }]
        };

        const ctx = document.getElementById('vehiclesAvailability').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Vehicle Availability',
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
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = chartData.labels[tooltipItem.dataIndex];
                                const value = chartData.datasets[0].data[tooltipItem.dataIndex];
                                return `${label}: ${value}`;
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
            },
        });
    });
</script>
