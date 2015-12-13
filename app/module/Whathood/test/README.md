### Test Databases

* The test database can be set using the environment variable WH_PHPUNIT_DB_NAME
* [whathood.test.php](/app/module/Whathood/test/whathood.test.php) contains an override of doctrine.connection and doctrine.entitymanager, which should point to the test databases.
