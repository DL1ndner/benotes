<?php
defined('TYPO3') or die();

	$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
   \TYPO3\CMS\Core\Imaging\IconRegistry::class
	);
	$iconRegistry->registerIcon(
		'benotes-note', // Icon-Identifier, e.g. tx-myext-action-preview
		\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
		['source' => 'EXT:benotes/Resources/Public/Icons/tx_benotes_domain_model_notes.gif']
	);
	
call_user_func(function()
{
   $extensionKey = 'benotes';

   \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
      $extensionKey,
      'setup',
      "@import 'EXT:benotes/Configuration/TypoScript/setup.typoscript'"
   );
   \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
      $extensionKey,
      'constants',
      "@import 'EXT:benotes/Configuration/TypoScript/constants.typoscript'"
   );
});


?>
