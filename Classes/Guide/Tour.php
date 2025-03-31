<?php

declare(strict_types=1);

/*
 * This file is part of the "guide" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

namespace Macopedia\Typo3InteractiveTour\Guide;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * This class serves as DTO for a tour
 */
final class Tour
{
    protected int $currentStep = 0;
    protected bool $isDisabled = false;
    protected bool $isCompleted = false;

    public function __construct(
        protected string $identifier,
        protected string $title,
        protected string $description,
        protected array $permissions = [],
        protected ?string $moduleName = null,
        protected ?string $moduleRoute = null,
        protected bool $isStandalone = false,
        protected ?string $skipButtonText = null,
        protected ?string $previousButtonText = null,
        protected ?string $nextButtonText = null,
        protected ?string $finishButtonText = null,
        protected ?string $nextTourIdentifier = null,
        protected array $steps = [],
    ) {
        $this->setUserContextProperties();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function getModuleRoute(): ?string
    {
        return $this->moduleRoute;
    }

    public function getCurrentStep(): int
    {
        return $this->currentStep;
    }

    public function getIsDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function getIsCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function getSkipButtonText(): ?string
    {
        return $this->skipButtonText;
    }

    public function getPreviousButtonText(): ?string
    {
        return $this->previousButtonText;
    }

    public function getNextButtonText(): ?string
    {
        return $this->nextButtonText;
    }

    public function getFinishButtonText(): ?string
    {
        return $this->finishButtonText;
    }

    public function getNextTourIdentifier(): ?string
    {
        return $this->nextTourIdentifier;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn(mixed $value) => $value !== null && $value !== []);
    }

    public static function __set_state(array $state): self
    {
        return new self(...$state);
    }

    private function setUserContextProperties(): void
    {
        $userTourSettings = $this->getBackendUser()->uc['tours'][$this->identifier] ?? [];
        $this->currentStep = (int)($userTourSettings['currentStep'] ?? 0);
        $this->isDisabled = (bool)($userTourSettings['disabled'] ?? false);
        $this->isCompleted = (bool)($userTourSettings['completed'] ?? false);
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
