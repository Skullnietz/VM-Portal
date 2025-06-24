@extends('adminlte::page')

@section('usermenu_body')
<center><b>Administrador</b></center>
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
                <span class="info-box-text">Planta de Alto Consumo</span>
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
            
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Gráficas
                            </h3><br><br>
                            <div class="card-tools">
                                <ul class="nav nav-pills ml-auto" id="chartPills">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-chart="planta" href="#">Por Planta</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-chart="productos" href="#">Top Productos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-chart="matriz" href="#">Matriz</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-chart="dia" href="#">Por Día</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="chart-planta" class="chart-container">
                                <h5 class="text-center">Consumo Total por Planta</h5>
                                <canvas id="chartPlantas"></canvas>
                            </div>
                            <div id="chart-productos" class="chart-container d-none">
                                <h5 class="text-center">Top 5 Productos</h5>
                                <canvas id="chartTopProductos"></canvas>
                            </div>
                            <div id="chart-matriz" class="chart-container d-none">
                                <h5 class="text-center">Consumo por Planta y Producto</h5>
                                <canvas id="chartMatriz"></canvas>
                            </div>
                            <div id="chart-dia" class="chart-container d-none">
                                <h5 class="text-center">Consumo Diario</h5>
                                <canvas id="chartPorDia"></canvas>
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
                            <div class="card-body p-0 table-responsive" style="height:740px">
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
                       <!-- Se puede agregar una card aqui -->
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

        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: flex;
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
    document.addEventListener("DOMContentLoaded", function () {
        // Control de tabs manual
        $('#chartPills a').on('click', function (e) {
            e.preventDefault();

            // Cambiar clase activa
            $('#chartPills a').removeClass('active');
            $(this).addClass('active');

            const chartTarget = $(this).data('chart');

            // Ocultar todos los contenedores
            $('.chart-container').addClass('d-none');

            // Mostrar el seleccionado
            $('#chart-' + chartTarget).removeClass('d-none');

            // Redibujar la gráfica si es necesario
            switch (chartTarget) {
                case 'planta':
                    if (window.chartPlantas) chartPlantas.resize();
                    break;
                case 'productos':
                    if (window.chartTopProductos) chartTopProductos.resize();
                    break;
                case 'matriz':
                    if (window.chartMatriz) chartMatriz.resize();
                    break;
                case 'dia':
                    if (window.chartPorDia) chartPorDia.resize();
                    break;
            }
        });
    });
</script>

</script>
    

    <script>
        function updateInfoBoxes() {
    fetch('/vm-admin')
        .then(response => response.json())
        .then(data => {
            document.getElementById('producto-mas-consumido').innerText = data.producto_mas_consumido || 'N/A';
            document.getElementById('area-alto-consumo').innerText = data.planta_alto_consumo || 'N/A';
            document.getElementById('vendings-activas').innerText = data.vendings_activas || '0';
            document.getElementById('articulos-consumidos').innerText = data.articulos_consumidos || '0';
        })
        .catch(error => console.error('Error:', error));
}

setInterval(updateInfoBoxes, 5000); // Actualizar cada 5 segundos
    </script>
    <script>
                                async function fetchData() {
                                    const response = await fetch('/vm-admingraphs');
                                    return await response.json();
                                }
                            
                                function updateCharts(data) {
                                    // Destruir si ya existen
                                    ['chartPlantas', 'chartTopProductos', 'chartMatriz', 'chartPorDia'].forEach(id => {
                                        const chart = window[id];
                                        if (chart && typeof chart.destroy === 'function') {
                                            chart.destroy();
                                        }
                                    });
                            
                                    // 1. Consumo por planta
                                    const plantaLabels = data.porPlanta.map(p => p.planta);
                                    const plantaValues = data.porPlanta.map(p => p.total_consumo);
                                    window.chartPlantas = new Chart(document.getElementById('chartPlantas'), {
                                        type: 'bar',
                                        data: {
                                            labels: plantaLabels,
                                            datasets: [{
                                                label: 'Total Consumido',
                                                data: plantaValues,
                                                backgroundColor: 'rgba(75,192,192,0.7)',
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: { y: { beginAtZero: true } }
                                        }
                                    });
                            
                                    // 2. Top productos
                                    const topLabels = data.topProductos.map(p => p.producto);
                                    const topValues = data.topProductos.map(p => p.total);
                                    window.chartTopProductos = new Chart(document.getElementById('chartTopProductos'), {
                                        type: 'pie',
                                        data: {
                                            labels: topLabels,
                                            datasets: [{
                                                data: topValues,
                                                backgroundColor: [
                                                    'rgba(255,99,132,0.8)',
                                                    'rgba(255,159,64,0.8)',
                                                    'rgba(255,205,86,0.8)',
                                                    'rgba(75,192,192,0.8)',
                                                    'rgba(54,162,235,0.8)'
                                                ]
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: { legend: { position: 'top' } }
                                        }
                                    });
                            
                                    // 3. Matriz por planta y producto
                                    const productosUnicos = [...new Set(data.porPlantaYProducto.map(d => d.producto))];
                                    const plantasUnicas = [...new Set(data.porPlantaYProducto.map(d => d.planta))];
                                    const colores = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
                            
                                    const datasetsMatriz = plantasUnicas.map((planta, idx) => {
                                        const datos = productosUnicos.map(prod => {
                                            const found = data.porPlantaYProducto.find(d => d.planta === planta && d.producto === prod);
                                            return found ? found.total : 0;
                                        });
                                        return {
                                            label: planta,
                                            data: datos,
                                            backgroundColor: colores[idx % colores.length]
                                        };
                                    });
                            
                                    window.chartMatriz = new Chart(document.getElementById('chartMatriz'), {
                                        type: 'bar',
                                        data: {
                                            labels: productosUnicos,
                                            datasets: datasetsMatriz
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                tooltip: { mode: 'index', intersect: false },
                                                legend: { position: 'top' }
                                            },
                                            scales: { y: { beginAtZero: true }, x: { stacked: true }, yAxes: { stacked: true } }
                                        }
                                    });
                            
                                    // 4. Consumo por día
                                    const diaLabels = data.porDia.map(d => d.dia);
                                    const diaValues = data.porDia.map(d => d.total);
                            
                                    window.chartPorDia = new Chart(document.getElementById('chartPorDia'), {
                                        type: 'line',
                                        data: {
                                            labels: diaLabels,
                                            datasets: [{
                                                label: 'Total Diario',
                                                data: diaValues,
                                                fill: true,
                                                backgroundColor: 'rgba(153,102,255,0.2)',
                                                borderColor: 'rgba(153,102,255,1)',
                                                tension: 0.1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: { y: { beginAtZero: true } }
                                        }
                                    });
                                }
                            
                                function refreshCharts() {
                                    fetchData().then(data => updateCharts(data));
                                }
                            
                                // Inicializar y actualizar cada minuto
                                refreshCharts();
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

        function initializeTabs(currentActiveTab = null) {
            const tabsContainer = document.getElementById('tabs-container');
            const tabsContent = document.getElementById('tabs-content');

            tabsContainer.innerHTML = '';
            tabsContent.innerHTML = '';

            const plants = [...new Set(vendingMachinesData.map(machine => machine.planta))];

            plants.forEach(plant => {
                const machineCount = vendingMachinesData.filter(m => m.planta === plant).length;

                const tab = document.createElement('div');
                tab.classList.add('tab');
                tab.textContent = `${plant} (${machineCount})`;
                tab.addEventListener('click', () => showTab(plant));
                tabsContainer.appendChild(tab);

                const tabContent = document.createElement('div');
                tabContent.classList.add('tab-content', 'row');
                tabContent.id = `tab-content-${plant}`;
                tabsContent.appendChild(tabContent);
            });

            // Restaurar la pestaña activa anterior, si existe
            if (plants.length > 0) {
                const tabToShow = currentActiveTab && plants.includes(currentActiveTab) ? currentActiveTab : plants[0];
                showTab(tabToShow);
            }
        }

    function showTab(plant) {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.classList.remove('active');
            const tabName = tab.textContent.replace(/\s\(\d+\)$/, '');
            if (tabName === plant) {
                tab.classList.add('active');
            }
        });

        tabContents.forEach(content => {
            content.classList.remove('active');
            content.style.display = 'none'; // 👈 Ocultamos todos
        });

        const activeContent = document.getElementById(`tab-content-${plant}`);
        activeContent.classList.add('active');
        activeContent.style.display = 'flex'; // 👈 Mostramos solo el actual

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
                if (machine.dispo !== "On") {
                    cardDiv.classList.add('bg-danger');
                    cardDiv.classList.add('disabled');
                }
                console.log(`Máquina ${machine.nombrevm} - dispo: ${machine.dispo}`);

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
            const currentActiveTab = document.querySelector('.tab.active')?.textContent.replace(/\s\(\d+\)$/, '');
            
            $.getJSON('/vm-allstatus', function(data) {
                vendingMachinesData = data.map(stat => ({
                    planta: stat.Nplanta,
                    id: stat.Id_Maquina,
                    percentage: parseInt(stat.Per_Alm), // Ajusta esto según tu lógica
                    nombrevm: stat.NameVM,
                    dispo: stat.dispo
                }));

                console.log(vendingMachinesData);

            
                // Inicializar las pestañas pasando la actual activa
                initializeTabs(currentActiveTab);
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
                fetch('/vm-rallconsum')
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
