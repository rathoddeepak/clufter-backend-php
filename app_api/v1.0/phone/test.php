<?php
function callback($buffer)
{
  // Save the output (to append or create file)
  $fh = fopen("razor_log.txt", "a");
  fwrite($fh, $buffer);
  fclose($fh);

  // Return the output
  return $buffer;
}

// Any thing that is indended to be sent to the client is stored in a buffer and callback is called
ob_start("callback");


echo $data['asdasd'];
// Write out to buffer here
echo "TEST";
echo " SOME MORE DATA";
echo "\r\nNEW LINE";
echo "<b>SOME HTML</b>";

// Send to client
ob_end_flush();
?>