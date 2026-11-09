<?php
// set or update cookie
setcookie('cookie_name','cookie_value_changed',time()+60,'/');
print_r($_COOKIE);
echo "<br><hr>".$_COOKIE['cookie_name'];

// delete cookie
setcookie('cookie_name','',time()-0,'/');