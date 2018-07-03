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
 * The example will be an arithmetic expression evaluator. Normally the tree would be produced by a parser  
 * depending on the expression entered. In this case we will hand code a tree to calculate the expression
 * ((a+b)*(c+d))/e. Then poke values for a,b,c,d,e into the leaf nodes and tell the tree to process itself 
 * and output the result. 
 * Version 1 will ignore the "(" and ")" and hand code the precedence. 
 * We need 5 leaf nodes and 1 each of *,+ and / nodes.
 * 
 * This is Version 2 - adding the braces
 * In this version the tree is changed from a binary tree - now composite ahve 3 children
 * This accommodates expressions comprising an operator and 2 operands
 * In this model leafs are either numbers, operators or braces
 * 
 * Note: In the context of parse trees - leaf nodes can be considered as terminal symbols
 * and composite nodes can be considered non-terminal symbols (some combination of
 * symbols that constutes a rule of the language).
 *    
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

// create the nodes
// Leaf objects for not value terminals
$plus = new Leaf("+");
$times = new Leaf("*");
$divide = new Leaf("/");
$lb = new Leaf("(");
$rb = new Leaf(")");

// now create value terminals and composites to execute ((a+b)*(c+d))/e
$a = new Leaf(113);
$b = new Leaf(57);
$sum1 = new TwoOperandComposite($a,$plus,$b);  // a+b
$expr1 = new BraceComposite($lb,$sum1,$rb);    // (a+b)

$c = new Leaf(-456.98);
$d = new Leaf(364.77);
$sum2 = new TwoOperandComposite($c,$plus, $d); // c+d
$expr2 = new BraceComposite($lb,$sum2,$rb);     // (c+d)

$prod = new TwoOperandComposite($expr1,$times, $expr2); // (a+b)*(c+d)
$expr3 = new BraceComposite($lb,$prod,$rb);             // ((a+b)*(c+d))

$d = new Leaf(37.99); 
$expr4 = new TwoOperandComposite($expr3,$divide, $d);  // ((a+b)*(c+d))/e

// Tell the top node to process()
$result = $expr4->processNode();  // should traverse the tree and print result
echo " = ".$result.PHP_EOL;
