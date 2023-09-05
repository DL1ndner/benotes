# benotes
Public and private backend notes for TYPO3

## Introduction
### What does it do?
This extension sets up a new module “Notes” within the user modules, which enables the editors to write categorized private and public notes. Writing notes is empowered by TYPO3 integrated ckeditor. Backend Notes is based on Extbase and Fluid and works with typo3 12.4

## Users manual
Go to the new Notes Module at Users->Notes 
Create one or more Categories first by selecting “Create Category“ from the top menu
Create a new Note by selecting “Create Note” and assign a category to it – you can decide if the Note should be private or public
Warning! Public Notes can be deleted by everyone!

### FAQ
Can I answer on notes? Not yet, but could be a feature for the future.
Can I assign notes to certain be_users/be_usergroups? Not yet, but could be a feature for the future.

## Administration
1. Upload ans activate extension benotes
2. Create a new Sysfolder and remember the Page ID
3. Include static Template Backend Notes (benotes) to your Root Template
4. Set module.tx_benotes.persistence.storagePid to your Sysfolder ID

### FAQ
None so far.

## Configuration
Configuration is done by TypoScript, see below

### FAQ
I'm getting a fatal error because of missing Page ID! Check if module.tx_benotes.persistence.storagePid is set in the setup section of your root template.

## Reference (TypoScript)
module.tx_benotes.persistence: Page ID of sysfolder, where your notes and categories should be stored; must be set, Default: 
Example: module.tx_benotes.persistence.storagePid = 120
module.tx_benotes.view.templateRootPath: Path to backend view template root, default: EXT:benotes/Resources/Private/Backend/Templates/
module.tx_benotes.view.partialRootPath: Path to backend view partials root, default: EXT:benotes/Resources/Private/Backend/Partials/
module.tx_benotes.view.layoutRootPath: Path to backend view layout root, default: EXT:benotes/Resources/Private/Backend/Layouts/
