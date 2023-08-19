<?php
defined('TYPO3') or die();

$extensionKey = 'benotes';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extensionKey, 'Configuration/TypoScript', 'Backend Notes');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_benotes_domain_model_note', 'EXT:benotes/Resources/Private/Language/locallang_csh_tx_benotes_domain_model_note.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_benotes_domain_model_note');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_benotes_domain_model_category', 'EXT:benotes/Resources/Private/Language/locallang_csh_tx_benotes_domain_model_category.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_benotes_domain_model_category');

?>
