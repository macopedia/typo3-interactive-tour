:navigation-title: Tour Access
..  _tour-access:

===========
Tour Access
===========

Most TYPO3 core tours, as well as those provided by extension authors, are bound to specific backend modules.
This means a tour will only be displayed when the user is in the corresponding module and has access to it.

It's important to note that if a user does not have access to a particular module, the related tour will not be displayed - it won't even appear in the dedicated `Guids` module.

On the Tour level configuration, it is also possible to define more specific access conditions through the :ref:`permission <typo3-interactive-tour:confval-tour-definition-permissions>` setting.
If those permissions are not met, the tour will not be displayed even if the user has access to the module.