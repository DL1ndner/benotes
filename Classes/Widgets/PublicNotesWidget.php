<?php

declare(strict_types=1);

namespace Dl\Benotes\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use Dl\Benotes\Domain\Repository\NoteRepository;

/**
 * Widget to display the last 10 public notes
 */
class PublicNotesWidget implements WidgetInterface, RequestAwareWidgetInterface, AdditionalCssInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly NoteRepository $noteRepository,
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly array $options = []
    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        try {
            $view = $this->backendViewFactory->create($this->request, ['dl/benotes']);
            
            // Set template root path
            $templatePaths = $view->getRenderingContext()->getTemplatePaths();
            $templatePaths->setTemplateRootPaths([
                'EXT:benotes/Resources/Private/Templates/'
            ]);
            
            // Fetch last 10 public notes
            $publicNotes = $this->getPublicNotes();
            
            $view->assignMultiple([
                'items' => $publicNotes,
                'options' => $this->options,
                'configuration' => $this->configuration,
            ]);
            
            $view->getRenderingContext()->setControllerName('Widget');
            $view->getRenderingContext()->setControllerAction('PublicNotesWidget');
            
            return $view->render();
        } catch (\Exception $e) {
            // Return error message for debugging
            return '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Get the last 10 public notes ordered by creation date descending
     *
     * @return array
     */
    protected function getPublicNotes(): array
    {
        try {
            // Get storagePid from TypoScript configuration
            $storagePid = $this->getStoragePid();
            
            // Set query settings
            $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
            if ($storagePid > 0) {
                $querySettings->setRespectStoragePage(true);
                $querySettings->setStoragePageIds([$storagePid]);
            } else {
                $querySettings->setRespectStoragePage(false);
            }
            $this->noteRepository->setDefaultQuerySettings($querySettings);
            
            // Query for public notes (ispublic = 1), ordered by crdate DESC, limit 10
            $query = $this->noteRepository->createQuery();
            $query->matching(
                $query->equals('public', 1)
            );
            $query->setOrderings(['crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);
            $query->setLimit(10);
            
            $notes = $query->execute();
            
            $items = [];
            foreach ($notes as $note) {
                $cruser = $note->getCruser();
                $cruserUid = 0;
                $username = 'System';
                
                // Check if cruser is a BackendUser object or an integer
                if ($cruser instanceof \TYPO3\CMS\Beuser\Domain\Model\BackendUser) {
                    $cruserUid = $cruser->getUid();
                    // Use realName if available, otherwise username
                    $username = !empty($cruser->getRealName()) ? $cruser->getRealName() : $cruser->getUserName();
                } elseif (is_int($cruser) && $cruser > 0) {
                    $cruserUid = $cruser;
                    $username = $this->getBackendUsername($cruserUid);
                }
                
                $items[] = [
                    'title' => $note->getTitle() ?? 'Untitled',
                    'content' => $this->truncateContent($note->getBodytext() ?? '', 150),
                    'crdate' => $note->getCrdate(),
                    'category' => $note->getCategory() ? $note->getCategory()->getTitle() : '',
                    'author' => $cruserUid,
                    'username' => $username,
                    'uid' => $note->getUid()
                ];
            }
            
            return $items;
        } catch (\Exception $e) {
            // Return empty array on error
            return [];
        }
    }

    /**
     * Get backend user's username from UID
     *
     * @param int $userUid
     * @return string
     */
    protected function getBackendUsername(int $userUid): string
    {
        if ($userUid === 0) {
            return 'System';
        }
        
        try {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('be_users');
            
            $user = $queryBuilder
                ->select('username', 'realName')
                ->from('be_users')
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($userUid, \PDO::PARAM_INT))
                )
                ->executeQuery()
                ->fetchAssociative();
            
            if ($user) {
                // Return real name if available, otherwise username
                return !empty($user['realName']) ? $user['realName'] : $user['username'];
            }
            
            return 'Unknown User';
        } catch (\Exception $e) {
            return 'User #' . $userUid;
        }
    }

    /**
     * Get storage PID from TypoScript configuration
     *
     * @return int
     */
    protected function getStoragePid(): int
    {
        try {
            $settings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            
            // Get storagePid from module.tx_benotes.persistence.storagePid
            $storagePid = $settings['module.']['tx_benotes.']['persistence.']['storagePid'] ?? 0;
            
            return (int)$storagePid;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Truncate content to specified length
     *
     * @param string $content
     * @param int $length
     * @return string
     */
    protected function truncateContent(string $content, int $length = 150): string
    {
        // Strip HTML tags
        $content = strip_tags($content);
        
        if (mb_strlen($content) <= $length) {
            return $content;
        }
        
        return mb_substr($content, 0, $length) . '...';
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Return CSS files to include
     *
     * @return array
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:benotes/Resources/Public/css/PublicNotesWidget.css',
        ];
    }
}
