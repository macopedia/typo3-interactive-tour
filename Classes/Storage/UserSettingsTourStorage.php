<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Storage;

use Macopedia\Typo3InteractiveTour\Guide\Tour;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class UserSettingsTourStorage implements TourStorageInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getTourConfiguration(string $tourIdentifier): array
    {
        $backendUser = $this->getBackendUserAuthentication();
        return $backendUser->uc['tours'][$tourIdentifier] ?? [];
    }

    public function setCurrentStepForTour(Tour $tour, int $currentStep): void
    {
        $backendUser = $this->getBackendUserAuthentication();
        $backendUser->uc['tours'][$tour->getIdentifier()]['currentStep'] = $currentStep;
        $backendUser->writeUC();
    }

    public function markTourAsCompleted(Tour $tour): void
    {
        $backendUser = $this->getBackendUserAuthentication();
        $backendUser->uc['tours'][$tour->getIdentifier()]['completed'] = true;
        $backendUser->uc['tours'][$tour->getIdentifier()]['currentStep'] = count($tour->getSteps());
        $backendUser->writeUC();
    }

    public function markTourAsNotCompleted(Tour $tour): void
    {
        $backendUser = $this->getBackendUserAuthentication();
        unset($backendUser->uc['tours'][$tour->getIdentifier()]['completed']);
        $backendUser->writeUC();
    }

    public function setTourAsEnabled(Tour $tour): void
    {
        $backendUser = $this->getBackendUserAuthentication();
        unset($backendUser->uc['tours'][$tour->getIdentifier()]['disabled']);
        $backendUser->writeUC();
    }

    public function setTourAsDisabled(Tour $tour): void
    {
        $backendUser = $this->getBackendUserAuthentication();
        $backendUser->uc['tours'][$tour->getIdentifier()]['disabled'] = true;
        $backendUser->writeUC();
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
