<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:3030/?username=tom&data=123");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_exec($ch);
curl_close($ch);