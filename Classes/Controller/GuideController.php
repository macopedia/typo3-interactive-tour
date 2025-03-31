<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Controller;

use Macopedia\Typo3InteractiveTour\Service\TourManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\JsonResponse;

#[AsController]
class GuideController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly TourManager $tourManager
    ) {}

    public function overview(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $view->assignMultiple([
            'tours' => $this->tourManager->getAllAccessibleTours(),
            'toursEnabled' => ((bool)($this->getBackendUserAuthentication()->uc['disableTours'] ?? false) === false),
        ]);
        return $view->renderResponse('Guide/Overview');
    }

    public function enableTours(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return new JsonResponse(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $backendUser = $this->getBackendUserAuthentication();
        unset($backendUser->uc['disableTours']);
        $backendUser->writeUC();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function disableTours(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return new JsonResponse(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $backendUser = $this->getBackendUserAuthentication();
        $backendUser->uc['disableTours'] = true;
        $backendUser->writeUC();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
