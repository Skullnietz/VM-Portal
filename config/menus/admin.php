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
            ]
        ],
        [
            'text' => 'Plantas',
            'url' => '/admin/plantas',
            'icon_color' => 'red',
            'icon' => 'fas fa-fw fa-industry',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Plantas',
                    'icon_color' => 'red',
                    'url' => '/admin/plantas',
                ],
                

            ]
        ],
        [
            'text' => 'Articulos',
            'url' => 'admin/pages',
            'icon' => 'fas fa-fw fa-boxes',
            'icon_color' => 'red',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Articulos',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
            ]
        ],
        [
            'text' => 'Vendings',
            'url' => 'admin/pages',
            'icon' => 'fas fa-fw fa-building',
            'icon_color' => 'red',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Modulos',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Planogramas',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Vendings',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
            ]

        ],
        [
            'text' => 'Alarmas',
            'url' => 'admin/pages',
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
                    'text' => 'Consumos',
                    'icon_color' => 'cyan',
                    'url' => '#',
                ],
                [
                    'text' => 'Activación NIP',
                    'icon_color' => 'cyan',
                    'url' => '#',
                ],
                [
                    'text' => 'Consumo empleado',
                    'icon_color' => 'cyan',
                    'url' => '#',
                ],
                [
                    'text' => 'Config Reportes',
                    'icon_color' => 'cyan',
                    'url' => '#',
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
        ['header' => 'MONITOREO'],
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