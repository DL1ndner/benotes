<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "benotes"
 *
 * Auto generated by Extension Builder 2014-04-12
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Backend Notes',
	'description' => 'Editors can add public and private Notes to this Backend module. Notes can be categorized.',
	'category' => 'module',
	'author' => 'Dominik Lindner',
	'author_email' => 'lindner_dominik@gmx.de',
	'author_company' => '',
	'shy' => '',
	'priority' => '',
	'module' => 'user',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.5.1',
	'constraints' => array(
		'depends' => array(
			'extbase' => '11.5.1-11.5.99',
			'fluid' => '11.5.1-11.5.99',
			'typo3' => '11.5.1-11.5.99',
			'numbered_pagination' => '1.0.0-1.9.99'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>
