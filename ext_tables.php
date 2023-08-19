<?php
defined('TYPO3') or die();

$extensionKey = 'benotes';
/**
 * Registers a Backend Module
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
	'Benotes',
	'user',	 // Make module a submodule of 'user'
	'Notes',	// Submodule key
	'top',						// Position
	[
	Dl\Benotes\Controller\NoteController::class => 'list, listPrivate, show, new, create, edit, update, delete',
	Dl\Benotes\Controller\CategoryController::class => 'list, listPrivate, show, new, create, edit, update, delete'
	],
	
	[
		'access' => 'user,group',
		'icon'   => 'EXT:benotes/Resources/Public/Icons/tx_benotes_domain_model_notes.png',
		'labels' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_notes.xlf',
	]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extensionKey, 'Configuration/TypoScript', 'Backend Notes');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_benotes_domain_model_note', 'EXT:benotes/Resources/Private/Language/locallang_csh_tx_benotes_domain_model_note.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_benotes_domain_model_note');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_benotes_domain_model_category', 'EXT:benotes/Resources/Private/Language/locallang_csh_tx_benotes_domain_model_category.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_benotes_domain_model_category');

?>
