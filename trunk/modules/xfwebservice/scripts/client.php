<html>
<head>
<title>Web Service API Test</title>
<style>
	.t {width:21em;}
</style>
</head>
<body>

<h1>Web Service API Test</h1>

<form method="POST" action="/modules/xfwebservice/docroot/auth/">
<input type="hidden" name="c" value="login">
<input type="text" class="t" name="u"> Username<br/>
<input type="password" class="t" name="p"> Password<br/>
<input type="submit" value="Login">
</form>

<form method="POST" action="/modules/xfwebservice/docroot/auth/">
<input type="hidden" name="c" value="logout">
<input type="text" class="t" name="s"> Session ID<br/>
<input type="submit" value="Logout">
</form>

<form method="GET" action="/modules/xfwebservice/docroot/build/">
<input type="hidden" name="c" value="targets">
<input type="submit" value="Targets">
</form>

<form method="POST" action="/modules/xfwebservice/docroot/build/">
<input type="hidden" name="c" value="start">
<input type="text" class="t" name="n" value="a_cvstest"> Project<br/>
<input type="text" class="t" name="m" value="hello"> CVS Module<br/>
<input type="text" class="t" name="t" value="suse-90-i586"> Target<br/>
<input type="text" class="t" name="s"> Session ID<br/>
<input type="submit" value="Build">
</form>

<form method="GET" action="/modules/xfwebservice/docroot/build/">
<input type="hidden" name="c" value="status">
<input type="text" class="t" name="b"> Build ID<br/>
<input type="text" class="t" name="s"> Session ID<br/>
<input type="submit" value="Build Status">
</form>

<form method="GET" action="/modules/xfwebservice/docroot/publish/">
<input type="hidden" name="c" value="files">
<input type="text" class="t" name="n" value="a_cvstest"> Project<br/>
<input type="text" class="t" name="s"> Session ID<br/>
<input type="submit" value="Publish List">
</form>

<form method="POST" action="/modules/xfwebservice/docroot/publish/">
<input type="hidden" name="c" value="start">
<input type="text" class="t" name="f"> File ID<br/>
<input type="text" class="t" name="s"> Session ID<br/>
<input type="submit" value="Publish">
</form>

<form method="GET" action="/modules/xfwebservice/docroot/publish/">
<input type="hidden" name="c" value="status">
<input type="text" class="t" name="p"> Publish ID<br/>
<input type="text" class="t" name="s"> Session ID<br/>
<input type="submit" value="Publish Status">
</form>

</body>
</html>