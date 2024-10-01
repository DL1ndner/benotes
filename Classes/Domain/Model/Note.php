<?php
namespace Dl\Benotes\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2024
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

/**
 * Note
 */
use TYPO3\CMS\Beuser\Domain\Model\BackendUser; 
use TYPO3\CMS\Extbase\Annotation as Extbase;

class Note extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * MODEL
	 * 
	 * 
	 * 
	 */
		
	
/**
 * @var Tx_Extbase_Domain_Repository_BackendUserRepository
 */
protected $userRepository;    

	
	/**
    * crdate
    *
    * @var string
    */
    protected $crdate;
	
	/**
	 * Title
	 * 
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $title = '';

	/**
	 * Content
	 * 
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $bodytext = '\'\'';

	/**
	 * Public
	 * 
	 * @var boolean
	 */
	protected $public = 'FALSE';

	/**
	 * category
	 * 
	 * @var \Dl\Benotes\Domain\Model\Category
         * @Extbase\Validate("NotEmpty")
	 */
	protected $category;

	/**
	 * @var BackendUser|null
	 */
	protected ?BackendUser $cruser = null;
 
	
		
	/**
	 * Returns the title
	 * 
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 * 
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the bodytext
	 * 
	 * @return string $bodytext
	 */
	public function getBodytext() {
		return $this->bodytext;
	}

	/**
	 * Sets the bodytext
	 * 
	 * @param string $bodytext
	 * @return void
	 */
	public function setBodytext($bodytext) {
		$this->bodytext = $bodytext;
	}

	/**
	 * Returns the public
	 * 
	 * @return boolean $public
	 */
	public function getPublic() {
		return $this->public;
	}

	/**
	 * Sets the public
	 * 
	 * @param boolean $public
	 * @return void
	 */
	public function setPublic($public) {
		$this->public = $public;
	}

	/**
	 * Returns the boolean state of public
	 * 
	 * @return boolean
	 */
	public function isPublic() {
		return $this->public;
	}

	/**
	 * Returns the category
	 * 
	 * @return \Dl\Benotes\Domain\Model\Category $category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Sets the category
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $category
	 * @return void
	 */
	public function setCategory(\Dl\Benotes\Domain\Model\Category $category) {
		$this->category = $category;
	}

     
     
    /**
    * Returns the crdate
    *
    * @return string $crdate
    */
    public function getCrdate() {
    return $this->crdate;
    }
     
    /**
    * Sets the crdate
    *
    * @param string $crdate
    * @return void
    */
    public function setCrdate($crdate) {
    $this->crdate = $crdate;
    }
	
	/**
	 * @return BackendUser|null
	 */
	public function getCruser(): ?BackendUser
	{
	    return $this->cruser;
	}
	
	/**
	 * @param ?BackendUser $cruser
	 */
	public function setCruser(?BackendUser $cruser): void
	{
	    $this->cruser = $cruser;
	}

   
	
	
}
