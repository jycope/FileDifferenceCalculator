<?php

$code = <<<'EOC'
<?php
$var = 42;
$var = 42;
EOC;

var_dump(ast\parse_code($code, $version=70));