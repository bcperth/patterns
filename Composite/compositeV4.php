<?php

/**
 * Composite: The composite pattern constructs a tree with each node being either a composite element 
 * (has children) or a leaf element (no children). It also allows the user to process the tree (or a node)
 * in a consistent manner without knowing in advance whether the node is composite or a leaf. 
 * Meaning, you can traverse the tree using process(node) at each node without first determining 
 * the node type. Its certain however that the processing will be different, but this is taken care of 
 * by the pattern. 
 * 
 * Comparison to Decorator. Decorator UML looks similar to Composite. Decorator also constructs
 * a "tree", with each node having 1 child only (ie a linked list) but the references point back to 
 * the base case. The Decorator tree is approach from the outermost parent and works back to the single leaf
 * which executes the base method and then work back out with each decorator method "adding value" on the
 * way. Decorator is a "functional" pattern, with recognisable use cases. Composite is a "structural" pattern,
 * used to create trees and traverse those trees in a transparent manner.
 * 
 * Comparison to Interpreter. It would seem that the Interpreter pattern is a specific "use case" of
 * the Composite pattern. ie the tree structures created are exactly the same. The idea with Interpreter
 * is to create a variation of the composite class for each derivation rule (ref BNF) of the language.
 * Leaf nodes correspond to terminal symboys - and composite nodes correspond to non-termminals.
 * All derived classes must implement the "evaluate()" method. Then a parse tree is contructed (either
 * by hand for a specific language statement or using a parser on any statement). The "evaluate()" method
 * is then called on the top node, which the recursively "executes" the entire statement.  
 * 
 * The example will be an arithmetic expression evaluator. Normally the tree would be produced by a parser  
 * depending on the expression entered. In this case we will hand code a tree to calculate the expression
 * ((a+b)*(c+d))/e. Then poke values for a,b,c,d,e into the leaf nodes and tell the tree to process itself 
 * and output the result. 
 * Version 1 will ignore the "(" and ")" and hand code the precedence. 
 * We need 5 leaf nodes and 1 each of *,+ and / nodes.
 * 
 * This is Version 2 - adding the braces
 * In this version the tree is changed from a binary tree - now composite objects have 3 children
 * This accommodates expressions comprising an operator and 2 operands
 * In this model leafs are either numbers, operators or braces
 * 
 * Version 3 includes a parser that will create a parse tree for any arithmentic expression.
 * This would be the full intended use case for the Interpreter pattern.
 * 
 * Version 4 adds a lexer and improves the parsing algorith to deal with precedence and associativity
 * 
 * Version 5 will turn the lexer and parser into objects (initially procedureal code)
 * The lexer and parser male be able to use other patterns ...
 */
// -------------------------------------------------------------------------
// Define an iterface to be implemented in every node

interface IProcess
{
                 // Interface definition
    public function processNode();   // process the node
}

// define an abstract leaf class
class Leaf implements IProcess
{
    private $value;   // can be a numeric value or one of the terminals

    public function __construct($value)
    {
        // the constructor just sets the value
        $this->value = $value;
    }

    public function processNode()
    {
        // a terminal is printed out when accessed
        // after execution you will see the full expression printed
        // correctly as ((a+b)*(c+d))/e  ( with value for a,b,c,d,e as hardcoded below)
        // .. showing that left order in which the termianl are accessed
        // ie all composites process children in left, middle, right order
        echo $this->value;  
        return $this->value;
    } // implementing IProcess
}

// Create the abstract class for the Composite nodes
abstract class Composite Implements IProcess
{
    // The parse tree 
    protected $leftChild;   // reference to left child
    protected $midChild;    // reference to mid child
    protected $rightChild;  // reference to right child

    // the constructor links its 2 children
    public function __construct($leftChild,$midChild,$rightChild)
    {
        $this->leftChild = $leftChild;
        $this->midChild = $midChild;
        $this->rightChild = $rightChild;
    }
    abstract public function processNode(); // process the node
}

// there are 4 types possible of composite nodes
// value operator value, leftBrace value rightBrace, operator value, value operator 
// but we only need "value operator value" and "leftBrace value rightBrace"

// define the "value operator value" composite
class TwoOperandComposite extends Composite
{
    public function processNode()
    {
        // returns product of 2 children
        $val1     =  $this->leftChild->processNode();   // returns a value
        $operator =  $this->midChild->processNode();    // the operator
        $val2     =  $this->rightChild->processNode();  // returns a value
        $result = 0;
        switch($operator){
        case "*":
            $result = $val1 * $val2;
            break;
        case "/":
            $result = $val1 / $val2;
            break;
        case "+":
            $result = $val1 + $val2;
            break;
        case "-":
            $result = $val1 - $val2;
            break;   
        }
        return($result);
        //return ($this->child1->processNode() * $this->child2->processNode());
    }
}

// define the "leftBrace operator RightBrace" composite
class BraceComposite extends Composite
{
    public function processNode()
    {
        // returns the value between braces
        $this->leftChild->processNode();
        $result     =  $this->midChild->processNode();  // returns a value
        $this->rightChild->processNode();
        return $result;
        //return ($this->child1->processNode() * $this->child2->processNode());
    }
}

//---------------------------------------------------------------------
// Construct the tree by creating the nodes and linking them to get evaluate correctly
// ((a+b)*(c+d))/e
// the order of calculation is a+b then c+d then * sums then / by e

// The Shunting-yard algorithm below creates the unique parse tree to evalute the supplied
// expression. (The "parse" was done by hand in composite)

class Token {
    public $tokenName;
    public $tokenValue;

    // the constructor initialises
    public function __construct($name,$value)
    {
        $this->tokenName = $name;
        $this->tokenValue = $value;
    }
}

// these are the arrays 
$input = "";         // input expression
$output = [];        // Array to contain the parse output in reverse-polish 
$operatorStack = []; // the operator stack for the Shunting-yard algorithm
$inTokens = [];      // this contains the lexers output
$nodeBuilder = [];   // used to construct parse tree. Contains head node after parse

// Step 1 - construct the "lexer"
// it will recognise:
// Numbers (floats or ints)    : outputs N token with value = numeric value
// Binary operators +,-,*,/,^  : outputs O token with value "/" etc
// Unary Operators -,          : outputs U token with value "-" etc
// Functions sin, cos, tan     : outputs F token with value "sin" etc
// Left Brace (                : outputs ( token
// Right Brace )               : outputs ) token
// unrecognised                : outputs ERR token with value of unrecognised input

$input = "((2+3)*(4+5))/2";       // input expression 
$pos=0;
$len = strlen($input);
// main processing loop
while ($pos < $len){
    // first test for a number (possibly multidigit) at this position
    if (preg_match("/^[0-9]*\.?[0-9]+/",substr($input,$pos),$match)){  
        // Found number, is at $match[0]
        $inTokens[] = new Token('N',$match[0]); // create the token
        $pos+= strlen($match[0]);               //advance past the string
        continue;
    }

    // Not a number so look for non-numeric tokens
    $ch = $input[$pos++];
    if ($ch == null) break;     // no more chars left
 
    switch ($ch)
    {
        case '(':
            $inTokens[] = new Token('(',null);
            break;
        case ')':
            $inTokens[] = new Token(')',null);
            break;
        case '+':
            $inTokens[] = new Token('O','+');
            break;
        case '-':
            $inTokens[] = new Token('O','-');
            break;
        case '*':
            $inTokens[] = new Token('O','*');
            break;
        case '/':
            $inTokens[] = new Token('O','/');
            break;            
        case '^':  // is right associative 2^3^4 = 2^(3^4)
            $inTokens[] = new Token('O','^');
            break;
        case 's':
        case 'c':
        case 't':
            $threeLetterFun = $ch.$input[$pos].$input[$pos+1];
            switch ($threeLetterFun) 
            {
                case "sin":
                    $inTokens[] = new Token('F','SIN'); 
                    $pos+=2;
                    break;
                case "cos":
                    $inTokens[] = new Token('F','COS');
                    $pos+=2; 
                    break;
                case "tan":
                    $inTokens[] = new Token('F','TAN');
                    $pos+=2;
                    break;
                default:
                    $inTokens[] = new Token('ERR',$pos-1);
                    break;           
            }
        case ' ' :  // skip spaces
        case "\t":  // skip tabs
            break;             
        default:
            $inTokens[] = new Token('ERR',$pos-1); // everything else is an error 
            break;     
    } // end switch
} // end lexer

$lexErrors = 0;
foreach($inTokens as $token){
    if ($token->tokenName == "ERR"){
        echo "Unrecognised token at position".$token->tokenValue.PHP_EOL;
        $lexErrors++;
    }    
}
if ($lexErrors > 0) {
    echo "Input string is not a conforming expression".PHP_EOL;
    return;
}

// Now do the shunt yard alorithm
// Note: The parse converts to reverse Polish expression
// Note: Parse Tree is then easily built from this 
foreach($inTokens as $token){
    switch ($token->tokenName){
        case 'N':   // if token is a number then output its value
            $output[] = $token;
            break;  
        case 'O':   // if token is an operator first...
            // pop any (higher precedence) operators from the stack to the output
            if (count($operatorStack)>0){
                $stackTopToken = array_pop($operatorStack);
                if ($stackTopToken->tokenName == 'O'){
                    $output[] = $stackTopToken;    // output the operator
                }
                else {
                    array_push($operatorStack,$stackTopToken);     // not an operator so put back on the stack
                }   
            }
            // ... then push the operator     
            array_push($operatorStack,$token);
            break;
        case '(':   // if token is '(' then push on operator stack
            array_push($operatorStack,$token);
            break;
        case ')':   // if token is ')' then 
                    // pop operators to the output until '(' is reached
                    // output '('
                    // output ')'
            $openBraceFound = false;    
            while (count($operatorStack) > 0){
                $operator = array_pop($operatorStack);
                if ($operator->tokenName == '('){
                    $output[] = $operator;     // '('
                    $output[] = $token;        // ')'
                    $openBraceFound=true;
                    break;   // exit the loop when opening bracket is found
                }
                else {
                    $output[] = $operator; // pop operators to the output until '(' is reached
                }
            } 
            if (! $openBraceFound){
                $output[] = "error";
            }       
            break;    
    } // end switch
} // end foreach

// while there are still operators on the stack pop them to the output queue
while (count($operatorStack)>0){
    $output[] = array_pop($operatorStack);
}
// end of shunt-yard parse

echo "input expression .....".PHP_EOL;
echo $input.PHP_EOL;
echo "Tokenised after lex ....".PHP_EOL;
foreach($inTokens as $in){
    echo $in->tokenName." ".$in->tokenValue." ";
}
echo PHP_EOL."Reverse polish after parse...".PHP_EOL;
foreach($output as $out){
    echo $out->tokenName." ".$out->tokenValue." ";
}
echo PHP_EOL;

// now build the parse tree (maybe later build tree during parse?)
// This code blindly assumes that:
//    operators are binary so when we find one we can confidently pop 2 values
//    breaces are in apirs so when we find them we can confidently pop 1 value
foreach ($output as $token){

    switch ($token->tokenName){
        case  'N':
            array_push($nodeBuilder, new Leaf($token->tokenValue));
            break;
        case 'O':
            $v2 = array_pop($nodeBuilder);
            $v1 = array_pop($nodeBuilder);
            $op  = new Leaf($token->tokenValue);
            $newNode = new TwoOperandComposite($v1,$op,$v2);
            array_push($nodeBuilder, $newNode);
            break;
        case '(':
            array_push($nodeBuilder, new Leaf('(') ); 
            break;
        case ')':
            $lb = array_pop($nodeBuilder); 
            $v1 = array_pop($nodeBuilder);   // hopefully evaluates to a number! 
            $rb = new Leaf(')');
            $newNode = new BraceComposite($lb,$v1,$rb);
            array_push($nodeBuilder, $newNode);     
    }
}

if (count($nodeBuilder) <> 1)
{
    echo "Parse did not terinate: \$nodeBuilder should 
          have exacly 1 node, but has:".count($nodeBuilder).PHP_EOL;
}

$parseTree = array_pop($nodeBuilder);
echo "Evaluation via composite pattern tree...".PHP_EOL;
$result = $parseTree->processNode();
echo " = ".$result.PHP_EOL;

/*

*/
