#!/opt/webstack/bin/php
<?php
/**
 * RPN caclulator 
 * 
 * Usage: rpn_eval.php <expression>
 */
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

/* die early if expression is empty */
if ($argc < 2) {
    echo "No expression given\n";
    echo "Usage: " . basename($argv[0]) . " <expression>\n";
    die();
}

/* expression can be comma, space or both delimited */
$expression = preg_replace('/,\s*/', ' ', 
    join(' ',
        array_slice($argv, 1)
    )
);

try {
    /* attempt to evaluate the expression */
    echo MC_Rpn::evaluate($expression) . "\n";
} catch (MC_Rpn_Exception $e) {
    /* Caught an expression error */ 
    die("RPN Error: " . $e->getMessage() . "\n");
} catch (MC_Type_Stack_Exception $e) {
    /* Caught a lower level stack error */
    die("Stack Error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    /* Caught an unknown error */
    die("Unknown Error: " . $e->getMessage() . "\n");
}
