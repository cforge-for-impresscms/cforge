<?php
/**
 * Cache functions library.
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: cache.php,v 1.4 2003/12/09 15:03:53 devsupaul Exp $
 */

/**
 * cache_include() - Cache the output of a function
 * 
 * Caches  a function and includes it.
 *
 * @param		string	The cache name
 * @param		string	The funcion who's output is to be cached
 * @param		int		The lenght of time the output should be cached
 */
function cache_include($name,$function,$time) {
	$filename = XOOPS_ROOT_PATH."/modules/xfmod/cache/xfcache_$name.xf";

	while (!file_exists($filename) || (filesize($filename)<=1) || ((time() - filemtime($filename)) > $time)) {
		// file is non-existant or expired, must redo, or wait for someone else to
		if (!file_exists($filename)) {
			@touch($filename);
		}

		// open file. If this does not work, wait one second and try cycle again
		if ($wfh = @fopen($filename,'wb')) {
			// obtain a blocking write lock, else wait 1 second and try again
			if (flock($wfh,2)) { 
				// open file for writing. if this does not work, something is broken.
				// have successful locks and opens now
				$return = cache_get_new_data($function);
				fwrite($wfh, $return); //write the file
				fflush($wfh);
				flock($wfh,3); //release lock
				fclose($wfh); //close the file
				return $filename;
			} else { // unable to obtain flock
				sleep(1);
				clearstatcache();
			}
		} else { // unable to open for reading
			global $xoopsForgeErrorHandler;
			$xoopsForgeErrorHandler->setSystemError("Unable to open cache file for writing.");
		}
	} 
	// file is now good, include it.
	return $filename;
}
/**
 * cache_display() - Cache the output of a function
 * 
 * Caches the output of a function for the duration of $time.
 *
 * @param		string	The cache name
 * @param		string	The funcion who's output is to be cached
 * @param		int		The lenght of time the output should be cached
 */
function cache_display($name,$function,$time) {
	$filename = XOOPS_ROOT_PATH."/modules/xfmod/cache/xfcache_$name.xf";

	while ((filesize($filename)<=1) || ((time() - filemtime($filename)) > $time)) {
		// file is non-existant or expired, must redo, or wait for someone else to
		if (!file_exists($filename)) {
			@touch($filename);
		}

		// open file. If this does not work, wait one second and try cycle again
		if ($wfh = @fopen($filename,'wb')) {
			// obtain a blocking write lock, else wait 1 second and try again
			if (flock($wfh,2)) { 
				// open file for writing. if this does not work, something is broken.
				// have successful locks and opens now
				$return = cache_get_new_data($function);
				fwrite($wfh, $return); //write the file
				fflush($wfh);
				flock($wfh,3); //release lock
				fclose($wfh); //close the file
				return $return;
			} else { // unable to obtain flock
				sleep(1);
				clearstatcache();
			}
		} else { // unable to open for reading
			global $xoopsForgeErrorHandler;
			$xoopsForgeErrorHandler->addError("Unable to open cache file for writing.");
			return cache_get_new_data($function);
		}
	} 
	// file is now good, use it for return value
	if (!$rfh = fopen($filename,"rb")) { //bad filename
		return cache_get_new_data($function);
	}
	while(!flock($rfh, 1 + 4) && ($counter < 30)) { // obtained non blocking shared lock 
		usleep(500000); // wait 0.5 seconds for the lock to become available
		$counter++;
	}
	$result = stripslashes( fread($rfh, 200000));
	flock($rfh,3); // cancel read lock
	fclose($rfh);
	return $result;
}

/**
 * cache_get_new_data() - Get new output for a function
 *
 * @param		string	The name of the function who's output is to be updated
 */
function cache_get_new_data($function) {
	eval("\$res= $function;");
	return $res;
}
?>