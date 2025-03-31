# TYPO3 Interactive Backend Tours

## Introduction

This TYPO3 extension provides an interactive guide for new and existing backend users, offering step-by-step tours to familiarize them with the TYPO3 interface quickly.\
It originated as a community-selected project in the Community Budget Ideas Q1 2025 initiative.

## Goals

Help users (developers, integrators, editors) quickly learn TYPO3 backend functionalities.\
Allow extension developers to easily define their own interactive tours.\
Provide clear, short explanations directly within the TYPO3 backend.\

## Key Features

### Interactive Tours

Short, guided tours through different TYPO3 backend modules and features.

### YAML-Based Configuration

Easy setup of tours and steps through YAML files (`Configuration/Tours`).

### Standalone and Module-Bound Tours

Tours can run automatically or when users access specific modules.

### Extensible

Developers can define custom tours for their own extensions.

### Backend Module

Manage tours, view progress, manually start tours, and disable automatic tour launches through the Guide module (accessible via the Help group).

### Dashboard Widget

Quickly view tour completion progress and access the Guide module.

## Integration

Uses Driver.js as a core library for rendering tooltips and tour popups.\
Custom solutions were developed to handle TYPO3's iframe-based module rendering. (Special thanks to Mateusz Gdula, Macopedia!)

## Documentation

Detailed documentation covers configuration options, tour setup, and usage guidance.\
It is available in the documentation section of this repository.

## Testing and Feedback

We encourage community testing, feedback, and contributions to enhance the extension further.\
Report issues and suggest improvements via the GitHub issue tracker.

## Known Issues

Determining the relevance of tour steps based on user permissions and visibility is challenging due to TYPO3's flexibility.\
A deeper integration and permission-checking logic may be needed, pending discussions with TYPO3 core developers.\
More explicit element identifiers would simplify tour creation and reliability.

## Future Plans

We aim to integrate this functionality into TYPO3 core or an official TYPO3 vendor extension.

## Special Thanks

Thank you to the TYPO3 community for supporting this initiative through the Community Budget Ideas Q1 2025, and to everyone who provided invaluable feedback and assistance!

**Enjoy creating your interactive TYPO3 backend tours!**