<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Service;

use Macopedia\Typo3InteractiveTour\Guide\Tour;
use Macopedia\Typo3InteractiveTour\Guide\TourFactory;
use Macopedia\Typo3InteractiveTour\Storage\TourStorageInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Tour Manager should be used to manage tours for backend users.
 */
readonly class TourManager
{
    public function __construct(
        private TourCollector $tourCollector,
        private TourFactory $tourFactory,
        private TourStorageInterface $tourStorage
    ) {}

    public function isExistingTour(string $tourIdentifier): bool
    {
        return $this->tourCollector->isTourConfigured($tourIdentifier);
    }

    public function getTour(string $tourIdentifier): Tour
    {
        $tourDefinition = $this->tourCollector->getTourDefinitionByIdentifier($tourIdentifier);
        return $this->tourFactory->createFromDefinition($tourDefinition);
    }

    /**
     * @return array<Tour>
     */
    public function getAllAccessibleTours(): array
    {
        $tours = $this->tourCollector->getToursAccessibleByUser();
        $allTours = [];
        foreach ($tours as $tourDefinition) {
            $allTours[] = $this->tourFactory->createFromDefinition($tourDefinition);
        }

        return $allTours;
    }

    public function setCurrentStepForTour(Tour $tour, int $currentStep): void
    {
        $this->tourStorage->setCurrentStepForTour($tour, $currentStep);
    }

    public function setTourAsEnabled(Tour $tour): void
    {
        $this->tourStorage->setTourAsEnabled($tour);
    }

    public function setTourAsDisabled(Tour $tour): void
    {
        $this->tourStorage->setTourAsDisabled($tour);
    }

    public function markTourAsCompleted(Tour $tour): void
    {
        $this->tourStorage->markTourAsCompleted($tour);
    }

    public function markTourAsNotCompleted(Tour $tour): void
    {
        $this->tourStorage->markTourAsNotCompleted($tour);
    }

    public function countCompletedTours(): int
    {
        $accessibleTours = array_map(static function (Tour $tour) {
            return $tour->getIdentifier();
        }, $this->getAllAccessibleTours());

        $userTours = $this->getBackendUser()->uc['tours'] ?? [];
        return count(array_filter($userTours, static function (array $tourData, string $tourIdentifier) use ($accessibleTours) {
            return ($tourData['completed'] ?? false) === true && in_array($tourIdentifier, $accessibleTours, true);
        }, ARRAY_FILTER_USE_BOTH));
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
