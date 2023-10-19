<?php

declare(strict_types=1);

/*
 *
 * This file is part of the "Site Generator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 */

namespace Oktopuce\SiteGenerator\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Core\Localization\LanguageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Oktopuce\SiteGenerator\Wizard\SiteGeneratorWizard;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;
use Oktopuce\SiteGenerator\Dto\SiteGeneratorDto;
use Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingFirstStepViewEvent;
use Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingSecondStepViewEvent;

/**
 * SiteGeneratorController
 */
//class SiteGeneratorController extends ActionController
class SiteGeneratorController
{
    /**
     * The local configuration array
     *
     * @var array
     */
    protected array $conf = [];

    /**
     * The data transfer object form => wizard
     *
     * @var SiteGeneratorDto Could also be DTO defined by TS
     */
    protected SiteGeneratorDto $siteGeneratorDto;

    /**
     * Extension configuration
     *
     * @var array
     */
    protected array $extensionConfiguration = [];

    private array $settings;

    /**
     * The constructor of this class
     *
     * @param ModuleTemplateFactory $moduleTemplateFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigurationManagerInterface $configurationManager
     * @param SiteGeneratorWizard $siteGeneratorWizard
     * @param IconFactory $iconFactory
     * @param PageRenderer $pageRenderer
     */
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly ConfigurationManagerInterface $configurationManager,
        protected readonly SiteGeneratorWizard $siteGeneratorWizard,
        protected readonly IconFactory $iconFactory,
        private readonly PageRenderer $pageRenderer
    ) {
    }

    /**
     * @throws \ReflectionException
     * @throws \JsonException
     */
    protected function init(ServerRequestInterface $request): void
    {
        // Get translations
        $this->getLanguageService()->includeLLFile('EXT:site_generator/Resources/Private/Language/locallang.xlf');

        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'SiteGenerator');

        // Store DTO data from form
        $this->storeDtoData($request);

        $queryParams = $request->getQueryParams();
        $parsedBody = $request->getParsedBody();

        $this->conf['action'] = $parsedBody['action'] ?? $queryParams['action'] ?? null;
        $this->conf['returnurl'] = $parsedBody['returnurl'] ?? $queryParams['returnurl'] ?? null;

        // Add JS labels
        $this->pageRenderer->addInlineLanguageLabelArray([
            'alert' => $this->getLanguageService()->sL('LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:alert'),
            'mandatory_fields' => $this->getLanguageService()->sL('LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:allfieldsMandatory'),
            'ok' => $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_common.xlf:ok')
        ]);
    }

    /**
     * Store DTO Data from form
     *
     * @param ServerRequestInterface $request
     * @return void
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function storeDtoData(ServerRequestInterface $request): void
    {
        $queryParams = $request->getQueryParams();
        $parsedBody = $request->getParsedBody();

        // Retrieve data from fositeDtoSavedrm
        $parameters = $parsedBody['tx_sitegenerator'] ?? $queryParams['tx_sitegenerator'] ?? [];
        $siteDtoSaved = $parsedBody['siteDtoSaved'] ?? $queryParams['siteDtoSaved'] ?? [];

        if ($siteDtoSaved) {
            // Restore saved form data
            $this->siteGeneratorDto = unserialize(json_decode($siteDtoSaved, false, 512, JSON_THROW_ON_ERROR), [true]);
        } else {
            // Store form data in DTO
            $this->siteGeneratorDto = GeneralUtility::makeInstance($this->settings['siteGenerator']['wizard']['formDto']);

            // Load default values from extension configuration
            $this->siteGeneratorDto->setTitle($this->getExtensionConfiguration('homePageTitle'));

            if ($this->siteGeneratorDto instanceof SiteGeneratorDto) {
                $this->siteGeneratorDto->setGroupPrefix($this->getExtensionConfiguration('groupPrefix'));
                $this->siteGeneratorDto->setCommonMountPointUid((int)$this->getExtensionConfiguration('commonMountPointUid'));
                $this->siteGeneratorDto->setBaseFolderName($this->getExtensionConfiguration('baseFolderName'));
                $this->siteGeneratorDto->setSubFolderNames($this->getExtensionConfiguration('subFolderNames'));
            }
        }

        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $setter = 'set' . ucfirst($key);

                // Retrieve method parameter type
                $reflectionFunc = new \ReflectionMethod(get_class($this->siteGeneratorDto), $setter);
                $reflectionParams = $reflectionFunc->getParameters();

                if ($reflectionParams[0]->getType() && $reflectionParams[0]->getType()->getName()) {
                    \settype($value, $reflectionParams[0]->getType()->getName());
                }

                $this->siteGeneratorDto->$setter($value);
            }
        }
    }

    /**
     * Injects the request object for the current request and gathers all data
     *
     * @param ServerRequestInterface $request the current request
     * @param ?ResponseInterface $response (removed in V10)
     *
     * @return ResponseInterface the response with the content
     * @throws RouteNotFoundException|\Doctrine\DBAL\Exception
     */
    public function dispatch(ServerRequestInterface $request, ?ResponseInterface $response = null): ResponseInterface
    {
        // Initialisations
        $this->init($request);

        if ($response === null) {
            $response = new HtmlResponse('');
        }
        $response->withHeader('Content-Type', 'text/html; charset=utf-8');

        // The pid is mandatory
        if ($this->siteGeneratorDto->getPid() <= 0) {
            $response->getBody()->write('This script cannot be called directly');
            return $response->withStatus(500);
        }

        $content = '';
        switch ($this->conf['action']) {
            case 'get_data_first_step':
                $content = $this->getDataFirstStepAction($request);
                break;
            case 'get_data_second_step':
                $content = $this->getDataSecondStepAction($request);
                break;
            case 'generate_site':
                $content = $this->generateSiteAction($request);
                break;
        }

        // Write response
        return $content;
    }

    /**
     * Display a form to gather data (first step)
     *
     * @return ResponseInterface The rendered view
     * @throws RouteNotFoundException|\Doctrine\DBAL\Exception
     */
    protected function getDataFirstStepAction(ServerRequestInterface $request): ResponseInterface
    {
        /* @var $pagesRepository PagesRepository */
        $pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);
        $modelPages = $pagesRepository->getPages($this->getExtensionConfiguration('modelsPid'));

        $nextStep = $this->buildUriFromRoute('tx_wizard_sitegenerator');

        $viewVariables = [
            'moduleUrl' => $nextStep,
            'siteDto' => $this->siteGeneratorDto,
            'siteDtoSaved' => json_encode(serialize($this->siteGeneratorDto)),
            'modelPages' => $modelPages,
            'action' => ($this->getExtensionConfiguration('onlyOneFormPage') ? 'generate_site' : 'get_data_second_step'),
            'returnurl' => $this->conf['returnurl'],
            'rules' => '[{"type":"required"}]'
        ];

        // Add event to assign more variables to the view (useful when using your own template)
        $event = $this->eventDispatcher->dispatch(new BeforeRenderingFirstStepViewEvent($viewVariables));

        $view = $this->moduleTemplateFactory->create($request);
        $view->assignMultiple($event->getViewVariables());
        $view->setModuleName('');
        $this->addDocHeaderBackButton($view);

        return $view->renderResponse('GetDataFirstStep');
    }

    /**
     * Display a form to gather data (second step)
     *
     * @return ResponseInterface The rendered view
     * @throws RouteNotFoundException
     */
    protected function getDataSecondStepAction(ServerRequestInterface $request): ResponseInterface
    {
        $nextStep = $this->buildUriFromRoute('tx_wizard_sitegenerator');

        $viewVariables = [
            'moduleUrl' => $nextStep,
            'siteDto' => $this->siteGeneratorDto,
            'siteDtoSaved' => json_encode(serialize($this->siteGeneratorDto)),
            'action' => 'generate_site',
            'returnurl' => $this->conf['returnurl'],
        ];

        // Add event to assign more variables to the view (useful when using your own template)
        $event = $this->eventDispatcher->dispatch(new BeforeRenderingSecondStepViewEvent($viewVariables));

        $view = $this->moduleTemplateFactory->create($request);
        $view->assignMultiple($event->getViewVariables());
        $view->setModuleName('');

        return $view->renderResponse('GetDataSecondStep');
    }

    /**
     * Call wizard for new site generation
     *
     * @return ResponseInterface The rendered view
     */
    protected function generateSiteAction(ServerRequestInterface $request): ResponseInterface
    {
        $errorMessage = '';

        try {
            // Start the wizard
            $this->siteGeneratorWizard->startWizard($this->siteGeneratorDto);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $viewVariables = [
            'errorMessage' => $errorMessage,
            'sucessMessage' => $this->siteGeneratorWizard->getSiteData()->getMessage()
        ];

        // Update page tree
        BackendUtility::setUpdateSignal('updatePageTree');

        $view = $this->moduleTemplateFactory->create($request);
        $view->assignMultiple($viewVariables);
        $view->setModuleName('');

        return $view->renderResponse('GenerateSite');
    }

    /**
     * Get data from extension configuration
     *
     * @param string $name Name of data to retrieve from configuration
     *
     * @return string
     */
    public function getExtensionConfiguration(string $name): string
    {
        if (empty($this->extensionConfiguration)) {
            $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['site_generator'];
        }
        return ($this->extensionConfiguration[$name]);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Generate URI for a backend module
     *
     * @param string $name The name of the route
     * @param array $parameters
     * @return string
     * @throws RouteNotFoundException
     */
    public function buildUriFromRoute(string $name, array $parameters = []): string
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (((string)$uriBuilder->buildUriFromRoute($name, $parameters)));
    }

    protected function addDocHeaderBackButton(ModuleTemplate $view): void
    {
        $lang = $this->getLanguageService();
        $gobackLabel = 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.goBack';

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();

        $viewButton = $buttonBar->makeLinkButton()
            ->setHref($this->conf['returnurl'])
            ->setTitle($lang->sL($gobackLabel))
            ->setIcon($this->iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL));
        $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 10);
    }
}
