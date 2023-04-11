<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_benotes_domain_model_note'] = [
	'ctrl' => [
		'label' => 'title',
		'default_sortby' => 'ORDER BY crdate',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		//'cruser_id' => 'cruser',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
		'delete' => 'deleted',
		'title' => 'LLL:EXT:benotes/Resources/Private/Language/locallang.xlf:tx_benotes_domain_model_notes',
		//'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('benotes') . 'Resources/Public/Icons/tx_benotes_domain_model_notes.gif',
		'iconfile' => 'EXT:benotes/Resources/Public/Icons/tx_benotes_domain_model_notes.gif',
		'sortby' => 'sorting'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, crdate, title, bodytext, public, cruser, cruser_id, category',
	],
	'types' => [
		'1' => [
			'showitem' => 'sys_language_uid;;;;, l10n_parent, l10n_diffsource, hidden, --palette--;;1, crdate, title, bodytext;;;, public, cruser, cruser_id, category,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime',
			'columnsOverrides' => [
				'config' => [
					'fieldControl' => 'fullScreenRichtext',
				],
			],
		],
	],
	'palettes' => [
		'1' => array('showitem' => ''),
	],
	'columns' => [
	
		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				],
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					array('', 0),
				],
				'foreign_table' => 'tx_benotes_domain_model_note',
				'foreign_table_where' => 'AND tx_benotes_domain_model_note.pid=###CURRENT_PID### AND tx_benotes_domain_model_note.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' =>[
			'config' => [
				'type' => 'passthrough',
			],
		],

		't3ver_label' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			],
		],
	
		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'behaviour' => [
					'allowLanguageSynchronization' => 'true',
				],
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
			],
		],
		'endtime' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'renderType' => 'inputDateTime',
				'size' => 13,
				'eval' => 'datetime',
				'behaviour' => [
					'allowLanguageSynchronization' => 'true',
				],
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
			],
		],
		'crdate' => [
			'exclude' => 1,
			'label' => 'Creation date',
			'config' => [
				'type' => 'none',
				'format' => 'date',
				'eval' => 'date',
 
			],	
		],
		
		'title' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_db.xlf:tx_benotes_domain_model_note.title',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			],
		],
		'bodytext' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_db.xlf:tx_benotes_domain_model_note.bodytext',
			'config' => [
				'type' => 'text',
				'enableRichtext' => true,
				'richtextConfiguration' => 'default',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim,required',
				/*'options' => 'richtext:rte_transform[flag=rte_enabled|mode=ts]',
				'wizards' => [
					'RTE' => [
						'icon' => 'actions-wizard_rte',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						//'script' => 'wizard_rte.php',
						'module' => [
							'name' => 'wizard_rte',
						],
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					],
				],*/
			],
			
		],
		'public' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_db.xlf:tx_benotes_domain_model_note.public',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		
		'category' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.category',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
                		'size' => 1,
               			 'minitems' => 1,
                		'maxitems' => 9999,
                		'autoSizeMax' => 5,
                		'multiple' => 0,
                		'foreign_table' => 'tx_benotes_domain_model_category',
				'default' => '0'
			],
		],
		'cruser' => [
			'exclude' => 1,
			'label' => 'Creation User',
			'config' => [
				'type' => 'none'
				
			],
		],
		
	],
];

?>
