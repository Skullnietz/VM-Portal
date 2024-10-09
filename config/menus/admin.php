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
                    'url' => '#',
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
            'url' => 'admin/pages',
            'icon_color' => 'red',
            'icon' => 'fas fa-fw fa-industry',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Plantas',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Empleados',
                    'url' => 'admin/pages',
                    'icon' => 'fas fa-fw fa-users',
                    'icon_color' => 'red',
                    'label_color' => 'success',
                    'submenu' => [
                        [
                            'text' => 'Áreas Empleado',
                            'url' => '#',
                        ],
                        [
                            'text' => 'Departamentos',
                            'url' => '#',
                        ],
                        [
                            'text' => 'Empleados',
                            'url' => '#',
                        ],
                    ]
                ],

            ]
        ],
        [
            'text' => 'Productos',
            'url' => 'admin/pages',
            'icon' => 'fas fa-fw fa-boxes',
            'icon_color' => 'red',
            'label_color' => 'success',
            'submenu' => [
                [
                    'text' => 'Categoría Productos',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Proveedores Productos',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Productos',
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
                    'text' => 'Categorias',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Marcas',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Modelos',
                    'icon_color' => 'red',
                    'url' => '#',
                ],
                [
                    'text' => 'Carriles',
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
        ['header' => 'OPERACIÓN'],
        [
            'text' => 'Vendings',
            'url' => 'admin/settings',
            'icon_color' => 'yellow',
            'icon' => 'fas fa-fw fa-building',
            'submenu' => [
                [
                    'text' => 'Planograma',
                    'icon_color' => 'yellow',
                    'url' => '#',
                ],
                [
                    'text' => 'Resurtidos',
                    'icon_color' => 'yellow',
                    'url' => '#',
                ],
                [
                    'text' => 'Permisos Producto',
                    'icon_color' => 'yellow',
                    'url' => '#',
                ],
            ]
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