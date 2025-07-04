:navigation-title: Tour Configuration

..  _tour-configuration:

==================
Tour Configuration
==================

The entire configuration for tours is stored in `YAML` files.
Each TYPO3 package is scanned to check if it contains a `.yaml` file inside the `Configuration/Tours/` folder.
It can be one of more files, and they all will be processed.
The file must follow the structure described in the next sections.

..  _configure-tour:

Configure tour
=========================================

Each tour should be configured in a separate YAML file.
The following properties are available:

..  confval-menu::
    :display: table
    :name: tour-definition
    :type:
    :required:

    ..  confval:: identifier
        :type: string
        :name: tour-definition-identifier
        :required: true

        | Unique identifier for the tour. Use the format `vendor/tourName`.
        | The typo3 vendor is reserved for core tours.
        | It is recommended to use the name of the backend module (if applicable) as the tour name.
        | Examples: `typo3/webLayout` or `vendor/webNewsManagement`.

    ..  confval:: title
        :type: string
        :name: tour-definition-title
        :required: true

        Title of the tour. It will be displayed in the :ref:`backend-module`, where the user can see all available tours.

    ..  confval:: description
        :type: string
        :name: tour-definition-description

        Short description of the tour. It will be displayed in the :ref:`backend-module`, where the user can see all available tours.

    ..  confval:: permission
        :type: array
        :name: tour-definition-permissions

        | Permissions required to access the tour.
        | The user must have all the listed permissions to see the tour.
        | The permissions are checked against the backend user and groups.
        | If the user does not have the required permissions, the tour will not be displayed.
        |
        | Allowed permissions:

        ..  code-block:: yaml
            modules: []
            pagetypesSelect: []
            tablesModify: []
            tablesSelect: []
            nonExcludeFields: []
            explicitAllowdeny: []
            filePermissions: []
            availableWidgets: []

        ..  note::
            Not all of those permissions will make sens to be used, but we support them to be consistent with the TYPO3 backend.

    ..  confval:: moduleName
        :type: string
        :name: tour-definition-moduleName

        | Module name to which the tour is bound. This is used to display the tour only when the user is in the corresponding module.
        | This should be a real module name as defined in the TYPO3 backend. Check the `Configuration` -> `Backend modules` module, to see the list of available modules.
        | Examples: `web_layout`, `web_list`, `file` etc.

        ..  note::
            This module name is used to check if the user has access to the module. Read more about tour access in the chapter :ref:`tour-access`.

    ..  confval:: isStandalone
        :type: bool
        :name: tour-definition-isStandalone

        | Indicates whether the tour is standalone (i.e., a single step not bound to any element).
        | Such tours are rendered automatically when the user logs in to the backend (they should not be bound to any module).
        | Their purpose is to provide general information, welcome messages, or similar content.

    ..  confval:: skipButtonText
        :type: string
        :name: tour-definition-skipButtonText

        | Custom text for the *Skip* step button.
        | Translations are supported. For example, `LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang.xlf:tour.skipButtonText`.
        | If not set, the default text is used.

    ..  confval:: previousButtonText
        :type: string
        :name: tour-definition-previousButtonText

        | Custom text for the *Previous* step button. Each step can override this setting.
        | Translations are supported. For example, `LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang.xlf:tour.previousButtonText`.
        | If not set, the default text is used.

    ..  confval:: nextButtonText
        :type: string
        :name: tour-definition-nextButtonText

        | Custom text for the *Next* step button. Each step can override this setting.
        | Translations are supported. For example, `LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang.xlf:tour.nextButtonText`.
        | If not set, the default text is used.

    ..  confval:: finishButtonText
        :type: string
        :name: tour-definition-finishButtonText

        | Custom text for the *Finish* button. Each step can override this setting.
        | Translations are supported. For example, `LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang.xlf:tour.finishButtonText`.
        | If not set, the default text is used.

    ..  confval:: nextTourIdentifier
        :type: string
        :name: tour-definition-nextTourIdentifier

        | Identifier of the next tour to be started after this one finishes.
        | This is useful to create a sequence of tours that guide the user through different parts of the backend.
        | The user must have access to the next tour for it to be started.

        ..  note::
            Even is set, not always the next tour will be started. It depends on the user's permissions. Read more about tour access in the chapter :ref:`tour-access`.

    ..  confval:: steps
        :type: array
        :name: tour-definition-steps
        :required: true

        Array of steps for the tour. See :ref:`configure-steps`.

Example:

.. code-block:: yaml
    identifier: vendor/shopProducts
    title: 'Introduction to Products Management Module'
    description: 'This tour introduces the basics of the Products module. Learn how to manage your product listings.'
    moduleName: 'shop_products'
    isStandalone: false
    enableInteraction: true
    previousButtonText: 'Back'
    nextButtonText: 'Next'
    finishButtonText: 'Done'
    nextTourIdentifier: vendor/shopCategories
    steps:
      - title: 'Product List View'
        content: 'This section displays all the products in your catalog. You can filter, sort, and search through the list.'
        target: '.products-list-container'
        frame: 'content'
        side: 'top'
        align: 'start'
        enableInteraction: false
      - title: 'Add New Product'
        content: 'Click this button to create a new product entry.'
        target: 'button.new-product-button'
        frame: 'content'
        side: 'right'
        align: 'center'
        enableInteraction: true
      - title: 'Product Details'
        content: 'Here you can enter details like product name, description, and price.'
        target: '.product-form'
        frame: 'content'
        side: 'bottom'
        align: 'center'
        enableInteraction: false

..  _configure-steps:

Configure steps
===============

Each step should be configured as a separate array item.
The following properties are available:

..  confval-menu::
    :display: table
    :name: tour-step
    :type:
    :required:

    ..  confval:: title
        :type: string
        :name: tour-step-title
        :required:

        Title displayed in the step popup.

    ..  confval:: content
        :type: string
        :name: tour-step-content
        :required:

        Content of the popup for this step.

    ..  confval:: target
        :type: string
        :name: tour-step-target

        | JavaScript-supported CSS selector used to highlight/select the target element on the page.
        | You can use common selectors like element names, IDs, classes, or attributes such as `data-*`.
        |
        | Examples:
        |
        | `#element-id `– select by ID
        | `.my-class` – select by class name
        | `button[data-action="save"]` – select a button with a specific data attribute
        | `input[name="productName"]` – select an input field by its name attribute
        |
        | These selectors follow standard querySelector rules and should uniquely point to the desired UI element.

    ..  confval:: frame
        :type: string
        :name: tour-step-frame

        | JavaScript-supported CSS selector pointing to the iframe in which the target element exists.
        | Use common selectors such as ID, class, name, or data attributes to identify the iframe.
        |
        | Examples:
        |
        | `iframe#content-frame` – select an iframe by ID
        | `iframe.module-frame` – select an iframe by class
        | `iframe[data-frame="productFrame"]` – select an iframe using a custom data attribute
        | `iframe[name="listFrame"]` – select an iframe by its name attribute
        |
        | The selector must match a valid iframe element in the TYPO3 backend.

    ..  confval:: side
        :type: string
        :name: tour-step-side

        Position of the popup relative to the target element. Allowed values: `top`, `right`, `bottom`, `left`.

    ..  confval:: align
        :type: string
        :name: tour-step-align

        Alignment of the popup relative to the target. Allowed values: `start`, `center`, `end`.

    ..  confval:: enableInteraction
        :type: bool
        :name: tour-step-enableInteraction

        Whether the user can interact with the highlighted area during the step.

    ..  confval:: previousButtonText
        :type: string
        :name: tour-step-previousButtonText

        | Custom text for the *Previous* step button (overrides tour-level setting if defined).
        | Translations are supported. For example, `LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang.xlf:step.previousButtonText`.

    ..  confval:: nextButtonText
        :type: string
        :name: tour-step-nextButtonText

        | Custom text for the *Next* step button (overrides tour-level setting if defined).
        | Translations are supported. For example, `LLL:EXT:typo3_interactive_tour/Resources/Private/Language/locallang.xlf:step.nextButtonText`.

    ..  confval:: requireUserActions
        :type: bool
        :name: tour-step-requireUserActions

        | Used to define if events of the `userActions` group (check :ref:`events <typo3-interactive-tour:confval-tour-step-events>` property) are required to perform by the user.
        | If the the `userActions` group in the `events` property is node defined, this property is ignored.

    ..  confval:: events
        :type: array
        :name: tour-step-events

        | Array of custom JavaScript events that should be triggered during the step.
        | They can be assigned to 3 different groups: `onShow`, `onExit`, `userActions`.
        | Read more about events in the chapter :ref:`configure-steps-events`.

    ..  confval:: sleep
        :type: int
        :name: tour-step-sleep

        | Time to wait before the step content is displayed.
        | This is sometimes needed to be able to highlight related target element inside the iframe.

Example:

.. code-block:: yaml

    steps:
      - title: 'Product List View'
        content: 'This section displays all the products in your catalog. You can filter, sort, and search through the list.'
        target: '.products-list-container'
        frame: 'content'
        side: 'top'
        align: 'start'
        enableInteraction: false
      - title: 'Add New Product'
        content: 'Click this button to create a new product entry.'
        target: 'button.new-product-button'
        frame: 'content'
        side: 'right'
        align: 'center'
        enableInteraction: true
      - title: 'Product Details'
        content: 'Here you can enter details like product name, description, and price.'
        target: '.product-form'
        frame: 'content'
        side: 'bottom'
        align: 'center'
        enableInteraction: false

..  _configure-steps-events:

Configure steps events
======================

Each step can contain an `events` property to define custom JavaScript events.
These events can be assigned to 3 different groups:

*   :javascript:`onShow`: events that are triggered when the step is shown
*   :javascript:`onExit`: events that are triggered when the step is hidden
*   :javascript:`userActions`: events that should be triggered by the user

..  note::
    The :javascript:`userActions` events will not be required if the :ref:`requireUserActions <typo3-interactive-tour:confval-tour-step-requireuseractions>` property is set to `false` on step level

..  confval-menu::
    :display: table
    :name: tour-step-events
    :type:

    ..  confval:: event
        :type: string
        :name: tour-step-events-event

        The JavaScript event to be triggered.

        Allowed values:
            - `click`
            - `keypress`
            - `focus`
            - `blur`
            - `change`
            - `input`
            - `resize`
            - `contextmenu`
            - `drag`
            - `drop`

    ..  confval:: target
        :type: string
        :name: tour-step-events-target

        | JavaScript-supported CSS selector used to highlight/select the target element on the page.
        | You can use common selectors like element names, IDs, classes, or attributes such as `data-*`.
        |
        | Examples:
        |
        | `#element-id `– select by ID
        | `.my-class` – select by class name
        | `button[data-action="save"]` – select a button with a specific data attribute
        | `input[name="productName"]` – select an input field by its name attribute
        |
        | These selectors follow standard querySelector rules and should uniquely point to the desired UI element.

    ..  confval:: delayBefore
        :type: integer
        :name: tour-step-events-delayBefore

        | Delay before the event is triggered (in miliseconds).
        | In certain cases, triggering an event on an element must be delayed to ensure that the element is ready for interaction.

    ..  confval:: delayAfter
        :type: integer
        :name: tour-step-events-delayAfter

        | Delay after the event is triggered (in miliseconds).

    ..  confval:: frame
        :type: string
        :name: tour-step-events-frame

        | JavaScript-supported CSS selector pointing to the iframe in which the event has to be triggered.
        | Use common selectors such as ID, class, name, or data attributes to identify the iframe.
        |
        | Examples:
        |
        | `iframe#content-frame` – select an iframe by ID
        | `iframe.module-frame` – select an iframe by class
        | `iframe[data-frame="productFrame"]` – select an iframe using a custom data attribute
        | `iframe[name="listFrame"]` – select an iframe by its name attribute
        |
        | The selector must match a valid iframe element in the TYPO3 backend.
