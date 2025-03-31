<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Macopedia\Typo3InteractiveTour\Controller\GuideController;
use Macopedia\Typo3InteractiveTour\Controller\TourController;

return [
    'guide_tours_enable' => [
        'path' => '/guide/tours/enable',
        'target' => GuideController::class . '::enableTours',
        'methods' => ['POST'],
    ],
    'guide_tours_disable' => [
        'path' => '/guide/tours/disable',
        'target' => GuideController::class . '::disableTours',
        'methods' => ['POST'],
    ],
    'guide_tour_get' => [
        'path' => '/guide/tour/get',
        'target' => TourController::class . '::get',
    ],
    'guide_tour_get_all' => [
        'path' => '/guide/tour/get-all',
        'target' => TourController::class . '::getAll',
    ],
    'guide_tour_set_current_step' => [
        'path' => '/guide/tour/set-current-step',
        'target' => TourController::class . '::setCurrentStep',
        'methods' => ['POST'],
    ],
    'guide_tour_complete' => [
        'path' => '/guide/tour/complete',
        'target' => TourController::class . '::complete',
        'methods' => ['POST'],
    ],
    'guide_tour_restart' => [
        'path' => '/guide/tour/restart',
        'target' => TourController::class . '::restart',
        'methods' => ['POST'],
    ],
    'guide_tour_disable' => [
        'path' => '/guide/tour/disable',
        'target' => TourController::class . '::disable',
        'methods' => ['POST'],
    ],
];
