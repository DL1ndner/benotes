<?php
if (!defined ('TYPO3')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_benotes_domain_model_category'] = [
	'ctrl' => [
		'label' => 'title',
		'default_sortby' => 'ORDER BY crdate',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		//'cruser_id' => 'cruser',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
		'delete' => 'deleted',
		'title' => 'LLL:EXT:benotes/Resources/Private/Language/locallang.xlf:tx_benotes_domain_model_categories',
		'iconfile' => 'EXT:benotes/Resources/Public/Icons/tx_benotes_domain_model_category.gif',
		'sortby' => 'sorting',
	        'security' => [
	             'ignorePageTypeRestriction' => true
	        ],
	],
	'types' => [
		'1' => [
			'showitem' => 'sys_language_uid;;;;, l10n_parent, l10n_diffsource, hidden, --palette--;;1, title, description, public, cruser,--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime',
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
				'type' => 'language'
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
				'foreign_table' => 'tx_benotes_domain_model_category',
				'foreign_table_where' => 'AND tx_benotes_domain_model_category.pid=###CURRENT_PID### AND tx_benotes_domain_model_category.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' =>  [
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
				'type' => 'datetime',
    				'format' => 'date',
         			'required' => true,
         			'size' => 20,
         			'default' => 0,
			],
		],
		'endtime' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'datetime',
    				'format' => 'date',
         			'required' => true,
         			'size' => 20,
         			'default' => 0,
			],
		],
		'title' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_db.xlf:tx_benotes_domain_model_category.title',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'required' => true,
			],
		],
		'description' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_db.xlf:tx_benotes_domain_model_category.description',
			'config' => [
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'default' => 'Category Description'
			],
		],
		'public' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:benotes/Resources/Private/Language/locallang_db.xlf:tx_benotes_domain_model_category.public',
			'config' => [
				'type' => 'check',
				'default' => 0
			],
		],
		'cruser' => [
			'exclude' => 1,
			'label' => 'Creation User',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle' 
				
			],
		],
		
	],
];

?>
