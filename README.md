# TYPO3 Interactive Backend Tours

## Introduction

This TYPO3 extension provides an interactive guide for new and existing backend users, offering step-by-step tours to familiarize them with the TYPO3 interface quickly.\
It originated as a community-selected project in the Community Budget Ideas Q1 2025 initiative.

## Main Goals

* Help users (developers, integrators, editors) quickly learn TYPO3 backend functionalities
* Allow extension developers to easily define their own interactive tours

## Key Features

| Feature                          | Description                                                                                                                |
|----------------------------------|----------------------------------------------------------------------------------------------------------------------------|
| Interactive Tours                | Short, guided tours through different TYPO3 backend modules and features.                                                 |
| YAML-Based Configuration         | Easy setup of tours and steps through YAML files (`Configuration/Tours`).                                                  |
| Standalone and Module-Bound Tours| Tours can run automatically or when users access specific modules.                                                         |
| Extensible                       | Developers can define custom tours for their own extensions.                                                              |
| Backend Module                   | Manage tours, view progress, manually start tours, and disable automatic tour launches through the Guide module (accessible via the Help group). |
| Dashboard Widget                 | Quickly view tour completion progress and access the Guide module.                                                         |


## Integration

Uses [Driver.js](https://driverjs.com) as a core library for rendering tooltips and tour popups.\
Custom solutions were developed to handle TYPO3's iframe-based module rendering. (Special thanks to Mateusz Gdula, Macopedia!)

## Usage

First, install the extension via Composer:

```bash
composer req macopedia/typo3-interactive-tour
```

This single step should be sufficient to get the extension up and running.

After installation, tours will start automatically when the related modules are accessed (provided that any defined tour permissions are met).\
The list of all available tours can be found in the `Guide` module, accessible via the `Help` section in the top bar.\
Please note that you must have the appropriate access permissions to view this module.

## Creating Your Own Tours

This package allows you to create interactive tours for the TYPO3 backend by defining them in YAML files.\
To create a custom tour, simply add a YAML file in your package's `Configuration/Tours/` directory with a structure similar to the example below:

```yaml
identifier: vendor/sampleTour
title: "LLL:EXT:vendor-sample-tour/Resources/Private/Language/locallang.xlf:tour.vendor.sampleTour.title"
description: "LLL:EXT:vendor-sample-tour/Resources/Private/Language/locallang.xlf:tour.vendor.sampleTour.description"
moduleName: "web_layout"
isStandalone: false
steps:
  - title: "Welcome"
    content: "Welcome to the TYPO3 backend tour. This step introduces the main interface."
    target: "#welcome-section"
    side: "top"
    align: "center"
    
  - title: "LLL:EXT:vendor-sample-tour/Resources/Private/Language/locallang.xlf:tour.vendor.sampleTour.step2.title"
    content: "LLL:EXT:vendor-sample-tour/Resources/Private/Language/locallang.xlf:tour.vendor.sampleTour.step2.content"
    target: ".navigation-menu"
    side: "right"
    align: "center"
    events:
      onShow:
        - event: "click"
          target: ".nav-item"
          delayAfter: 200      
      onExit:
        - event: "click"
          target: ".nav-item"
```

Define one tour per YAML file.\
The tour `identifier` should be unique and follow the format `vendor/tourName`.\
For a list of all available tour, step and event options, please refer to the documentation.

## Documentation

Detailed documentation covers configuration options, tour setup, and usage guidance.\
It is available in the documentation section of this repository.

## Contributing

We welcome contributions to this project! 

If you have ideas, bug fixes, or enhancements, please submit a pull request or open an issue on the GitHub repository.\

If you would like to help to translate the extension into your language, you can either:
- Create a pull request with your translation files in the `Resources/Private/Language/` directory
- Visit our project on [Crowdin](https://crowdin.com/project/typo3-extension-interactive-to) and contribute translations there

## Known Issues

Determining the relevance of tour steps based on user permissions and visibility is challenging due to TYPO3's flexibility.\
A deeper integration and permission-checking logic may be needed, pending discussions with TYPO3 core developers.\
More explicit element identifiers would simplify tour creation and reliability.

## Future Plans

We aim to integrate this functionality into TYPO3 core or an official TYPO3 vendor extension.

## Special Thanks

Thank you to the TYPO3 community for supporting this initiative through the Community Budget Ideas Q1 2025, and to everyone who provided invaluable feedback and assistance!

**Enjoy creating your interactive TYPO3 backend tours!**
