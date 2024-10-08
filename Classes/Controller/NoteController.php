<?php

declare(strict_types = 1);

namespace Dl\Benotes\Controller;

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

use Symfony\Component\Mime\Address;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\MailMessage;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Attribute\Controller;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder as UriBuilderBackend;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\DropDown\DropDownItem;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use GeorgRinger\NumberedPagination\NumberedPagination;
use Dl\Benotes\Domain\Repository\NoteRepository;
use Dl\Benotes\Domain\Repository\CategoryRepository;

/**
 * NoteController
 */
#[Controller]
class NoteController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	private ?NoteRepository $noteRepository = null;
	
	private ?CategoryRepository $categoryRepository = null;
	
	public function __construct(
		protected TypoScriptService $typoScriptService,
		protected UriBuilderBackend $uriBuilderBackend,
		protected ModuleTemplateFactory $moduleTemplateFactory,
		protected IconFactory $iconFactory,
		//protected readonly BackendUserRepository $backendUserRepository,
		private ResponseFactory $factory
	)  
	{
	 	$this->moduleName = 'benotes_note';
        	$this->modulePrefix = 'tx_benotes_user_benotesnotes';
		$this->moduleTemplateFactory = $moduleTemplateFactory;
		$this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
   	}

	public function injectNoteRepository(NoteRepository $noteRepository)
	{
		$this->noteRepository = $noteRepository;
	}
	
	public function injectCategoryRepository(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
	}

	public function injectBackendUserRepository (\TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository $backendUserRepository)
    	{
        	$this->backendUserRepository = $backendUserRepository;
    	}
	private function getBackendUser(): BackendUserAuthentication
    	{
        	return  $GLOBALS['BE_USER'];
    	}
	protected function getLanguageService(): LanguageService
    	{
        	return $GLOBALS['LANG'];
    	}
	
	protected function initializeAction()
	{
		$pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        	$pageRenderer->addCssFile('EXT:benotes/Resources/Public/css/tx_benotes.css');
		$languageService = $this->getLanguageService();
		$this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
		$this->moduleTemplate->setTitle(
                    	$languageService->sL('LLL:EXT:benotes/Resources/Private/Language/locallang.xlf:mlang_tabs_tab')
                );
	}

	
	/**
	* @return string
	* @throws RouteNotFoundException
	*/
	protected function getReturnUrl(): string
	{
	        $parameter = [
	            'id' => $this->pageUid,
	            $this->modulePrefix => [
	                'action' => $this->request->getControllerActionName(),
	                'controller' => $this->request->getControllerName(),
	            ],
	        ];
	        return (string)$this->uriBuilderBackend->buildUriFromRoute($this->moduleName, $parameter);
	}
	protected function getIcon(string $key): Icon
	{
	        return $this->iconFactory->getIcon($key, Icon::SIZE_SMALL);
	}
	
	/**
	* v12 returns ModuleTemplate, v11 ViewInterface
	*
	* @return ModuleTemplate|ViewInterface
	*/
	protected function getViewToUse()
	{
	        if (method_exists($this->moduleTemplate, 'assign')) {
	            return $this->moduleTemplate;
	        }
	        return $this->view;
	}
	
	protected function renderViewToUse(): ResponseInterface
	{
	        if (!$this->getViewToUse() instanceof ModuleTemplate) {
	            // v11
	            $this->moduleTemplate->setContent($this->getViewToUse()->render());
	            return $this->htmlResponse($this->moduleTemplate->render());
	        }
	        return $this->htmlResponse($this->getViewToUse()->render());
	}
	
	/**
	 * Render notes by single PID or PID list with numbered_pagination
	 *
	 * @param string $pids Single PID or comma separated list of PIDs
	 * @return string
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $pids
	 */
	// use Psr\Http\Message\ResponseInterface
	public function listAction(): ResponseInterface
	{
		if (empty($GLOBALS['BE_USER']->user['uid'])) {
			return '';
    		}
		$cruser = '';
		$notes = $this->noteRepository->findByCruser($cruser);	
		$currentPage = '1';
		$itemsPerPage = (int)$this->settings['itemsPerPage'];
		$maximumLinks = 10;
		$currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
		$paginator = new \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator($notes, $currentPage, $itemsPerPage);
		// temporarily deactivate numbered pagination
		//$pagination = new \GeorgRinger\NumberedPagination\NumberedPagination($paginator, $maximumLinks);
		$pagination = new SlidingWindowPagination(
            		$paginator,
            		$maximumLinks
	        );

	      //  $this->view->assign(
	      //  	'pagination',
	      //      	[
		//		'pagination' => $pagination,
		//		'paginator' => $paginator,
		//	]
		//);
		
		
		//$this->view->assign('notes', $notes);	
		
		//$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
      		//$moduleTemplate->setContent($this->view->render());
		//return $this->htmlResponse($moduleTemplate->renderContent());
		
		$this->getViewToUse()->assign('notes', $notes);
        	$this->getViewToUse()->assign(
		       	'pagination',
	            	[
				'pagination' => $pagination,
				'paginator' => $paginator,
			]
		);
       
        	$this->getViewToUse()->assign('actionMethodName', $this->actionMethodName);
        	return $this->renderViewToUse();


	}
	
	
	/**
	 * Render private notes by single PID or PID list
	 *
	 * @param string $pids Single PID or comma separated list of PIDs
	 * @return string
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $pids
	 */
	public function listPrivateAction(): ResponseInterface
	{
		if (empty($GLOBALS['BE_USER']->user['uid'])) {
			return '';
		}
		$cruser = $GLOBALS['BE_USER'];
		$notes = $this->noteRepository->findPrivateByCruser($cruser);
		$currentPage = '1';
		$itemsPerPage = (int)$this->settings['itemsPerPage'];
		$maximumLinks = 10;
		$currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
		$paginator = new \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator($notes, $currentPage, $itemsPerPage);
		//temporaily deactivate numbered pagination
		//$pagination = new \GeorgRinger\NumberedPagination\NumberedPagination($paginator, $maximumLinks);
		$pagination = new SlidingWindowPagination(
                   	$paginator,
                   	$maximumLinks
                );
		$this->getViewToUse()->assign('pagination', [
			'paginator' => $paginator,
			'pagination' => $pagination,
		]);
		$this->getViewToUse()->assign('notes', $notes);
		//$moduleTemplate = $this->moduleTemplateFactory->create($this->request);
      		//$moduleTemplate->setContent($this->view->render());
		$this->getViewToUse()->assign('actionMethodName', $this->actionMethodName);
        	return $this->renderViewToUse();

	}
	

	/**
	 * action show
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * @return void
	 */
	public function showAction(\Dl\Benotes\Domain\Model\Note $note): ResponseInterface
	{
				
		$this->getViewToUse()->assign('note', $note);
		$this->getViewToUse()->assign('actionMethodName', $this->actionMethodName);
        	return $this->renderViewToUse();


		
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
	public function newAction(\Dl\Benotes\Domain\Model\Note $newNote = NULL): ResponseInterface
	{
		$this->getViewToUse()->assign('newNote', $newNote);
		//$currentUserUid = (int)$GLOBALS['BE_USER']->user['uid'];
		$currentUserUid = (int)$this->getBackendUser()->user['uid'];
		$this->getViewToUse()->assign('cruser', $currentUserUid);
		
		$category = $this->categoryRepository->findByCruser($currentUserUid);
		$this->getViewToUse()->assign('category',$category);
		$this->getViewToUse()->assign('actionMethodName', $this->actionMethodName);
        	return $this->renderViewToUse();


	}
	
	

	/**
	 * action create
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $newNote
  	 * TYPO3\CMS\Extbase\Annotation\IgnoreValidation $newNote
         *  
	 * @return void
	 */
	public function createAction(\Dl\Benotes\Domain\Model\Note $newNote): ResponseInterface
	{
		$this->noteRepository->add($newNote);
		$currentUserUid = (int)$this->getBackendUser()->user['uid'];
		$category = $this->categoryRepository->findByCruser($currentUserUid);
		$this->getViewToUse()->assign('category',$category);
       
		//$currentUserUid = (int)$GLOBALS['BE_USER']->user['uid'];
		//$this->view->assign('cruser', $currentUserUid);
		
		$isitpublic = $newNote->getPublic();
		$this->getViewToUse()->assign('public',$isitpublic);
		
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

		return $this->redirect('list');
	}

	/**
	 * action edit
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * TYPO3\CMS\Extbase\Annotation\IgnoreValidation $note
	 * @return void
	 */
	public function editAction(\Dl\Benotes\Domain\Model\Note $note): ResponseInterface
	{
		$this->getViewToUse()->assign('note', $note);
		$category = $this->categoryRepository->findAll();
		$this->getViewToUse()->assign('category',$category);
		$this->getViewToUse()->assign('actionMethodName', $this->actionMethodName);
        	return $this->renderViewToUse();

	}

	/**
	 * action update
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * @return void
	 */
	public function updateAction(\Dl\Benotes\Domain\Model\Note $note): ResponseInterface
	{
		$this->noteRepository->update($note);
		$category = $this->categoryRepository->findAll();
		$this->getViewToUse()->assign('category',$category);
		
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

		return $this->redirect('list');
	}

	/**
	 * action delete
	 * 
	 * @param \Dl\Benotes\Domain\Model\Note $note
	 * @return void
	 */
	public function deleteAction(\Dl\Benotes\Domain\Model\Note $note): ResponseInterface
	{
		
		$this->noteRepository->remove($note);
		return $this->redirect('list');
		

	}

	/**
	 * action
	 * 
	 * @return void
	 */
	public function Action() {
		
	}

}
