<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\EventListener;

use TYPO3\CMS\Backend\Controller\Event\AfterBackendPageRenderEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Page\PageRenderer;

final readonly class AfterBackendPageRenderEventListener
{
    public function __construct(private PageRenderer $pageRenderer) {}

    #[AsEventListener(event: AfterBackendPageRenderEvent::class)]
    public function __invoke(): void
    {
        $backendUser = $this->getBackendUser();
        $disableTours = (bool)($backendUser->uc['disableTours'] ?? false);

        $javaScriptRenderer = $this->pageRenderer->getJavaScriptRenderer();
        $javaScriptRenderer->addGlobalAssignment([
            'TYPO3' => [
                'configuration' => [
                    'disableTours' => $disableTours,
                ],
            ],
        ]);

        if ($disableTours === true) {
            return;
        }

        $this->pageRenderer->addInlineLanguageLabel('guide.buttonSkip', $this->getLanguageService()->sL('LLL:EXT:typo3-interactive-tour/Resources/Private/Language/locallang.xlf:buttonSkip'));
        $this->pageRenderer->addInlineLanguageLabel('guide.buttonPrevious', $this->getLanguageService()->sL('LLL:EXT:typo3-interactive-tour/Resources/Private/Language/locallang.xlf:buttonPrevious'));
        $this->pageRenderer->addInlineLanguageLabel('guide.buttonNext', $this->getLanguageService()->sL('LLL:EXT:typo3-interactive-tour/Resources/Private/Language/locallang.xlf:buttonNext'));
        $this->pageRenderer->addInlineLanguageLabel('guide.buttonFinish', $this->getLanguageService()->sL('LLL:EXT:typo3-interactive-tour/Resources/Private/Language/locallang.xlf:buttonFinish'));

        $javaScriptRenderer->addJavaScriptModuleInstruction(
            JavaScriptModuleInstruction::create('@macopedia/typo3-interactive-tour/guide.js')
        );
        $this->pageRenderer->addCssFile('EXT:typo3-interactive-tour/Resources/Public/StyleSheets/Guide.css');
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
