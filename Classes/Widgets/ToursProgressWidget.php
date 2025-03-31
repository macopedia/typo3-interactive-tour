<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Widgets;

use Macopedia\Typo3InteractiveTour\Service\TourManager;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

final class ToursProgressWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly TourManager $tourManager,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        private readonly array $options = []
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request);
        $view->assignMultiple([
            'toursCount' => count($this->tourManager->getAllAccessibleTours()),
            'toursCompletedCount' => $this->tourManager->countCompletedTours(),
            'currentProgress' => ceil($this->tourManager->countCompletedTours() / count($this->tourManager->getAllAccessibleTours()) * 100),
            'button' => $this->isGuideModuleAccessible() ? $this->buttonProvider : null,
            'configuration' => $this->configuration,
        ]);
        return $view->render('Widget/ToursProgressWidget');
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    private function isGuideModuleAccessible(): bool
    {
        return $this->getBackendUser()->isAdmin() || $this->getBackendUser()->check('modules', 'help_guide');
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
