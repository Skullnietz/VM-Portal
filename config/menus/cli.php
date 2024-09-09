
<?php
return[
    'menu' => [
        // Navbar items:
        
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Busqueda',
        ],


        ['header' => 'CONFIGURACIÓN'],
        
        [
            'text' => 'Empleados',
            'icon_color' => 'cyan',
            'icon' => 'fas fa-fw fa-people-carry',
            'url' => '/cli/empleados-cli',
        ],
        [
            'text' => 'Areas',
            'icon' => 'fas fa-fw fa-grip-horizontal',
            'icon_color' => 'cyan',
            'url' => '/cli/areas-cli',
        ],
        [
            'text' => 'Permisos Articulo',
            'icon' => 'fas fa-fw fa-user-lock',
            'icon_color' => 'cyan',
            'url' => '/cli/permisos-cli',
        ],
        ['header' => 'REPORTES'],
        [
            'text' => 'Consumos',
            'icon_color' => 'green',
            'icon' => 'fas fa-fw fa-people-carry',
            'url' => '#',
            'submenu' => [
                [
                    'text' => 'Consumo por Area',
                    'icon_color' => 'green',
                    'url' => '/cli/reporte/consumoxarea',
                ],
                [
                    'text' => 'Consumo por Empleado',
                    'icon_color' => 'green',
                    'url' => '/cli/reporte/consumoxempleado',
                ],
                [
                    'text' => 'Consumo por Vending',
                    'icon_color' => 'green',
                    'url' => '/cli/reporte/consumoxvending',
                ],
            ]
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
                    'url' => '#',
                ],
            ]
        ],
        


    ],
]
?>

