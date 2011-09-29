<?php
  if   ( $server_auth_http )
  {
   if   ( ! isset($PHP_AUTH_USER))
   {
     header("WWW-Authenticate: Basic realm=\"News Portal\"");
     header("HTTP/1.0 401 Unauthorized");
     echo "Authentifizierung erforderlich\n";
     exit;
   }
   else
   {
       $server_auth_user = $PHP_AUTH_USER;
       $server_auth_pass = $PHP_AUTH_PW;
   }
  }
?>