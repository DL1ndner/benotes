<?php
namespace Dl\Benotes\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014
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
 * Category
 */
 
use TYPO3\CMS\Extbase\Annotation as Extbase;

class Category extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	* @var \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
	*/
	protected $userRepository;  
	
	/**
	 * Title
	 * 
	 * @var string
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $title = '';

	/**
	 * Description
	 * 
	 * @var string
	 */
	protected $description = '';

	/**
	 * Public
	 * 
	 * @var boolean
	 * @Extbase\Validate("NotEmpty")
	 */
	protected $public = 'FALSE';
	
	/**
  * The creation user from the be_users
  *
  * @var \TYPO3\CMS\Extbase\Domain\Model\BackendUser $cruser
  */
 protected $cruser;


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
	 * Returns the description
	 * 
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 * 
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
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
     * Returns the cruser
     *
     * @return $cruser
     */
    public function getCruser() {
        return $this->cruser;
    }
	
	

    /**
     * Sets the cruser
     *
     * @param int $cruser
     * @return void
     */
    public function setCruser($cruser) {
		$this->cruser = $cruser;
    }
}