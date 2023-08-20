<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2023
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use Dl\Benotes\Controller\NoteController;
use Dl\Benotes\Controller\CategoryController;


return [
    'user_benotes' => [
        'parent' => 'user',
        'position' => ['before' => '*'],
        'access' => 'user',
        'workspaces' => '*',
        'standalone' => 1,
        'iconIdentifier' => 'notes',
        'path' => '/module/user/benotes/notes',
        'labels' => 'LLL:EXT:benotes/Resources/Private/Language/locallang.xlf',
        'extensionName' => 'benotes',
        'controllerActions' => [
            \Dl\Benotes\Controller\NoteController::class => [
                'list',
                'listPrivate', 
                'show', 
                'new',
                'create',
                'edit', 
                'update',
                'delete'
            ],
             \Dl\Benotes\Controller\CategoryController::class => [
                'list',
                'listPrivate', 
                'show', 
                'new',
                'create',
                'edit', 
                'update',
                'delete'
            ]
        ],
    ],
];

?>
