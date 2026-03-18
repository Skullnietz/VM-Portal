<?php
return [
    'menu' => [
        // Navbar items:

        [
            'text' => 'Dashboard Operativo',
            'url' => 'op/dashboard-operativo',
            'icon_color' => 'green',
            'icon' => 'fas fa-fw fa-tachometer-alt',
        ],
        [
            'text' => 'Vendings',
            'url' => 'admin/settings',
            'icon_color' => 'green',
            'icon' => 'fas fa-fw fa-building',
            'submenu' => [
                [
                    'text' => 'Inventario Vendings',
                    'icon_color' => 'green',
                    'url' => 'op/op-vendings',
                ],
            ]
        ],
        [
            'text' => 'Cortes',
            'icon_color' => 'orange',
            'icon' => 'fas fa-fw fa-clipboard-check',
            'submenu' => [
                [
                    'text' => 'Historial de Cortes',
                    'icon_color' => 'orange',
                    'url' => 'op/corte/historial',
                ],
            ]
        ],
        [
            'text' => 'Reportes',
            'icon_color' => 'blue',
            'icon' => 'fas fa-fw fa-chart-line',
            'submenu' => [
                [
                    'text' => 'Consumo por Empleado',
                    'icon_color' => 'blue',
                    'url' => 'op/reporte-op/consumoxempleado',
                ],
                [
                    'text' => 'Consumo entre Resurtimientos',
                    'icon_color' => 'blue',
                    'url' => 'op/reporte/consumo-entre-resurtimientos',
                ],
                [
                    'text' => 'Discrepancias',
                    'icon_color' => 'blue',
                    'url' => 'op/reporte/discrepancias',
                ],
                [
                    'text' => 'Tendencias de Consumo',
                    'icon_color' => 'blue',
                    'url' => 'op/reporte/tendencias',
                ],
            ]
        ],



    ],
]
    ?>
