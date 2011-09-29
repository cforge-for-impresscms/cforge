<?php 
/**
 * about.php
 *
 * @version   $Id: about.php,v 1.5 2004/01/16 19:07:58 jcox Exp $
 */
include_once("../../../mainfile.php");

$langfile="help.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/help/help_utils.php");

//meta tag information
$metaTitle=": "._XF_H_HELP;

include ("../../../header.php");

$site = $xoopsConfig['sitename'];

help_menu("about");

begin_help_content();

$title = "Welcome";
$content = "<p>Welcome to Novell Forge. May the Source be with you! It is our sincere desire that this site and all that it has to offer becomes an increasingly valuable resource in your Novell and open source development efforts. 
There are several reasons why Novell Forge was created: 

<ul>
<li><b>Hosting of Open Source Projects</b> - This site provides access to a wide array of software projects that are governed by open source licenses. Novell Forge allows you to take advantage of useful software that is made broadly available by your peers in the development community. This site also allows you to host your software projects and securely share them with others, and thereby reap the benefits of having other users test and enhance your projects. 
<li><b>Development Communities</b> - This site contains a multitude of specialized development communities that are focused on enabling software development around a certain language or technology. These communities are managed by Novell but are open to public contribution. The communities provide a valuable resource for information, from peer engineering groups as well as from Novell, on how to most effectively develop and leverage the software you care most about. 
<li><b>Proof Of Concept</b> - This website is a proof of concept that demonstrates the ability of Novell technology to interact effectively and seamlessly with well-known open source software. Linux, MySQL, Xoops/XoopsForge, GNU Mailman, CVS, and ViewCVS are used in harmony with Novell technologies including iChain and eDirectory to power this website.
<li><b>Promote Novell as an Open Source Contributor</b> - Novell utilizes this site to manage and release open source software projects that Novell is working on. This website demonstrates the increased committment by Novell to the promotion of open source software. 

</ul>";

themesidebox_help($title,$content);

echo "<br><br>";

$title = "Technology Behind $site";
$content = "<p>$site is the result of mixing many open source technologies with key
Novell technologies, as well as a significant development effort by Novell Developer
Services.  Here is a summary of the technology involved.
<ul>
<li><b>Content Management Framework</b> - We used the basic content management
framework provided by <a href=\"http://www.xoops.org/\">Xoops</a>.  This framework
is written in <a href=\"http://www.php.net/\">PHP</a> and provides a basic architecture
for the site (theme capabilities, module plug-in support, site administrative console,
etc.).  A few key modifications were made to the Xoops framework to meet our needs.
<li><b>Code Collaboration Support</b> - We used the
<a href=\"http://xoopsforge.sourceforge.net/\">XoopsForge</a>
code base to provide code collaboration support.  XoopsForge is a Xoops module that
extends the functionality of a Xoops site to provide features found in SourceForge.
At the time of our snapshot many of the features of SourceForge had been implemented
but there were still key features missing.  We have made extensive modifications to
the XoopsForge code base.  XoopsForge is written in PHP.
<li><b>Web Server</b> - We use the <a href=\"http://www.apache.org/\">Apache</a> web
server to serve the pages.  No modification of Apache in the way of additional Apache
modules was required.
<li><b>Database</b> - Xoops is built to utilize
<a href=\"http://www.mysql.com/\">MySQL</a> as the database
which houses the web site data, so we also chose to use MySQL for this.  Xoops provides
a database abstraction which could have allowed us to use an alternative relational
database, but has only completed the implementation for MySQL.  We considered
<a href=\"http://www.postgresql.org/\">PostgreSQL</a> as an alternative.
We decided against PostgreSQL primarily due to time constraints; we would have been
required to implement the database abstraction ourselves.
<li><b>Source Code Control</b> - We used <a href=\"http://www.cvshome.org/\">CVS</a>
as the repository for source code for projects.
<li><b>Source Code Repository Browsing via Web</b> - One key feature missing from
XoopsForge that we required was the ability to browse a CVS repository via a web browser.
The <a href=\"http://viewcvs.sourceforge.net/\">ViewCVS</a> product gave us the
functionality that we needed for the time being.  ViewCVS is written in Python.
<li><b>Mailing list support</b> - Another key feature missing from XoopsForge that we
needed was mailing list support.  We used <a href=\"http://www.list.org/\">GNU Mailman</a>
to implement mailing list support.  Mailman is written in Python.  We had to write a
custom piece of software to integrate with Mailman during automatic list creation time.
This piece of software is written in C.
<li><b>User Authentication and Authorization</b> - A key requirement for us was to not
require new users of our site to have to create new accounts with new usernames and
passwords, but to be able to use existing <a href=\"http://www.novell.com/coolsolutions/psmag/assets/elogin.pdf\">eLogin</a>
accounts to gain access to the site.  This meant, first of all, that our websites would
sit behind <a href=\"http://www.novell.com/products/ichain/\">iChain</a>.
We also added LDAP authentication capabilities to Xoops so that we now authenticate using
<a href=\"http://www.novell.com/products/edirectory/\">eDirectory</a> instead of MySQL
as Xoops does.  We also needed to use project membership to enforce rights to view or
modify CVS code repositories.  We made extensions to the eDirectory schema to support
this, and then reconfigured CVS to utilize eDirectory as an authentication and
authorization provider.
<br>Probably the coolest feature of the Forge site is how we were able to use
eDirectory to enforce rights to code repositories.  Results of this integration with eDirectory
include:
	<ul>
	<li>Ability to use the same username and password to access CVS repositories and the Forge website
	<li>Users can only view or modify CVS repositories for hosted projects where they have permission to do so
	<li>The server that houses the individual CVS repositories for each project allows authenticated users to view or modify CVS if they have rights; however, users cannot actually log into the server. This provides a high level of security for the code repositories, and for the Forge website in general.
	</ul>
</ul>";

themesidebox_help($title,$content);

end_help_content();

include("../../../footer.php");
?>