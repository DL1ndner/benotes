<?php

use Dl\Benotes\Controller\NoteController;
use Dl\Benotes\Controller\CategoryController;


return [
    'user_benotes' => [
        'parent' => 'user',
        'position' => ['before' => '*'],
        'access' => 'user',
        'workspaces' => 'live',
        'iconIdentifier' => 'notes',
        'path' => '/module/user/benotes',
        'labels' => 'LLL:EXT:benotes/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'benotes',
        'controllerActions' => [
            NoteController::class => [
                'list',
                'listPrivate', 
                'show', 
                'new',
                'create',
                'edit', 
                'update',
                'delete'
            ],
            CategoryController::class => [
                'list',
                'listPrivate', 
                'show', 
                'new',
                'create',
                'edit', 
                'update',
                'delete'
            ],
        ],
    ],
];

?>
