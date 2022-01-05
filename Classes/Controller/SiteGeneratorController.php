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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use Oktopuce\SiteGenerator\Wizard\SiteGeneratorWizard;
use Oktopuce\SiteGenerator\Domain\Repository\PagesRepository;
use Oktopuce\SiteGenerator\Dto\SiteGeneratorDto;
use Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingFirstStepViewEvent;
use Oktopuce\SiteGenerator\Wizard\Event\BeforeRenderingSecondStepViewEvent;

/**
 * SiteGeneratorController
 */
class SiteGeneratorController extends ActionController
{
    /**
     * @var IconFactory
     */
    protected IconFactory $iconFactory;

    /**
     * @var ModuleTemplate
     */
    protected ModuleTemplate $moduleTemplate;

    /**
     * @var ButtonBar
     */
    protected ButtonBar $buttonBar;

    /**
     * @var StandaloneView
     */
    protected StandaloneView $standaloneView;

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
    protected $siteGeneratorDto;

    /**
     * Extension configuration
     *
     * @var array
     */
    protected array $extensionConfiguration = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var SiteGeneratorWizard
     */
    protected SiteGeneratorWizard $siteGeneratorWizard;

    /**
     * The constructor of this class
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigurationManagerInterface $configurationManager
     * @param SiteGeneratorWizard $siteGeneratorWizard
     * @param ModuleTemplate $moduleTemplate
     * @throws \ReflectionException
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ConfigurationManagerInterface $configurationManager,
        SiteGeneratorWizard $siteGeneratorWizard,
        ModuleTemplate $moduleTemplate
    ) {
        // Dependency injection
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationManager = $configurationManager;
        $this->siteGeneratorWizard = $siteGeneratorWizard;
        $this->moduleTemplate = $moduleTemplate;

        // Get translations
        $this->getLanguageService()->includeLLFile('EXT:site_generator/Resources/Private/Language/locallang.xlf');

        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'SiteGenerator');

        // Store DTO data from form
        $this->storeDtoData();

        $this->conf['action'] = GeneralUtility::_GP('action');
        $this->conf['returnurl'] = GeneralUtility::_GP('returnurl');

        // Initialize module template
//        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->iconFactory = $this->moduleTemplate->getIconFactory();
        $this->buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        // Load JS for context menu and site generator validation
        $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');
        $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/SiteGenerator/SiteGeneratorForm');

        // Add JS labels
        $this->moduleTemplate->getPageRenderer()->addInlineLanguageLabelArray([
            'alert' => $this->getLanguageService()->sL('LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:alert'),
            'mandatory_fields' => $this->getLanguageService()->sL('LLL:EXT:site_generator/Resources/Private/Language/backend.xlf:allfieldsMandatory'),
            'ok' => $this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_common.xlf:ok')
        ]);

        // Standalone view initialisation
        $this->initStandaloneView();
    }

    /**
     * Store DTO Data from form
     *
     * @return void
     * @throws \ReflectionException
     */
    public function storeDtoData(): void
    {
        // Retrieve data from form
        $parameters = GeneralUtility::_GP('tx_sitegenerator');
        $siteDtoSaved = GeneralUtility::_GP('siteDtoSaved');

        if ($siteDtoSaved) {
            // Restore saved form data
            $this->siteGeneratorDto = unserialize(json_decode($siteDtoSaved));
        } else {
            // Store form data in DTO
            $this->siteGeneratorDto = GeneralUtility::makeInstance($this->settings['siteGenerator']['wizard']['formDto']);

            // Load default values from extension configuration
            $this->siteGeneratorDto->setTitle($this->getExtensionConfiguration('homePageTitle'));

            if ($this->siteGeneratorDto instanceof SiteGeneratorDto) {
                $this->siteGeneratorDto->setGroupPrefix($this->getExtensionConfiguration('groupPrefix'));
                $this->siteGeneratorDto->setCommonMountPointUid((int) $this->getExtensionConfiguration('commonMountPointUid'));
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
     * Initialise Standalone view
     *
     * @return void
     */
    public function initStandaloneView(): void
    {
        // Get template paths
        $fullTS = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT, 'SiteGenerator');
        $templateRootPaths = $fullTS['module.']['tx_sitegenerator.']['view.']['templateRootPaths.'];
        $partialRootPaths = $fullTS['module.']['tx_sitegenerator.']['view.']['partialRootPaths.'];
        $layoutRootPaths = $fullTS['module.']['tx_sitegenerator.']['view.']['layoutRootPaths.'];

        // Generate Standalone view
        /* @var $this->standaloneView StandaloneView */
        $this->standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->standaloneView->setTemplateRootPaths($templateRootPaths);
        $this->standaloneView->setLayoutRootPaths($layoutRootPaths);
        $this->standaloneView->setPartialRootPaths($partialRootPaths);
        $this->standaloneView->getRequest()->setControllerExtensionName('site_generator');
        $this->standaloneView->getRequest()->setControllerName('SiteGenerator');
        $this->standaloneView->assign('settings', $this->settings);

        $renderingContext = $this->standaloneView->getRenderingContext();
        $renderingContext->setControllerName('SiteGenerator');
        $this->standaloneView->setRenderingContext($renderingContext);
    }

    /**
     * Injects the request object for the current request and gathers all data
     *
     * @param ServerRequestInterface $request the current request
     * @param ?ResponseInterface $response (removed in V10)
     *
     * @return ResponseInterface the response with the content
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function dispatch(ServerRequestInterface $request, ?ResponseInterface $response = null): ResponseInterface
    {
        if ($response === null) {
            $response = new HtmlResponse('');
        }
        $response->withHeader('Content-Type', 'text/html; charset=utf-8');

        // The pid is mandatory
        if ($this->siteGeneratorDto->getPid() <= 0) {
            $response->getBody()->write('This script cannot be called directly');
            return $response->withStatus(500);
        }

        // Add doc header buttons
        $this->addbuttons();

        $content = '';
        switch ($this->conf['action']) {
            case 'get_data_first_step':
                $content = $this->getDataFirstStepAction();
                break;
            case 'get_data_second_step':
                $content = $this->getDataSecondStepAction();
                break;
            case 'generate_site':
                $content = $this->generateSiteAction();
                break;
        }

        // Write response
        $response->getBody()->write($content);

        return $response;
    }

    /**
     * Add doc header buttons
     *
     * @return void
     */
    protected function addbuttons(): void
    {
        $lang = $this->getLanguageService();
        $gobackLabel = 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.goBack';

        if ($this->conf['returnurl']) {
            $returnButton = $this->buttonBar->makeLinkButton()
                ->setHref($this->conf['returnurl'])
                ->setTitle($lang->sL($gobackLabel))
                ->setIcon($this->moduleTemplate->getIconFactory()->getIcon('actions-view-go-back', Icon::SIZE_SMALL));
            $this->buttonBar->addButton($returnButton, ButtonBar::BUTTON_POSITION_LEFT, 10);
        }
    }

    /**
     * Display a form to gather data (first step)
     *
     * @return string The rendered view
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function getDataFirstStepAction(): string
    {
        /* @var $pagesRepository PagesRepository */
        $pagesRepository = GeneralUtility::makeInstance(PagesRepository::class);
        $modelPages = $pagesRepository->getPages($this->getExtensionConfiguration('modelsPid'));

        $nextStep = $this->buildUriFromRoute('wizard_sitegenerator');

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

        $this->standaloneView->assignMultiple($event->getViewVariables());

        $this->setTemplateName('GetDataFirstStep.html');
        $this->moduleTemplate->setContent($this->standaloneView->render());

        return ($this->moduleTemplate->renderContent());
    }

    /**
     * Display a form to gather data (second step)
     *
     * @return string The rendered view
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function getDataSecondStepAction(): string
    {
        $nextStep = $this->buildUriFromRoute('wizard_sitegenerator');

        $viewVariables = [
            'moduleUrl' => $nextStep,
            'siteDto' => $this->siteGeneratorDto,
            'siteDtoSaved' => json_encode(serialize($this->siteGeneratorDto)),
            'action' => 'generate_site',
            'returnurl' => $this->conf['returnurl'],
        ];

        // Add event to assign more variables to the view (useful when using your own template)
        $event = $this->eventDispatcher->dispatch(new BeforeRenderingSecondStepViewEvent($viewVariables));

        $this->standaloneView->assignMultiple($event->getViewVariables());

        $this->setTemplateName('GetDataSecondStep.html');
        $this->moduleTemplate->setContent($this->standaloneView->render());

        return ($this->moduleTemplate->renderContent());
    }

    /**
     * Call wizard for new site generation
     *
     * @return string The rendered view
     */
    protected function generateSiteAction(): string
    {
        $errorMessage = '';

        try {
            // Start the wizard
            $this->siteGeneratorWizard->startWizard($this->siteGeneratorDto);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $this->standaloneView->assignMultiple([
            'errorMessage' => $errorMessage,
            'sucessMessage' => $this->siteGeneratorWizard->getSiteData()->getMessage()
        ]);

        $this->setTemplateName('GenerateSite.html');
        $this->moduleTemplate->setContent($this->standaloneView->render());

        // Update page tree
        BackendUtility::setUpdateSignal('updatePageTree') .

            // Add JS for refreshing tree node
            $content = $this->moduleTemplate->renderContent();

        return ($content);
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
     * Set template name for view
     *
     * @param string $templateName
     *
     * @return void
     */
    public function setTemplateName(string $templateName): void
    {
        try {
            $this->standaloneView->setTemplate($templateName);
        } catch (InvalidTemplateResourceException $e) {
            // no template $templateName found in given $templatePaths
            exit($e->getMessage());
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected function getLanguageService(): \TYPO3\CMS\Core\Localization\LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Generate URI for a backend module
     *
     * @param string $name The name of the route
     *
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function buildUriFromRoute(string $name): string
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (((string)$uriBuilder->buildUriFromRoute($name)));
    }
}
