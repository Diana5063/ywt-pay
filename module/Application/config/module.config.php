<?php

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index'
                    ]
                ]
            ],
            'application' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__.'/../view/layout.phtml',
            'error/404' => __DIR__.'/../view/error/404.phtml',
            'error/index' => __DIR__.'/../view/error/index.phtml'
        ],
        'template_path_stack' => [
            __DIR__.'/../view'
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ]
    ]
];
