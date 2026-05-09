<?php
return [
    'menu' => [
        // Navbar items:

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
            'text' => 'Reportes',
            'icon_color' => 'blue',
            'icon' => 'fas fa-fw fa-chart-line',
            'submenu' => [
                [
                    'text' => 'Consumo por Empleado',
                    'icon_color' => 'blue',
                    'url' => 'op/reporte-op/consumoxempleado',
                ],
            ]
        ],



    ],
]
    ?>