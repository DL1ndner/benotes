<?php
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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder as UriBuilderBackend;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use Dl\Benotes\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * CategoryController
 */
#[Controller]
class CategoryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	public function __construct(
		protected TypoScriptService $typoScriptService,
		protected UriBuilderBackend $uriBuilderBackend,
         	protected readonly ModuleTemplateFactory $moduleTemplateFactory,
		private ResponseFactory $factory
    	 ) 
	{
		$this->moduleName = 'benotes_note';
        $this->modulePrefix = 'tx_benotes_user_benotescategories';
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

	public function findCurrent() {
		$currentCatUserUid = (int)$GLOBALS['BE_USER']->user['uid'];
		return $currentCatUserUid ? $this->findByUid($currentCatUserUid) : null;
	}	

	protected function initializeAction(): void
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
        return $this->iconFactory->getIcon($key, IconSize::SMALL);
    }

    protected function initializeModuleTemplate(
        ServerRequestInterface $request,
	    ): ModuleTemplate {
		$view = $this->moduleTemplateFactory->create($request);

		//$this->modifyDocHeaderComponent($view);
		$view->setFlashMessageQueue($this->getFlashMessageQueue());

		return $view;
	    }

	/**
	 * action list
	 * 
	 * @return void
	 */
	public function listAction(): ResponseInterface 
	{
		if (empty($GLOBALS['BE_USER']->user['uid'])) {
			return '';
		}
		$currentCatUserUid = (int)$this->getBackendUser()->user['uid'];
		$categories = $this->categoryRepository->findByCruser($currentCatUserUid);
		$view = $this->initializeModuleTemplate($this->request);
		$view->assign('categories', $categories);
	        $view->assign('actionMethodName', $this->actionMethodName);
        	
		return $view->renderResponse('Category/List');
	}

	/**
	 * action show
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $category
	 * @return void
	 */
	public function showAction(\Dl\Benotes\Domain\Model\Category $category): ResponseInterface
	{
		$view = $this->initializeModuleTemplate($this->request);
		$view->assign('category', $category);
		$view->assign('actionMethodName', $this->actionMethodName);
        	
		return $view->renderResponse('Category/Show');
	}


	/**
	 * action new
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $newCategory
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $newCategory
	 * @return void
	 */
	public function newAction(\Dl\Benotes\Domain\Model\Category $newCategory = NULL): ResponseInterface
	{
		$currentCatUserUid = (int)$this->getBackendUser()->user['uid'];
		$view = $this->initializeModuleTemplate($this->request);
		$view->assign('newCategory', $newCategory);
		$view->assign('cruser', $currentCatUserUid);
		$view->assign('actionMethodName', $this->actionMethodName);
        	
		return $view->renderResponse('Category/New');
	}

	/**
	 * action create
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $newCategory
	 * @return void
	 */
	public function createAction(\Dl\Benotes\Domain\Model\Category $newCategory): ResponseInterface
	{
		$currentCatUserUid = (int)$this->getBackendUser()->user['uid'];
		$view = $this->initializeModuleTemplate($this->request);
		$view->assign('cruser', $currentCatUserUid);
		$this->categoryRepository->add($newCategory);
		
		return $this->redirect('list');
	}

	/**
	 * action edit
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $category
	 * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $category
	 * @return void
	 */
	public function editAction(\Dl\Benotes\Domain\Model\Category $category): ResponseInterface
	{
		$view = $this->initializeModuleTemplate($this->request);
		$view->assign('category', $category);
		$view->assign('actionMethodName', $this->actionMethodName);
        	
		return $view->renderResponse('Category/Edit');
	}

	/**
	 * action update
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $category
	 * @return void
	 */
	public function updateAction(\Dl\Benotes\Domain\Model\Category $category): ResponseInterface
	{
		$this->categoryRepository->update($category);
		return $this->redirect('list');
	}

	/**
	 * action delete
	 * 
	 * @param \Dl\Benotes\Domain\Model\Category $category
	 * @return void
	 */
	public function deleteAction(\Dl\Benotes\Domain\Model\Category $category): ResponseInterface
	{
		$this->categoryRepository->remove($category);
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
