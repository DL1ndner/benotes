<?php
defined('TYPO3') or die();

$extensionKey = 'benotes';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extensionKey, 'Configuration/TypoScript', 'Backend Notes');

?>
