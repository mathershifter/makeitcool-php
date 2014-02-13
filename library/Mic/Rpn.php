<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Mic PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic_Rpn
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * @see Mic_Object
 */
require_once 'Mic/Object.php';

/**
 * Mic_Rpn
 * 
 * RPN Caclulator
 *
 * @category  Mic
 * @package   Mic_Rpn
 */
class Mic_Rpn extends Mic_Object
{
    /**
     * Maps operators to their callback methods
     * 
     * Current mappings:
     * 
     * $_operators = array(
     *     '+'     => '_add',
     *     '-'     => '_subtract',
     *     '*'     => '_multiply',
     *     '/'     => '_divide',
     *     '%'     => '_mod',
     *     '^'     => '_pow',      '**'    => '_pow',
     *     '<'     => '_lt',       'LT'    => '_lt',
     *     '<='    => '_le',       'LE'    => '_le',
     *     '=='    => '_eq',       'EQ'    => '_eq',
     *     '>='    => '_ge',       'GE'    => '_ge',
     *     '>'     => '_gt',       'GT'    => '_gt',
     *     'MAX'   => '_max',
     *     'MIN'   => '_min',
     *     'IF'    => '_if',
     *     'LIMIT' => '_limit'
     *     'NaN'   => '_nan',      'NAN'   => '_nan',  'nan'   => '_nan'
     *  );
     *
     * @var array
     */
    private $_operators = array(
        '+'     => '_add',
        '-'     => '_subtract',
        '*'     => '_multiply',
        '/'     => '_divide',
        '%'     => '_mod',
        '^'     => '_pow',      '**'    => '_pow',
        '<'     => '_lt',       'LT'    => '_lt',
        '<='    => '_le',       'LE'    => '_le',
        '=='    => '_eq',       'EQ'    => '_eq',
        '>='    => '_ge',       'GE'    => '_ge',
        '>'     => '_gt',       'GT'    => '_gt',
        'MAX'   => '_max',
        'MIN'   => '_min',
        'IF'    => '_if',
        'LIMIT' => '_limit',
        'NaN'   => '_nan',      'NAN'   => '_nan',  'nan'   => '_nan'
    );
    
    /**
     * Stores the RPN expression
     * 
     * @var Mic_Ring
     */
    private $_expression;
    
    /**
     * Stores resaults of RPN expression
     * 
     * @var Mic_Ring
     */
    private $_stack;
    
    /**
     * Initializes an empty stack for storing residuals from the evaluated RPN
     * expression
     * 
     * @param $data expression to be evaluated
     * @param $args
     */
    protected function __construct($expression)
    {
        if (is_array($expression)) {
            $this->_expression = new Mic_Array($expression);
        } elseif (is_scalar($expression)) {
            $this->_expression = S($expression)->rsplit('/\,|\s+/');
        } elseif ($expression instanceof Mic_Array) {
            $this->_expression = $expression;
        } else {
            throw new Mic_Array_Exception("Expresion must be an array, scalar, or Mic_Array."); 
        }
        
        $this->_stack = new Mic_Array();
    }
    
    public function dump()
    {
        return $this->_stack;
    }
    
    /**
     * Static wrapper for evaluating an RPN expression
     *  
     * @see _evaluate
     * @param string|array $expression
     * @return mixed
     */
    public static function evaluate($expression)
    {
        $instance = new self($expression);
        return $instance->_evaluate();
    }
    
    /**
     * Evaluates the RPN expression
     * 
     * @return mixed
     */
    private function _evaluate()
    {
        foreach ($this->_expression as $token) {
            /*
             *  Allow numerics, booleans, and null to be pushed directly onto
             *  the stack.  Strings are checked against the valid operator
             *  list then if all checks out the associated method is called
             */
            if (is_numeric($token) || is_bool($token) || is_null($token)) {
                $this->_stack->push($token);
            } elseif (is_string($token)) {
                if (!array_key_exists("$token", $this->_operators)) {
                    require_once 'Mic/Rpn/Exception.php';
                    throw new Mic_Rpn_Exception("$token is not a valid token");
                }
                
                // catch failed operations and push null onto the stack
                try {
                    $this->{$this->_operators["$token"]}();    
                } catch (Mic_Rpn_Exception $e) {
                    $this->_stack->push(null);
                }
            } else {
                require_once 'Mic/Rpn/Exception.php';
                throw new Mic_Rpn_Exception(get_class($token) . " is not a valid token type");
            }      
        }
        
        // by this point there should only be one element on the stack
        if ($this->_stack->count() !== 1) {
            require_once 'Mic/Rpn/Exception.php';
            throw new Mic_Rpn_Exception("Should be only one element remaining");
        }
        
        return $this->_stack->last();
    }
    
    /**
     * 
     */
    private function _nan()
    {
        $this->_stack->push(null);
    }
    /**
     * Pops 2 numbers off the stack and stores the sum
     * 
     * @return void
     */
    private function _add()
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        $this->_stack->push(array_sum(array($a, $b)));
    }
    
    /**
     * Pops 2 numbers off the stack and stores the product
     * 
     * @return void
     */
    private function _multiply()
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        $this->_stack->push(array_product(array($a, $b)));
    }
    
    /**
     * Pops 2 numbers off the stack and stores the difference
     * 
     * @return void
     */
    private function _subtract()
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        $this->_stack->push(array_sum(array($a, -$b)));
    }
    
    /**
     * Pops 2 numbers off the stack and stores the quotient
     * 
     * @return void
     */
    private function _divide()
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        if ($b === 0) {
            $this->_stack->push(null);
        } else {
            $this->_stack->push(array_product(array($a, 1/$b)));    
        }
    }
    
    /**
     * Pops 2 numbers off the stack and stores the modulus
     *
     * @return void
     */
    private function _mod()
    {
        list($a, $b) = $this->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        if ($b === 0) {
            $this->_stack->push(null);
        } else {
            eval("\$result = $a % $b;");
        }
        
        $this->_stack->push($result);
    }
    
    /**
     * Pops 2 numbers off the stack and stores first (base) raised to the
     * power of the second (exponent)  
     * 
     * @return void
     */
    private function _pow()
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        $this->_stack->push(pow($a, $b));
    }
    
    /**
     * Pops 2 numbers off the stack and stores larger of the two
     * 
     * @return void
     */
    private function _max()
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        $this->_stack->push(max($a, $b));
    }
    
    /**
     * Pops 2 numbers off the stack and stores smaller of the two
     * 
     * @return void
     */
    private function _min()
    {   
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        $this->_stack->push(min($a, $b));
    }
    
    /**
     * Pops 3 numbers off the stack and stores true if the first is
     * between the second and third, false if not 
     * 
     * @return void
     */
    private function _limit()
    {
        list($a, $b, $c) = $this->_stack->splice(-3, 3);
        
        $this->_validate($a);
        $this->_validate($b);
        $this->_validate($c);
        
        if ($a >= $b && $a <= $c) {
            $this->_stack->push($a);
        } else {
            $this->_stack->push(null);
        }
    }
    
    /**
     * Pops 3 numbers off the stack and stores the second if the first
     * evaluates to true, the third otherwise
     * 
     * @return void
     */
    private function _if()
    {
        list($a, $b, $c) = $this->_stack->splice(-3, 3);
        
        if ($a) {
            $this->_stack->push($b);   
        } else {
            $this->_stack->push($c);
        }   
    }
    
    /**
     * Convenience method for _compare('<')
     * 
     * @return void
     */
    private function _lt()
    {
        $this->_compare('<');
    }
    
    /**
     * Convenience method for _compare('<=')
     * 
     * @return void
     */
    private function _le()
    {
        $this->_compare('<=', $b);
    }
    
    /**
     * Convenience method for _compare('===')
     * 
     * @return void
     */
    private function _eq()
    {
        $this->_compare('===');
    }
    
    /**
     * Convenience method for _compare('>=')
     * 
     * @return void
     */
    private function _ge()
    {
        $this->_compare('>=');
    }
    
    /**
     * Convenience method for _compare('>')
     * 
     * @return void
     */
    private function _gt()
    {
        $this->_compare('>');
    }
    
    /**
     * Pops 2 numbers off the stack and compares the using the supplied
     * operator.  Stores the result
     * 
     * @param string $op
     * @return void
     */
    private function _compare($op)
    {
        list($a, $b) = $this->_stack->splice(-2, 2);
        
        $this->_validate($a);
        $this->_validate($b);
        
        eval("\$result = $a $op $b;");
        
        $this->_stack->push($result);
    }
    
    /**
     * Ensures the term is numeric
     * 
     * @param $term
     * @return void
     */
    private function _validate($term)
    {
        if (!is_int($term) && !is_float($term) && !is_numeric($term)) {
            require_once 'Mic/Rpn/Exception.php';
            throw new Mic_Rpn_Exception("Term '{$term}' is not numeric");
        }
    }
}
