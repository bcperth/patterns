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
 * Version 5 will turn the lexer and parser into objects (procedureal code to V4)
 * The lexer and parser may be able to use other patterns ...
 */
// -------------------------------------------------------------------------
/** Define an iterface to be implemented in every node
* Here is the "language" description - traditional infix expressions
* <E> ::= <E> + <E> | <E> - <E> | <E> * <E> | <E> / <E> | <E> ^ <E>     (Rule 1)
*     ::= sin(<E>) | cos(<E>) | tan(<E>)                                (Rule 2)
*     ::= ( <E> )                                                       (Rule 3)
*     ::= UNARYMINUS <E>                                                (Rule 5) 
*     ::= <E> !                                                         (Rule 6) Factorial not Implemented yet
*     ::= Number  (float or Integer or pi)                              (Rule 4)
* 
*  ACcording to Composite patter, make a class to represent specific nodes for each rule
*  Terminal symbols and Numbers are represented by leaf nodes. (rule 4)
*  Composite nodes do the actual calculations - with a class for each treatment of operands
*  All classed implement the same interface IProcess   (could be better called IEvaluate)
*/

// Interface definition
interface IProcess
{
    public function processNode();   // process the node
}

// Abstract class for the Composite nodes
abstract class Composite Implements IProcess
{
    // References to child nodes  
    protected $leftChild;
    protected $midChild;
    protected $rightChild;

    // the constructor establishes the links
    public function __construct($leftChild,$midChild,$rightChild)
    {
        $this->leftChild = $leftChild;
        $this->midChild = $midChild;
        $this->rightChild = $rightChild;
    }
    // This is where the calculation is done
    abstract public function processNode(); 
}

// Rule 1: Represents (E op E) where op is in (+,-,*,/,^)
class TwoOperandComposite extends Composite
{
    public function processNode()
    {
        // retrieve the left and right children and do the required operation
        $val1     =  $this->leftChild->processNode();   // returns a value
        $operator =  $this->midChild->processNode();    // the operator
        $val2     =  $this->rightChild->processNode();  // returns a value
        $result = 0;
        switch($operator){
        case "*":
            $result = $val1 * $val2;
            break;
        case "/":
            if ($val2 < 0.000000001){
                echo "warning: divide by zero encountered".PHP_EOL;
            }
            $result = $val1 / $val2;
            break;
        case "+":
            $result = $val1 + $val2;
            break;
        case "-":
            $result = $val1 - $val2;
            break;
        case "^":
            $result = pow($val1,$val2);  // gives $val1 ^ $val2
            break;
        }
        return($result);
        //return ($this->child1->processNode() * $this->child2->processNode());
    }
}

// Rule 2: Represents 'fun(E)' where fun in (sin, cos, tan ... any any additionals)
class FunctionComposite extends Composite
{
    public function processNode()
    {
        // returns mapping of x->fn(x)
        $function =  $this->midChild->processNode();    // returns the function
        $val1     =  $this->leftChild->processNode();  // returns a value
        $result = 0;
        switch($function){
        case "sin":
            $result = sin($val1);
            break;
        case "cos":
            $result = cos($val1);
            break;
        case "tan":
            $result = tan($val1);
            break;
        }
        return($result);
    }
}

// Rule 3: Represents braced expressions ie '(' E ')'
class BraceComposite extends Composite
{
    public function processNode()
    {
        // returns the value between braces
        $this->leftChild->processNode();           // force the '(' to be echoed (can be ommitted)
        $result = $this->midChild->processNode();  // returns a value
        $this->rightChild->processNode();          // force the ')' to be echoed (can be ommitted)
        return $result;
    }
}

// Rule 4:  represents ('Number') and all terminal symbols - (+,-,*,/,^,'(',')',sin, cos, tan)
class Leaf implements IProcess
{
    private $value;   // can be a numeric value or one of the terminals
    // the constructor initialises the value
    public function __construct($value)
    {
        $this->value = $value;
    }
    public function processNode()
    {
        if ($this->value =='u')
        {
            echo '-'; // for unary minus
        }
        else{
            echo $this->value;       // echoing terminals verifies the order of processing (cam be omitted) 
        }
        return $this->value;     // value is either a Number or other terminal
    } // implementing IProcess
}

// Rule 5: One operand operators (initially only Unary Minus)
class OneOperandComposite extends Composite
{
    public function processNode()
    {
        // returns product of 2 children
        $op   =  $this->midChild->processNode();    // returns the operator
        $val1 =  $this->leftChild->processNode();   // returns a value
        $result = 0;
        switch($op){
        case 'u':
            $result = -$val1;
            break;
        default:
            $result = $val1; // no op
        }
        return($result);
    }
}
//---------------------------------------------------------------------
// Step 1 - construct the "lexer"
// it will recognise:
// Numbers (floats or ints)    : outputs N token with value = numeric value
// Binary operators +,-,*,/,^  : outputs O token with value "/" etc
// Unary Operators -,          : outputs U token with value "-" etc
// Functions sin, cos, tan     : outputs F token with value "sin" etc
// Left Brace (                : outputs ( token
// Right Brace )               : outputs ) token
// unrecognised                : outputs ERR token with value of unrecognised input

// All tokens will have a name and a value 
class Token {
    public $tokenName;
    public $tokenValue;

    // the constructor initialises name and value
    public function __construct($name,$value)
    {
        $this->tokenName = $name;
        $this->tokenValue = $value;
    }
}

 // the lexer will process the input string token by token on demand
class Lexer {
    private $inExpression;     // input expression
    private $prevTokenName;    // needed to detect UnaryMinus
    private $pos = 0;          // position of next token in Expression
    private $len;              // length of Expression in Chars

    // the constructor saves the input exprssion and initialises things
    public function __construct()
    {
        $this->inExpression = "1+1";  // default expression if none supplied
        $this->len = strlen($this->inExpression);
        $this->pos = 0;
        $this->prevTokenName = 'NOTOK';
    }

    // pass an expression to the lexer
    public function setExpression($inExpression)
    {
        $this->inExpression = $inExpression;
        $this->len = strlen($inExpression);
        $this->pos = 0;
        $this->prevTokenName = 'NOTOK';
    }

    // create a new token and move cursor
    protected function newToken($tokName,$tokValue)
    {
        $this->prevTokenName = $tokName;
        $this->pos++;
        return (new Token ($tokName,$tokValue));
    }

    // public interface to provide next token to parser
    public function nextToken()
    {
        if ($this->pos >= $this->len)
        {
            return($this->newToken('END',null));   // string is parsed
        }
        // first for a number (possibly multidigit) starting at this position
        if (preg_match("/^[0-9]*\.?[0-9]+/",substr($this->inExpression,$this->pos),$match)){  // regex for floats and integers
            // Found number, is at $match[0]
            //echo "in number at pos:".$this->pos." and length of num is ".strlen($match[0]).PHP_EOL;
            $this->pos+= strlen($match[0])-1;            //advance to end of string
            return ($this->newToken('N',$match[0]));     // create the token
        }
        // Not a number so look for non-numeric tokens
        $ch = $this->inExpression[$this->pos];
        //echo $ch." at pos ".$this->pos.PHP_EOL;
        switch ($ch)
        {
            case '(':
            case ')':
                return($this->newToken($ch,null));
                break;
            case '-':  // test for UNARY MINUS
                if ($this->prevTokenName == 'NOTOK'){ // if first token is '-', must be UnaryMinus
                    return($this->newToken('U','u'));
                }
                if ($this->prevTokenName == '(' ||         // must be unary after '('
                    $this->prevTokenName == 'O' ||         // must be unary after any binary operator
                    $this->prevTokenName == 'U' )          // must be unary after any Unary operator
                {  
                    return($this->newToken('U','u'));
                }
                // if not unary then keep going - ie no break here.
            case '+':
            case '-':  // we reached here so must be binary operator minus
            case '*':
            case '/':        
            case '^':  // is right associative 2^3^4 = 2^(3^4)
                return($this->newToken('O',$ch));
                break;
            case 's':
            case 'c':
            case 't':
                $threeLetterFunc = $ch.$this->inExpression[$this->pos+1].$this->inExpression[$this->pos+2];
                switch ($threeLetterFunc) 
                {
                    case "sin":
                    case "cos":
                    case "tan":
                        $this->pos+=2;      // advance to end of string
                        return ($this->newToken('F',$threeLetterFunc));
                    default:
                        return ($this->newToken('ERR',$ch." ".$this->pos));             
                }
                break;
            case 'p':
                $twoLetterConstant = $ch.$this->inExpression[$this->pos+1];
                if ($twoLetterConstant == "pi")
                {
                    $this->pos+=1;        // advance to end of string
                    return ($this->newToken('N',3.141592653589793238));
                }
                else
                {
                    return($this->newToken('ERR',$ch." ".$this->pos)); 
                }
                break;
            case ' ' :  // skip spaces
            case "\t":  // skip tabs
                break;             
            default:
                return($this->newToken('ERR',$ch." ".$this->pos)); // everything else is an error 
                break;     
        } // end switch
    } //end method newToken()
} // end class Lexer
//--------------------------------

class ShuntYard 
{
    // Class to execute the shunt yard alorithm
    // Converts Token stream from Lexer to reverse Polish
    private $lexer;              // lexer object passed to the parser in constructor
    private $operatorStack = []; // the operator stack used by Shunting-yard algorithm

    // output options
    private $expressionValue;    // the final value of the input exression
    private $outTokens=[];       // the input expression converted to Reverse Polish after parse
    private $nodeBuilder=[];     // the parse tree for the supplied expression

    // a table of operators and their precedence and associativity'
    private $op=[];

    // the constructor saves the input exprssion and initialises things
    public function __construct($lexer)
    {
        $this->lexer = $lexer;

        $this->op['+'] = ['prec' => 1, 'assoc' => 'left'];
        $this->op['-'] = ['prec' => 1, 'assoc' => 'left'];
        $this->op['*'] = ['prec' => 2, 'assoc' => 'left'];
        $this->op['/'] = ['prec' => 2, 'assoc' => 'left']; 
        $this->op['^'] = ['prec' => 3, 'assoc' => 'right']; 
        $this->op['u'] = ['prec' => 4, 'assoc' => 'right'];// needs adjustment in code ^ so that -2^2 evaluates as -(2^2)
    }

    // add nodes to the parse-tree. Aways adds 
    // a leaf for every Terminal token
    // and adds composite node for every non-terminal 
    public function addNode($token){  
        switch ($token->tokenName){
            case  'N': // number
                array_push($this->nodeBuilder, new Leaf($token->tokenValue));
                break;
            case 'U':  // unary operator
                $v1 = array_pop($this->nodeBuilder);
                $op  = new Leaf($token->tokenValue);
                $newNode = new OneOperandComposite($v1,$op,null);
                array_push($this->nodeBuilder, $newNode);
                break;           
            case 'O': // binary operator
                $v2 = array_pop($this->nodeBuilder);
                $v1 = array_pop($this->nodeBuilder);
                $op  = new Leaf($token->tokenValue);
                $newNode = new TwoOperandComposite($v1,$op,$v2);
                array_push($this->nodeBuilder, $newNode);
                break;
            case 'F': // function
                $v1 = array_pop($this->nodeBuilder);
                $fn = new Leaf($token->tokenValue);
                $newNode = new FunctionComposite($v1,$fn,null);
                array_push($this->nodeBuilder, $newNode);
                break;    
            case '(':
                array_push($this->nodeBuilder, new Leaf('(')); 
                break;
            case ')':
                $lb = array_pop($this->nodeBuilder); 
                $v1 = array_pop($this->nodeBuilder);   // hopefully evaluates to a number! 
                $rb = new Leaf(')');
                $newNode = new BraceComposite($lb,$v1,$rb);
                array_push($this->nodeBuilder, $newNode);
                break;
            case 'ERR':
                break;     
        } // end switch
     } // end function addNode()}

    public function parse()
    {
        // the parser consumes token from the lexer until lexer supplies an 'END' token.
        // when the parser "outputs" a token = output can mean any of 3 things
        // 1. it can perform an evaluation immediately
        // 2. it can construct an element of the parse-tree
        // 3. it can just save the tokens in Reverse Polish order 
        // For fun we will do all 3
        for($token=$this->lexer->nextToken(); $token->tokenName !='END'; $token=$this->lexer->nextToken()){
            echo $token->tokenName.' '.$token->tokenValue.' '; // echoing the lexer output 
            switch ($token->tokenName){
                case 'N':   // if a number then output it
                    $this->outTokens[] = $token;  // 
                    $this->addNode($token);       //
                    break;
                case 'F':   // if a function then push it on the operator stack
                    array_push($this->operatorStack,$token);
                    break;
                case 'O': 
                case 'U':  
                    // if the op stack is not empty, first pop and output functions and higher precedence ops
                    while(count($this->operatorStack)>0){
                        // pop operator and test    
                        $popTok = array_pop($this->operatorStack);
                        $popName = $popTok->tokenName;
                        $popValue = $popTok->tokenValue;

                        // first: conditionally swap the precedence of UnaryMinus and ^ to make -x^y evaluate as -(x^y) instead of (-x)^y
                        if (($popValue == 'u') && ($token->tokenValue == '^'))
                        {
                            // leave 'u' on the stack in this case only 
                            array_push($this->operatorStack,$popTok);
                            break; // and exit loop
                        }
                        else
                        { 
                            if ( $popName == 'F' ||  // its a function
                                ($popName == 'O' || $popName == 'U') && (  // Its an operator
                                    $this->op[$popValue]['prec'] >  $this->op[$token->tokenValue]['prec'] ||  // and its higher precedence
                                    // or same precedence and right associative
                                    ($this->op[$popValue]['prec'] == $this->op[$popValue]['prec'] && $this->op[$popValue]['assoc']== 'left')
                                )) 
                            {
                                // it meets one of the criteria for outputting
                                $this->outTokens[] = $popTok;
                                $this->addNode($popTok);
                            }
                            else
                            {   // does not meet criteria for outputing so push it back 
                                array_push($this->operatorStack,$popTok);
                                break; // and exit loop
                            }
                        }
                    } // end while
                    // we have done popping any higher operators so we can now push the new token    
                    array_push($this->operatorStack,$token);
                    break;
                case '(':   // if token is '(' then push on operator stack
                    array_push($this->operatorStack,$token);
                    break;
                case ')':   // if token is ')' then pop operators to the output until '(' is reached                
                    $openBraceFound = false;    
                    while (count($this->operatorStack) > 0){
                        $operator = array_pop($this->operatorStack);  
                        if ($operator->tokenName == '('){ // '(' is reached
                            $this->outTokens[] = $operator;     // output '('
                            $this->outTokens[] = $token;        // output ')'
                            $this->addNode($operator);
                            $this->addNode($token);
                            $openBraceFound=true;
                            break;   // exit the loop when opening bracket is found
                        }
                        else {
                            $this->outTokens[] = $operator; // pop operators to the output until '(' is reached
                            $this->addNode($operator);
                        }
                    } 
                    if (! $openBraceFound){
                        $errToken = new Token("ERR","Closing brace not found");
                        $this->outTokens[] = $errToken;
                        $this->addNode($errToken);
                    }       
                    break;
                default:
                    $errToken = new Token("ERR","unrecognised token");
                    $this->outTokens[] = $errToken;
                    $this->addNode($errToken);
            } // end switch
        } // end for

        // while there are still operators on the stack Output them
        while (count($this->operatorStack)>0){
            $opToken = array_pop($this->operatorStack);
            $this->outTokens[] = $opToken;
            $this->addNode($opToken);
        }
        echo PHP_EOL;
        echo "parse done".PHP_EOL;
        return ($this->nodeBuilder);
        //return ($this->outTokens);

    } // end for loop
}// end class ShuntYard   
//-------------------------------

// now use the lexer and parser to create a parse-tree for any conforming input expression
$expression = "((2+3)*sin(pi/2))^9^.5";
$expression = "---6^---2";
echo "input expression ".PHP_EOL.$expression.PHP_EOL;

$lex = new Lexer();                     // make the lexer object
$shuntYard = new ShuntYard($lex);       // make the parser object
$lex->setExpression($expression);       // enter the expression to be evaluated.
echo "lexer output".PHP_EOL;
$nodeBuilder = $shuntYard->parse();     // parse method returning the parse-tree
$parseTree = array_pop($nodeBuilder);   // recover the head of the parse-tree 
echo "Evaluation via composite pattern tree...".PHP_EOL;
$result = $parseTree->processNode();
echo " = ".$result.PHP_EOL;

return;