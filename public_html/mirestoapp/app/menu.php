<?php

$menu = [
    [
        'label' => 'Panel Principal',
        'icon'  => 'ri-home-smile-line',
        'roles' => ['superadmin', 'admin', 'operador', 'repartidor'],
        'open'  => true,
        'children' => [
            [
                'label' => 'Pedidos',
                'icon'  => 'ri-shopping-bag-3-line',
                'link'  => 'index.php',
                'roles' => ['superadmin', 'admin', 'operador', 'repartidor'],
            ],
            [
                'label' => 'Pedido Telefónico',
                'icon'  => 'ri-phone-line',
                'link'  => 'pedido_telefonico.php',
                'roles' => ['superadmin', 'admin', 'operador'],
            ],
        ],
    ],
    [
        'label' => 'Catálogo',
        'icon'  => 'ri-restaurant-2-line',
        'roles' => ['superadmin', 'admin', 'operador'],
        'children' => [
            [
                'label' => 'Categorías',
                'icon'  => 'ri-layout-grid-line',
                'link'  => 'categorias.php',
                'roles' => ['superadmin', 'admin', 'operador'],
            ],
            [
                'label' => 'Productos',
                'icon'  => 'ri-store-2-line',
                'link'  => 'productos.php',
                'roles' => ['superadmin', 'admin', 'operador'],
            ],
            [
                'label' => 'Gustos Empanada',
                'icon'  => 'ri-restaurant-line',
                'link'  => 'empanada_gustos.php',
                'roles' => ['superadmin', 'admin', 'operador'],
            ],
            [
                'label' => 'Zonas de Envío',
                'icon'  => 'ri-map-pin-2-line',
                'link'  => 'zonas_envio.php',
                'roles' => ['superadmin', 'admin', 'operador'],
            ],
        ],
    ],
    [
        'label' => 'Salir',
        'icon'  => 'ri-logout-box-r-line',
        'link'  => 'logout.php',
        'roles' => ['superadmin', 'admin', 'operador', 'repartidor'],
    ],
];
