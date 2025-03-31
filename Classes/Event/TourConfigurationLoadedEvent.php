<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Event;

/**
 * Event after a tour configuration has been read from a yaml file
 * before it is cached - allows dynamic modification of the tour's configuration.
 */
final class TourConfigurationLoadedEvent
{
    public function __construct(
        protected string $siteIdentifier,
        protected array $configuration
    ) {}

    public function getTourIdentifier(): string
    {
        return $this->siteIdentifier;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration Overwrite the configuration array of the tour
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
