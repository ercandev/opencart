<?php
function token($length = 32): string {
  return substr(bin2hex(random_bytes($length)), 0, $length);
}
