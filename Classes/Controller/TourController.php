<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Controller;

use Macopedia\Typo3InteractiveTour\Event\AfterTourCompletedEvent;
use Macopedia\Typo3InteractiveTour\Event\AfterTourRestartEvent;
use Macopedia\Typo3InteractiveTour\Event\BeforeTourCompletedEvent;
use Macopedia\Typo3InteractiveTour\Event\BeforeTourRestartEvent;
use Macopedia\Typo3InteractiveTour\Service\TourManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Core\Http\JsonResponse;

#[AsController]
class TourController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TourManager $tourManager
    ) {}

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $tourIdentifier = $request->getQueryParams()['tourIdentifier'] ?? null;
        if (!$tourIdentifier) {
            return new JsonResponse(['error' => 'Tour identifier is missing.'], Response::HTTP_BAD_REQUEST);
        }
        if ($this->tourManager->isExistingTour($tourIdentifier) === false) {
            return new JsonResponse(['error' => sprintf('Tour with identifier "%s" is not configured.', $tourIdentifier)], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($this->tourManager->getTour($tourIdentifier)->toArray());
    }

    public function getAll(ServerRequestInterface $request): ResponseInterface
    {
        $allTours = $this->tourManager->getAllAccessibleTours();
        $tours = [];
        foreach ($allTours as $tour) {
            $tours[] = $tour->toArray();
        }

        return new JsonResponse($tours);
    }

    public function setCurrentStep(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return new JsonResponse(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $tourIdentifier = $request->getParsedBody()['tourIdentifier'] ?? null;
        $step = $request->getParsedBody()['step'] ?? null;

        if (!$tourIdentifier || !isset($step)) {
            return new JsonResponse(['error' => 'Tour identifier or step is missing.'], Response::HTTP_BAD_REQUEST);
        }
        if ($this->tourManager->isExistingTour($tourIdentifier) === false) {
            return new JsonResponse(['error' => sprintf('Tour with identifier "%s" is not configured.', $tourIdentifier)], Response::HTTP_BAD_REQUEST);
        }

        $stepInt = (int)$step;
        $tour = $this->tourManager->getTour($tourIdentifier);

        if ($stepInt < 1 || $stepInt > count($tour->getSteps())) {
            return new JsonResponse(['error' => sprintf('Invalid tour step provided. Step "%d" for tour "%s" is not defined.', $step, $tourIdentifier)], Response::HTTP_BAD_REQUEST);
        }
        $this->tourManager->setCurrentStepForTour($tour, $stepInt);
        // if it is the last step, mark the tour as completed
        // otherwise mark it as not completed as the user might
        // click on a previous step
        if (count($tour->getSteps()) === $stepInt) {
            $this->tourManager->markTourAsCompleted($tour);
        } else {
            $this->tourManager->markTourAsNotCompleted($tour);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function complete(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return new JsonResponse(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $tourIdentifier = (string)($request->getParsedBody()['tourIdentifier'] ?? '');
        if ($this->tourManager->isExistingTour($tourIdentifier) === false) {
            return new JsonResponse(['error' => sprintf('Tour with identifier "%s" not configured.', $tourIdentifier)], Response::HTTP_BAD_REQUEST);
        }

        $tour = $this->tourManager->getTour($tourIdentifier);
        $this->eventDispatcher->dispatch(new BeforeTourCompletedEvent($tour));
        $this->tourManager->markTourAsCompleted($tour);
        $this->eventDispatcher->dispatch(new AfterTourCompletedEvent($tour));

        return new JsonResponse($this->tourManager->getTour($tourIdentifier)->toArray());
    }

    public function restart(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return new JsonResponse(null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $tourIdentifier = (string)($request->getParsedBody()['tourIdentifier'] ?? '');
        if ($this->tourManager->isExistingTour($tourIdentifier) === false) {
            return new JsonResponse(['error' => sprintf('Tour with identifier "%s" not configured.', $tourIdentifier)], Response::HTTP_BAD_REQUEST);
        }

        $tour = $this->tourManager->getTour($tourIdentifier);
        $this->eventDispatcher->dispatch(new BeforeTourRestartEvent($tour));
        $this->tourManager->markTourAsNotCompleted($tour);
        $this->tourManager->setCurrentStepForTour($tour, 0);
        $this->eventDispatcher->dispatch(new AfterTourRestartEvent($tour));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function disable(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        $tourIdentifier = $request->getParsedBody()['tourIdentifier'] ?? null;
        $tour = $this->tourManager->getTour($tourIdentifier);
        $this->tourManager->setTourAsDisabled($tour);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
