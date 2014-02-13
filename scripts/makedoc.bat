:: $Id: $ 
@ECHO OFF

:: /**
::  * makedoc - PHPDocumentor script to save your settings
::  * 
::  * Put this file inside your PHP project homedir, edit its variables and run whenever you wants to
::  * re/make your project documentation.
::  * 
::  * The version of this file is the version of PHPDocumentor it is compatible.
::  * 
::  * It simples run phpdoc with the parameters you set in this file.
::  * NOTE: Do not add spaces after bash variables.
::  *
::  * @copyright         makedoc.bat is part of PHPDocumentor project {@link http://freshmeat.net/projects/phpdocu/} and its LGPL
::  * @author            Roberto Berto <darkelder (inside) users (dot) sourceforge (dot) net>
::  * @version           Release-1.1.0
::  */

:: /**
::  * Get parent directories path.  Yikes!
::  */
SET SCRIPT=%~n0%~x0
FOR %%F IN (%SCRIPT%) DO SET PWD=%%~dpF
SET PWD=%PWD%..

:: /**
::  * path of PHPDoc executable
::  *
::  * @var               string PATH_PHPDOC
::  */
::SET PATH_PHPDOC=C:\PhpDocumentor-1.4.3

:: /**
::  * title of generated documentation, default is 'Generated Documentation'
::  * 
::  * @var               string TITLE
::  */
SET TITLE=Mic Framework Documentation

:: /** 
::  * name to use for the default package. If not specified, uses 'default'
::  *
::  * @var               string PACKAGES
::  */
SET PACKAGES=Mic

:: /**
::  * name of a directory(s) to parse directory1,directory2
::  * %CD% is the directory where makedoc.bat 
::  *
::  * @var               string DIRECTORIES
::  */
SET DIRECTORIES=%PWD%\library,%PWD%\tutorials

:: /** 
::  * name of files to parse file1,file2
::  *
::  * @var               string FILES
::  */
SET FILES=%PWD%\README,%PWD%\INSTALL,%PWD%\CHANGELOG,%PWD%\MIT-LICENSE

SET FILE_IGNORE=*Test*

:: /**
::  * where documentation will be put
::  *
::  * @var               string PATH_DOCS
::  */
SET PATH_DOCS=%PWD%\doc

:: /**
::  * what outputformat to use (html/pdf)
::  *
::  * @var               string OUTPUTFORMAT
::  */
SET OUTPUTFORMAT=HTML

:: /** 
::  * converter to be used
::  *
::  * @var               string CONVERTER
::  */
SET CONVERTER=Smarty

:: /**
::  * template to use
::  *
::  * @var               string TEMPLATE
::  */
SET TEMPLATE=PHP

:: /**
::  * parse elements marked as private
::  *
::  * @var               bool (on/off)           PRIVATE
::  */
SET PRIVATE=off

:: make documentation
::CD %PATH_PHPDOC%
::%PATH_PHPDOC%\
phpdoc.bat -f "%FILES%" -d "%DIRECTORIES%" -i "%FILE_IGNORE%" -t "%PATH_DOCS%" -ti "%TITLE%" -dn "%PACKAGES%" -o "%OUTPUTFORMAT%:%CONVERTER%:%TEMPLATE%" -pp "%PRIVATE%"

