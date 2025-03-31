:navigation-title: Introduction

..  _introduction:

============
Introduction
============

Welcome to the **TYPO3 Interactive Tour** extension documentation.
This extension was created as a initiative selected by the community through the *Community Budget Ideas 2025 Q1* (you can read more about it `here <https://talk.typo3.org/t/interactive-guide-tour-to-typo3-backend/5996>`_).

The main goal of this extension is to provide an interactive tour through the TYPO3 backend, to help users get familiar with the backend and its features.

..  _main-concepts:

Main Concepts
=============

To get started, let's introduce the main concepts of the extension.

..  _guide-tours:

Guide and Tours
---------------

The main concept is to provide a guide that offers users tours they can follow to become familiar with the TYPO3 backend.
We have then two main names to remember:

*   **Guide**: The *Guide* module is a dedicated section in the TYPO3 backend that allows users to manage tours and track their progress.
    To view this module, the user must be granted access.
    The main purpose of the *Guide* module is to provide an overview of all available tours and their progress.
    You can read more about it in the chapter :ref:`backend-module`.

*   **Tour**: A tour is a sequence of steps that guides the user through the TYPO3 backend.
    Each step introduces a specific part of the interface or asks the user to perform a simple task.
    Users can navigate through the steps using the *Next* and *Previous* buttons.
    Tours can be started, continued, reset, and progress can be tracked.
    You can read more about tours in the chapter :ref:`tour-configuration`.

.. _extensibility:

Extensibility
-------------

The extension is designed to be easily extensible, allowing the community to create new tours and share them with others.
This way, extension authors can create tours specifically designed for their custom modules, helping users become familiar with them more easily.

You can read more about how to create your own tours in the chapter :ref:`tour-configuration`.