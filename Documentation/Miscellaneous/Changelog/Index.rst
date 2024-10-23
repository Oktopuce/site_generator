
.. _changelog:

==========
Change log
==========

V3.2.1
------

::

   2024-10-04  [TASK] Change version number for TER (commit: 970ecd5 by Florian RIVAL)

V3.2.0
------

::

   2024-10-03 [FEATURE][DOC] Override extension configuration with site configuration (Commit beccc73 by Florian RIVAL)
   2024-09-20 [TASK] Migration to TYPO3 V13 (Commit 0d01bce by Florian RIVAL)

V3.1.1
------

::

   2024-05-16 [FEATURE][DOC] FIX-21 Make mapInList also work with functions (Thanks to Stig Nørgaard Færch) (commit: 54db0d2)
   2024-05-16 [BUGFIX][DOC] FIX-22 Manage multiline with same constant key - with and without condition for example (Thanks to Stig Nørgaard Færch) (commit: e4f5d6f)
   2024-05-16 [BUGFIX][DOC] FIX-20 Manage ":=" for TypoScript update (Thanks to Stig Nørgaard Færch) (commit: c4de908)
   2024-05-16 [BUGFIX][DOC] FIX-24 Allow module access for non-admin users (commit: a0e3954)

V3.1.0
------

::

   2023-11-10 [TASK] Add Page TsConfig configuration (commit: e69cb2e)

V3.0.0
------

::

   2023-10-27 16:31:54 [DOC] Update documentation for version 3 (commit: 4664b06)
   2023-10-27 13:18:19 [TASK] Use extbase ViewHelpers in Fluid Templates (commit: 53a81d5)
   2023-10-19 17:59:14 [TASK] Migration to TYPO3 V12 (commit: 76789ea)
   2023-09-08 16:07:41 [TASK] Upgrade with rector TYPO3 V11 & PHP 8.1 (commit: 46fa03c)

V2.0.1
------

::

   2024-05-16 [FEATURE][DOC] FIX-21 Make mapInList also work with functions (Thanks to Stig Nørgaard Færch) (commit: d7e014e)
   2024-05-16 [BUGFIX][DOC] FIX-22 Manage multiline with same constant key - with and without condition for example (Thanks to Stig Nørgaard Færch) (commit: e4f5d6f)
   2024-05-16 [BUGFIX][DOC] FIX-20 Manage ":=" for TypoScript update (Thanks to Stig Nørgaard Færch) (commit: c4de908)
   2024-05-16 [BUGFIX][DOC] FIX-24 Allow module access for non-admin users (commit: a0e3954)
   2023-11-10 [TASK] Add Page TsConfig configuration (commit: e69cb2e)
   2023-10-27 [DOC] Update documentation for version 3 (commit: 972f72c)
   2023-10-27 [TASK] Use extbase ViewHelpers in Fluid Templates (commit: 53a81d5)
   2023-10-19 [TASK] Migration to TYPO3 V12 (commit: 76789ea)
   2023-09-08 [TASK] Upgrade with rector TYPO3 V11 & PHP 8.1 (commit: 46fa03c)
   2022-01-05 [TASK] Make settings available in fluid (commit: 3857c19)
   2022-02-15 [DOCS] Update documentation for constant mapping (commit: 4ba56f2)
   2022-02-15 [TASK] Add new feature test cases for constants mapping in home page template (commit: b470a47)
   2022-02-15 [DOCS] Update documentation for new features and according to TYPO3 Extension Award Team feedback (commit: a2fcb27)
   2022-02-15 [FEATURE] Manage directives for constants mapping customization in home page template (commit: 3f8c0ee)
   2022-02-15 [TASK] Add types, type hint, optimisation, dependency injections and correct typo (commit: 4776cdc)
   2022-02-02 [TASK] Upgrade typo3/cms-core to version 11.5.0 because of vulnerabilities in version less than 11.5.0 (commit: 1b1ad76)
   2022-02-01 [TASK] Manage all data from mappingArrayMerge so that custom extensions can use them (commit: 4f8306b)
   2022-02-01 [BUGFIX] Update of clearCacheCmd in page TSConfig (commit: 283e216)
   2022-01-28 [BUGFIX] Fix warning with PHP V8 (commit: 38087fe)
   2022-01-28 [TASK] Remove unused property (commit: 2d94acb)
   2021-09-24 [DOCS] Update documentation (commit: b399656)
   2021-09-23 [TASK] Add new supported versions (commit: 04b6637)
   2021-09-23 [TASK] Add missing message (commit: 424c814)
   2021-09-23 [TASK] Set unit test (commit: a22ff1c)
   2021-09-23 [TASK] Use new event dispatcher instead of signal slot (commit: 5fdfaf2)
   2021-09-23 [TASK] Set type hint and cast for strict_types (commit: 520198c)
   2021-09-23 [TASK] Use symfony dependency injection (commit: c4b34d2)
   2021-09-06 [TASK] Set lang configuration for site (commit: fa99441)
   2021-09-06 [BUGFIX] Correct bug if there is no TS on home page (commit: 526bf72)
   2021-09-06 [TASK] Declare strict_types (commit: ad4d12d)
   2021-09-06 [TASK] V11 migration tasks (commit: 37fbf59)
   2021-09-03 [TASK] Add version 11 (commit: 49da7dd)
   2021-09-03 [TASK] Prepare V1 migration (commit: 1ed975a)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.
