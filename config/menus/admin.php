<?php
return[
'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'Buscar',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Busqueda',
        ],
        [
            'text' => 'blog',
            'url' => 'admin/blog',
            'can' => 'manage-blog',
        ],
        ['header' => 'ADMINISTRACIÓN'],
        [
            'text' => 'Usuarios',
            'url' => 'admin/pages',
            'icon_color' => 'red',
            'icon' => 'fas fa-fw fa-user-friends',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Usuarios',
                    'icon_color' => 'red',
                    'url' => '/admin/usuarios',
                ],
                [
                    'text' => 'Administradores',
                    'icon_color' => 'red',
                    'url' => '/admin/administradores',
                ],
                [
                    'text' => 'Operadores',
                    'icon_color' => 'red',
                    'url' => '/admin/operadores',
                ],
            ]
        ],
        [
            'text' => 'Plantas',
            'url' => '/admin/plantas',
            'icon_color' => 'red',
            'icon' => 'fas fa-fw fa-industry',
            'label_color' => 'success',
        ],
        [
            'text' => 'Articulos',
            'url' => '/admin/articulos',
            'icon' => 'fas fa-fw fa-boxes',
            'icon_color' => 'red',
            'label_color' => 'success',
        ],
        [
            'text' => 'Vending',
            'icon' => 'fas fa-fw fa-building',
            'icon_color' => 'red',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Vending Machines',
                    'icon_color' => 'red',
                    'icon' => 'fas fa-fw fa-building',
                    'url' => '/admin/vendings',
                ],
                [
                    'text' => 'Dispositivos',
                    'icon' => 'fas fa-fw  fa-tablet',
                    'icon_color' => 'red',
                    'url' => '/admin/dispositivos',
                ],
            ]

        ],
        [
            'text' => 'Alarmas',
            'url' => '/admin/alertas',
            'icon' => 'fas fa-fw fa-flag',
            'icon_color' => 'red',
            'label_color' => 'success',
        ],
        
        ['header' => 'REPORTES'],
        [
            'text' => 'Empleados',
            'icon_color' => 'cyan',
            'icon' => 'fas fa-fw fa-people-carry',
            'url' => '#',
            'submenu' => [
                [
                    'text' => 'Reporte de Consumos',
                    'icon_color' => 'cyan',
                    'url' => '/all/reportesCE',
                ],
                [
                    'text' => 'Reporte de Activación NIP',
                    'icon_color' => 'cyan',
                    'url' => '/activaciones',
                ],
                [
                    'text' => 'Reporte Consumo por area',
                    'icon_color' => 'cyan',
                    'url' => '/all/reportesCA',
                ],
            ]
        ],
        [
            'text' => 'Vendings',
            'url' => 'admin/settings',
            'icon_color' => 'cyan',
            'icon' => 'fas fa-fw fa-building',
            'submenu' => [
                [
                    'text' => 'Inventario Vendings',
                    'icon_color' => 'cyan',
                    'url' => '#',
                ],
            ]
        ],
        [
            'text' => 'Resurtimiento',
            'icon_color' => 'orange',
            'icon' => 'fas fa-fw fa-clipboard-check',
            'submenu' => [
                [
                    'text' => 'Historial de Cortes',
                    'icon_color' => 'orange',
                    'url' => '/admin/corte/historial',
                ],
                [
                    'text' => 'Consumo entre Resurtimientos',
                    'icon_color' => 'orange',
                    'url' => '/admin/reporte/consumo-entre-resurtimientos',
                ],
                [
                    'text' => 'Discrepancias de Inventario',
                    'icon_color' => 'orange',
                    'url' => '/admin/reporte/discrepancias',
                ],
                [
                    'text' => 'Tendencias de Consumo',
                    'icon_color' => 'orange',
                    'url' => '/admin/reporte/tendencias',
                ],
            ]
        ],
        ['header' => 'MONITOREO'],
        [
            'text' => 'Dashboard Operativo',
            'url' => '/admin/dashboard-operativo',
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
                    'text' => 'Vendings Conectadas',
                    'icon_color' => 'green',
                    'url' => '#',
                ],
                [
                    'text' => 'Vendings Locales',
                    'icon_color' => 'green',
                    'url' => '#',
                ],
            ]
        ],


    ],
    ]
    ?>