@extends('adminlte::page')

@section('usermenu_body')
<center><b>{{$Codigocliente[0]->Txt_Nombre_Planta}} | {{$Codigocliente[0]->Txt_Sitio}} | {{$Codigocliente[0]->Txt_Codigo_Cliente}}</b></center>
@stop

@section('title', __('Monitoreo VM'))

@section('content_header')
    
@stop

@section('content')

    <div class="container">
    <div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Producto más consumido</span>
                <span class="info-box-number" id="producto-mas-consumido">Cargando...</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-industry"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Área de Alto Consumo</span>
                <span class="info-box-number" id="area-alto-consumo">Cargando...</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-power-off"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Vendings Activas</span>
                <span class="info-box-number" id="vendings-activas">Cargando...</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-dark"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Artículos Consumidos</span>
                <span class="info-box-number" id="articulos-consumidos">Cargando...</span>
            </div>
        </div>
    </div>
</div>
        <div class="row">
            <div class="col-8">
                <div class="card card-tabs">
                    <div class="card-header ">
                    <div id="tabs-container"></div>
                    </div>
                    <div class="card-body">
                    
                    <div id="tabs-content" class="container">
                        
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
                                            <th style="width: 10px">N#</th>
                                            <th>Producto</th>
                                            <th>VM</th>
                                            <th style="width: 40px">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-consumptions-body">
                                        <!-- Datos dinámicos aquí -->
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
            background: url('/vendor/adminlte/dist/img/vending-machine.png') no-repeat center center;
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
        
        
        .tab {
            cursor: pointer;
            padding: 10px;
            margin-right: 5px;
            border: 1px solid #ccc;
            display: inline-block;
            background-color: #f1f1f1;
        }
        .tab.active {
            background-color: #ddd;
        }
        .vm-container.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .vm-container {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .fill {
            width: 100%;
            background-color: red;
            border-radius: 5px;
        }
        .disabled {
            opacity: 0.5;
            pointer-events: none;
        }
    
    </style>

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateInfoBoxes() {
    fetch('/vm-dash')
        .then(response => response.json())
        .then(data => {
            document.getElementById('producto-mas-consumido').innerText = data.producto_mas_consumido || 'N/A';
            document.getElementById('area-alto-consumo').innerText = data.area_alto_consumo || 'N/A';
            document.getElementById('vendings-activas').innerText = data.vendings_activas || '0';
            document.getElementById('articulos-consumidos').innerText = data.articulos_consumidos || '0';
        })
        .catch(error => console.error('Error:', error));
}

setInterval(updateInfoBoxes, 5000); // Actualizar cada 5 segundos
    </script>
    <script>
        // Función para obtener datos desde la API
        async function fetchData() {
            const response = await fetch('/vm-graphs');
            const data = await response.json();
            return data;
        }

        // Función para actualizar las gráficas
        function updateCharts(data) {
            const labels = data.map(item => item.nombre);
            const values = data.map(item => item.total_cantidad);
            const ids = data.map(item => item.id);

            const barCtx = document.getElementById('myChart').getContext('2d');
            const pieCtx = document.getElementById('pastelChart').getContext('2d');

            // Destruir las gráficas anteriores si existen
            if (window.barChart) {
                window.barChart.destroy();
            }
            if (window.pieChart) {
                window.pieChart.destroy();
            }

            window.barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Consumo de Artículos',
                        data: values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(201, 203, 207, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    onClick: (evt, item) => {
                        if (item.length > 0) {
                            const index = item[0].index;
                            const articleId = ids[index];
                            window.open(`/articulo/${articleId}`, '_blank');
                        }
                    }
                }
            });

            window.pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Consumo de Artículos',
                        data: values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(201, 203, 207, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Consumo de Artículos'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    onClick: (evt, item) => {
                        if (item.length > 0) {
                            const index = item[0].index;
                            const articleId = ids[index];
                            window.open(`/articulo/${articleId}`, '_blank');
                        }
                    }
                }
            });
        }

        // Llamar a la función fetchData y luego updateCharts
        function refreshCharts() {
            fetchData().then(data => updateCharts(data));
        }

        // Inicializar las gráficas al cargar la página
        refreshCharts();

        // Actualizar las gráficas cada minuto (60000 milisegundos)
        setInterval(refreshCharts, 60000);
    </script>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <script>
        let vendingMachinesData = [];

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

        function initializeTabs() {
            const tabsContainer = document.getElementById('tabs-container');
            const tabsContent = document.getElementById('tabs-content');

            // Limpiar los contenedores
            tabsContainer.innerHTML = '';
            tabsContent.innerHTML = '';

            // Crear pestañas y contenido para cada planta
            const plants = [...new Set(vendingMachinesData.map(machine => machine.planta))]; // Obtener plantas únicas

            plants.forEach(plant => {
                // Crear tab
                const tab = document.createElement('div');
                tab.classList.add('tab');
                tab.textContent = plant;
                tab.addEventListener('click', () => showTab(plant));
                tabsContainer.appendChild(tab);

                // Crear contenido de la tab
                const tabContent = document.createElement('div');
                tabContent.classList.add('tab-content');
                tabContent.classList.add('row');
                tabContent.id = `tab-content-${plant}`;
                tabsContent.appendChild(tabContent);
            });

            // Mostrar la primera pestaña por defecto
            if (plants.length > 0) {
                showTab(plants[0]);
            }
        }

        function showTab(plant) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.textContent === plant) {
                    tab.classList.add('active');
                }
            });

            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === `tab-content-${plant}`) {
                    content.classList.add('active');
                }
            });

            // Renderizar las máquinas expendedoras en el contenido de la pestaña seleccionada
            renderMachines(plant);
        }

        function renderMachines(plant) {
            const tabContent = document.getElementById(`tab-content-${plant}`);
            tabContent.innerHTML = ''; // Limpiar contenido previo

            const machines = vendingMachinesData.filter(machine => machine.planta === plant);

            machines.forEach(machine => {
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
                percentageDiv.textContent = machine.percentage + '% ';

                const NameVMDiv = document.createElement('div');
                NameVMDiv.textContent = machine.nombrevm;

                vmContainer.appendChild(fillDiv);
                vmContainer.appendChild(vendingMachineDiv);

                const colDiv = document.createElement('div');
                colDiv.classList.add('col-4');

                const cardDiv = document.createElement('div');
                cardDiv.classList.add('card');
                if (machine.dispo === "Off") {
                    cardDiv.classList.add('bg-danger');
                    cardDiv.classList.add('disabled');
                }

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
                percentageColDiv.appendChild(NameVMDiv);

                rowDiv.appendChild(innerColDiv);
                rowDiv.appendChild(percentageColDiv);

                cardDiv.appendChild(rowDiv);

                colDiv.appendChild(cardDiv);

                tabContent.appendChild(colDiv);

                updateVendingMachine(machine.id, machine.percentage);
            });
        }

        function fetchVendingMachinesData() {
            $.getJSON('/vm-status', function(data) {
                vendingMachinesData = data.map(stat => ({
                    planta: stat.Nplanta,
                    id: stat.Id_Maquina,
                    percentage: parseInt(stat.Per_Alm), // Ajusta esto según tu lógica
                    nombrevm: stat.NameVM,
                    dispo: stat.dispo
                }));

                // Inicializar las pestañas y renderizar las máquinas expendedoras
                initializeTabs();
            });
        }

        // Inicializar las máquinas expendedoras al cargar la página
        fetchVendingMachinesData();

        // Actualizar las máquinas expendedoras cada 5 segundos con datos reales
        setInterval(fetchVendingMachinesData, 60000);
    </script>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fetchRecentConsumptions() {
                fetch('/vm-rconsum')
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.getElementById('recent-consumptions-body');
                        tbody.innerHTML = ''; // Clear existing content
                        data.forEach((item, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${index + 1}</td>
                                <td>${item.NArticulo}</td>
                                <td>${item.NombreMaquina}</td>
                                <td>${item.Fecha_Consumo}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error fetching recent consumptions:', error));
            }

            // Fetch data initially
            fetchRecentConsumptions();
            
            // Fetch data every minute
            setInterval(fetchRecentConsumptions, 60000); // 60000 ms = 1 minute
        });
    </script>
@stop
