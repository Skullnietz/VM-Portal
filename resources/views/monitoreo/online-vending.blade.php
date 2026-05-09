@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Monitoreo VM'))

@section('content_header')
    <br>
@stop

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                <div class="info-box-content">
                <span class="info-box-text">Producto + consumido</span>
                <span class="info-box-number">LENTES URVINA</span>
                </div>

                </div>

                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-industry"></i></span>
                    <div class="info-box-content">
                    <span class="info-box-text">Planta de Alto Consumo</span>
                    <span class="info-box-number">HELLA GUADALAJARA</span>
                    </div>

                    </div>

                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-power-off"></i></span>
                        <div class="info-box-content">
                        <span class="info-box-text">Vendings Activas</span>
                        <span class="info-box-number">10</span>
                        </div>

                        </div>

                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                            <span class="info-box-icon bg-dark"><i class="fas fa-boxes"></i></span>
                            <div class="info-box-content">
                            <span class="info-box-text">Articulos Consumidos</span>
                            <span class="info-box-number">1,234</span>
                            </div>

                            </div>

                            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="card card-success card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">HELLA GUADALAJARA</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">HELLA IRAPUATO</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">MABE</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                <!-- Contenido dinámico de HELLA GUADALAJARA -->
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                <!-- Contenido dinámico de HELLA IRAPUATO -->
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                                <!-- Contenido dinámico de MABE -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <div class="col-4">
                <div class="row">
                    <div class="col">
                        <div class="card">

                            <div class="card-header">

                                <h5 class="card-title">
                                    <i class="fas fa-history mr-1"></i>
                                    Consumos Recientes
                                </h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-tool dropdown-toggle"
                                            data-toggle="dropdown">
                                            <i class="fas fa-wrench"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                                            <a href="#" class="dropdown-item">Action</a>
                                            <a href="#" class="dropdown-item">Another action</a>
                                            <a href="#" class="dropdown-item">Something else here</a>
                                            <a class="dropdown-divider"></a>
                                            <a href="#" class="dropdown-item">Separated link</a>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                            </div>
                            <div class="card-body p-0 table-responsive" style="height:300px">
                                <table class="table table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px"># ID</th>
                                            <th>Producto</th>
                                            <th>VM</th>
                                            <th style="width: 40px">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>207</td>
                                            <td>GUANTE PUNTOS NEGROS</td>
                                            <td>
                                                <div>
                                                    HELLA GUADALAJARA
                                                </div>
                                            </td>
                                            <td>08/07/2024</td>
                                        </tr>
                                        <tr>
                                            <td>206</td>
                                            <td>GUANTE 4VNGR</td>
                                            <td>
                                                <div>
                                                    HELLA IRAPUATO
                                                </div>
                                            </td>
                                            <td>08/07/2024</td>
                                        </tr>
                                        <tr>
                                            <td>205</td>
                                            <td>GUANTE LATEX ROJO</td>
                                            <td>
                                                <div>
                                                    HELLA IRAPUATO
                                                </div>
                                            </td>
                                            <td>08/07/2024</td>
                                        </tr>
                                        <tr>
                                            <td>204</td>
                                            <td>LENTE URVINA</td>
                                            <td>
                                                <div>
                                                    MABE
                                                </div>
                                            </td>
                                            <td>08/07/2024</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header ui-sortable-handle" style="cursor: move;">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-1"></i>
                                    Graficas
                                </h3>
                                <div class="card-tools">
                                    <ul class="nav nav-pills ml-auto">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#revenue-chart" data-toggle="tab">Barras</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#sales-chart" data-toggle="tab">Pastel</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content p-0">

                                    <div class="chart tab-pane" id="revenue-chart"
                                        style="position: relative; height: 300px;">
                                        <div class="chartjs-size-monitor">
                                            <div class="chartjs-size-monitor-expand">
                                                <div class=""></div>
                                            </div>
                                            <div class="chartjs-size-monitor-shrink">
                                                <div class=""></div>
                                            </div>
                                        </div>
                                        <canvas id="myChart" height="300"
                                            style="height: 300px; display: block; width: 550px;"
                                            class="chartjs-render-monitor" width="550"></canvas>
                                    </div>
                                    <div class="chart tab-pane active" id="sales-chart"
                                        style="position: relative; height: 300px;">
                                        <div class="chartjs-size-monitor">
                                            <div class="chartjs-size-monitor-expand">
                                                <div class=""></div>
                                            </div>
                                            <div class="chartjs-size-monitor-shrink">
                                                <div class=""></div>
                                            </div>
                                        </div>
                                        <canvas id="pastelChart" height="300"
                                            style="height: 300px; display: block; width: 550px;"
                                            class="chartjs-render-monitor" width="550"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
    <style>
        .vending-machine {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('vendor/adminlte/dist/img/vending-machine.png') no-repeat center center;
            background-size: contain;
            z-index: 2;
        }

        .fill {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0;
            background-color: green;
            z-index: 1;
            transition: height 1s ease;
            margin-bottom: 2px
        }

        .percentage {
            margin-top: 20px;
            font-size: 24px;
        }

        .vm-container {
            position: relative;
            width: 75px;
            /* Ajusta el tamaño según tus necesidades */
            height: 99px;
            /* Ajusta el tamaño según tus necesidades */
        }
    </style>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['PLANTA1', 'P2', 'P3', 'P4', 'P5', 'P6'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
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

        const ctY = document.getElementById('pastelChart');

        new Chart(ctY, {
            type: 'pie',
            data: {
                labels: ['PLANTA1', 'P2', 'P3', 'P4', 'P5', 'P6'],
                backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
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
    </script>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <script>
        const vendingMachinesData = [
            { planta: "HELLA GUADALAJARA", id: 1, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA GUADALAJARA", id: 2, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA GUADALAJARA", id: 3, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA GUADALAJARA", id: 4, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA GUADALAJARA", id: 5, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA IRAPUATO", id: 6, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA IRAPUATO", id: 7, percentage: Math.floor(Math.random() * 101) },
            { planta: "HELLA IRAPUATO", id: 8, percentage: Math.floor(Math.random() * 101) },
            { planta: "MABE", id: 9, percentage: Math.floor(Math.random() * 101) }
        ];

        function updateVendingMachine(id, percentage) {
            const fillElement = document.getElementById(`fill-${id}`);
            const percentageElement = document.getElementById(`percentage-${id}`);

            percentageElement.textContent = percentage + '%';

            // Actualizar el color de la barra de llenado según el porcentaje
            if (percentage >= 71) {
                fillElement.style.backgroundColor = 'green';
            } else if (percentage >= 51) {
                fillElement.style.backgroundColor = 'yellow';
            } else {
                fillElement.style.backgroundColor = 'red';
            }

            // Actualizar la altura de la barra de llenado
            fillElement.style.height = percentage + '%';
        }

        function initializeVendingMachines() {
            const tabContent = {
                "HELLA GUADALAJARA": document.getElementById('custom-tabs-one-home'),
                "HELLA IRAPUATO": document.getElementById('custom-tabs-one-profile'),
                "MABE": document.getElementById('custom-tabs-one-messages')
            };

            vendingMachinesData.forEach(machine => {
                const vmContainer = document.createElement('div');
                vmContainer.classList.add('vm-container');

                const fillDiv = document.createElement('div');
                fillDiv.classList.add('fill');
                fillDiv.id = `fill-${machine.id}`;

                const vendingMachineDiv = document.createElement('div');
                vendingMachineDiv.classList.add('vending-machine');

                const percentageDiv = document.createElement('div');
                percentageDiv.classList.add('percentage');
                percentageDiv.id = `percentage-${machine.id}`;
                percentageDiv.textContent = machine.percentage + '%';

                vmContainer.appendChild(fillDiv);
                vmContainer.appendChild(vendingMachineDiv);

                const colDiv = document.createElement('div');
                colDiv.classList.add('col-3'); // Aquí se agrega la clase col-4

                const cardDiv = document.createElement('div');
                cardDiv.classList.add('card');

                const rowDiv = document.createElement('div');
                rowDiv.classList.add('row');

                const centerDiv = document.createElement('center');
                centerDiv.appendChild(vmContainer);

                const innerColDiv = document.createElement('div');
                innerColDiv.classList.add('col');
                innerColDiv.appendChild(centerDiv);

                const percentageColDiv = document.createElement('div');
                percentageColDiv.classList.add('col');
                percentageColDiv.appendChild(percentageDiv);

                rowDiv.appendChild(innerColDiv);
                rowDiv.appendChild(percentageColDiv);

                cardDiv.appendChild(rowDiv);

                colDiv.appendChild(cardDiv);

                // Agregar la columna a la pestaña correspondiente
                if (!tabContent[machine.planta].lastElementChild || tabContent[machine.planta].lastElementChild.childElementCount === 4) {
                    const newRow = document.createElement('div');
                    newRow.classList.add('row');
                    tabContent[machine.planta].appendChild(newRow);
                }

                tabContent[machine.planta].lastElementChild.appendChild(colDiv);

                updateVendingMachine(machine.id, machine.percentage);
            });
        }

        // Inicializar las máquinas expendedoras al cargar la página
        initializeVendingMachines();

        // Actualizar las máquinas expendedoras cada 5 segundos (por ejemplo)
        setInterval(() => {
            vendingMachinesData.forEach(machine => {
                machine.percentage = Math.floor(Math.random() * 101); // Simula un nuevo porcentaje
                updateVendingMachine(machine.id, machine.percentage);
            });
        }, 5000);
    </script>
@stop
