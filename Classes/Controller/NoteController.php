<?php
namespace Dl\Benotes\Controller;

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

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Imaging\Icon;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\MailMessage;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator; 
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use GeorgRinger\NumberedPagination\NumberedPagination;
use Dl\Benotes\Domain\Repository\NoteRepository;
use Dl\Benotes\Domain\Repository\CategoryRepository;

/**
 * NoteController
 */
class NoteController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * noteRepository
	 * 
	 * @var \Dl\Benotes\Domain\Repository\NoteRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	private ?NoteRepository $noteRepository = null;

    public function injectNoteRepository(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }
	
	/**
	 * categoryRepository
	 * 
	 * @var \Dl\Benotes\Domain\Repository\CategoryRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	private ?CategoryRepository $categoryRepository = null;

    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
	
	
	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\BackendUserRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $backendUserRepository;
	
	
	
	/**
	 * Render notes by single PID or PID list with numbered_pagination
	 *
	 * @param string $pids Single PID or comma separated list of PIDs
	 * @return string
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $pids
	 */
	public function listAction() {
		if (empty($GLOBALS['BE_USER']->user['uid'])) {
			return '';
    		}
		$notes = $this->noteRepository->findByCruser($cruser);	
		$currentPage = '1';
		$itemsPerPage = $this->settings['itemsPerPage'];
		$maximumLinks = 10;
		$currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
		$paginator = new \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator($notes, $currentPage, $itemsPerPage);
		$pagination = new \GeorgRinger\NumberedPagination\NumberedPagination($paginator, $maximumLinks);
		$this->view->assign('pagination', [
			'paginator' => $paginator,
			'pagination' => $pagination,
		]);
		$this->view->assign('notes', $notes);		
	}
	
	
	/**
	 * Render private notes by single PID or PID list
	 *
	 * @param string $pids Single PID or comma separated list of PIDs
	 * @return string
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $pids
	 */
	public function listPrivateAction() {
		if (empty($GLOBALS['BE_USER']->user['uid'])) {
			return '';
		}
		$notes = $this->noteRepository->findPrivateByCruser($cruser);
		$currentPage = '1';
		$itemsPerPage = $this->settings['itemsPerPage'];
		$maximumLinks = 10;
		$currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
		$paginator = new \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator($notes, $currentPage, $itemsPerPage);
		$pagination = new \GeorgRinger\NumberedPagination\NumberedPagination($paginator, $maximumLinks);
		$this->view->assign('pagination', [
			'paginator' => $paginator,
			'pagination' => $pagination,
		]);
		$this->view->assign('notes', $notes);
		
	}
	

	/**
	 * action show
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * @return void
	 */
	public function showAction(\Dl\Benotes\Domain\Model\Note $note) {
				
		$this->view->assign('note', $note);
		
		
	}
	public function findCurrent() {
		$currentUserUid = (int)$GLOBALS['BE_USER']->user['uid'];
		return $currentUserUid ? $this->findByUid($currentUserUid) : null;
	}
	/**
	 * action new
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $newNote
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $newNote
	 * @return void
	 */
	public function newAction(\Dl\Benotes\Domain\Model\Note $newNote = NULL) {
		$this->view->assign('newNote', $newNote);
		$currentUserUid = (int)$GLOBALS['BE_USER']->user['uid'];
		$this->view->assign('cruser',$currentUserUid);
		
		$category = $this->categoryRepository->findByCruser($cruser);
		$this->view->assign('category',$category);
		
	}
	
	

	/**
	 * action create
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $newNote
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $newNote
	 * @return void
	 */
	public function createAction(\Dl\Benotes\Domain\Model\Note $newNote) {
		$this->noteRepository->add($newNote);
		$category = $this->categoryRepository->findByCruser($cruser);
		$this->view->assign('category',$category);
       
		$currentUserUid = (int)$GLOBALS['BE_USER']->user['uid'];
		$this->view->assign('cruser',$this->currentUserUid);
		
		$isitpublic = $newNote->getPublic();
		$this->view->assign('public',$public);
		
		$site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId(1);
		
		// if note is public, send message to recipients defined by typoscript
		if($isitpublic == 1) {
			if($this->settings['infomailto'] !='') {
				$from = \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();
			
				$recipients = $this->settings['infomailto'];
				$recipients = trim($recipients);
				$recipients = str_replace(',', ';', $recipients);
				$recipients = explode(';', $recipients);
			
				$mail = GeneralUtility::makeInstance(FluidEmail::class);
     				$mail
				       ->to(...$recipients)
				       ->format(FluidEmail::FORMAT_BOTH)
				       ->setTemplate('NewNoteMail')
							 ->assign('baseUri', (string)$site->getBase())
				       ->assign('note', $newNote);
				try {
					GeneralUtility::makeInstance(Mailer::class)->send($mail);
					$this->addFlashMessage('Öffentliche Mitteilung erstellt. Eine Bestätigungsmail wurde an ' . $this->settings['infomailto'] . ' versandt.');
				}
				catch (TransportExceptionInterface $e) {
					$this->addFlashMessage('Öffentliche Mitteilung erstellt. Die Bestätigungsmail wurde nicht versandt.');
				}
			} else {
				$this->addFlashMessage('Öffentliche Mitteilung erstellt.');
			}

		} else {
			$this->addFlashMessage('Private Notiz erstellt.');
		}

		$this->redirect('list');
	}

	/**
	 * action edit
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * TYPO3\CMS\Extbase\Annotation\IgnoreValidation $note
	 * @return void
	 */
	public function editAction(\Dl\Benotes\Domain\Model\Note $note) {
		$this->view->assign('note', $note);
		$category = $this->categoryRepository->findAll();
		$this->view->assign('category',$category);
	}

	/**
	 * action update
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * @return void
	 */
	public function updateAction(\Dl\Benotes\Domain\Model\Note $note) {
		$this->noteRepository->update($note);
		$category = $this->categoryRepository->findAll();
		$this->view->assign('category',$category);
		$isitpublic = $note->getPublic();
		$site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId(1);
		// if note is public, send message to recipients defined by typoscript
		if($isitpublic == 1) {
			if($this->settings['infomailto'] !='') {
				$from = \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();
				$recipients = $this->settings['infomailto'];
				$recipients = trim($recipients);
				$recipients = str_replace(',', ';', $recipients);
				$recipients = explode(';', $recipients);

    				$mail = GeneralUtility::makeInstance(FluidEmail::class);
    				$mail
		       			->to(...$recipients)
		       			->format(FluidEmail::FORMAT_BOTH)
		       			->setTemplate('UpdateNoteMail')
					->assign('baseUri', (string)$site->getBase())
		       			->assign('note', $note);
    	 			try {
					GeneralUtility::makeInstance(Mailer::class)->send($mail);
 				  	$this->addFlashMessage('Öffentliche Mitteilung geändert. Eine Bestätigungsmail wurde an ' . $this->settings['infomailto'] . ' versandt.');
				}
      				catch (TransportExceptionInterface $e) {
			   		$this->addFlashMessage('Öffentliche Mitteilung geändert. Die Bestätigungsmail wurde nicht versandt.');
				}
			} else {
			   	$this->addFlashMessage('Öffentliche Mitteilung geändert.');
			}

		} else {
			$this->addFlashMessage('Private Notiz geändert.');
		}

		$this->redirect('list');
	}

	/**
	 * action delete
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * @return void
	 */
	public function deleteAction(\Dl\Benotes\Domain\Model\Note $note) {
		
		$this->noteRepository->remove($note);
		$this->redirect('list');
	}

	/**
	 * action
	 * 
	 * @return void
	 */
	public function Action() {
		
	}

}
