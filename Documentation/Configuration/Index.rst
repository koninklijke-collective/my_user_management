.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt
.. index:: Configuration

Configuration
-------------

When the extension is installed you can start configuring the backend.

Configure Backend User
^^^^^^^^^^^^^^^^^^^^^^

You need a backend user that have access to the following elements:

+-------------------------------------------------------------------------------------------------------+
| Modules                                                                                               |
+=======================================================================================================+
| * Backend Management **(always)**                                                                     |
| * Backend Management>User Admin *(optional)*                                                          |
| * Backend Management>User Access *(optional)*                                                         |
| * Backend Management>File Mounts *(optional)*                                                         |
| * Backend Management>Login History *(optional)*                                                       |
+-------------------------------------------------------------------------------------------------------+

+-------------------------------------------------------------------------------------------------------+
| Tables (modify)                                                                                       |
+=======================================================================================================+
| * Backend usergroup                                                                                   |
| * Backend user                                                                                        |
| * Filemount                                                                                           |
+-------------------------------------------------------------------------------------------------------+

+-------------------------------------------------------------------------------------------------------+
| Allowed excludefields                                                                                 |
+=======================+===============================================================================+
| **Backend User**      | * db_mountpoints                                                              |
|                       | * disable                                                                     |
|                       | * email                                                                       |
|                       | * file_mountpoints                                                            |
|                       | * usergroup                                                                   |
|                       | * realName                                                                    |
|                       | * password                                                                    |
|                       | * username                                                                    |
+-----------------------+-------------------------------------------------------------------------------+
| **Backend Usergroup** | * disable                                                                     |
|                       | * grouptitle                                                                  |
|                       | * description                                                                 |
+-----------------------+-------------------------------------------------------------------------------+
| **Filemount**         | (all)                                                                         |
|                       |                                                                               |
|                       | * disable                                                                     |
|                       | * path                                                                        |
|                       | * title                                                                       |
|                       | * read_only                                                                   |
|                       | * base                                                                        |
+-----------------------+-------------------------------------------------------------------------------+


Configure Backend Group
^^^^^^^^^^^^^^^^^^^^^^^

You can **optionally** define access to specific backend groups.

+-------------------------------------------------------------------------------------------------------+
| Backend User Management: Allowed backend groups for maintenance                                       |
+=======================================================================================================+
| * Checkbox per group                                                                                  |
| * A selected group gives the permission to restrict backend groups                                    |
+-------------------------------------------------------------------------------------------------------+