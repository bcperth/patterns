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
* and output the result. Version 1 will ignore the "(" and ")" and hand code the precedence. 
* 
* We need 5 leaf nodes and 1 each of *,+ and / nodes.
*
* 
*
*/
// -------------------------------------------------------------------------
// Define an iterface to be implemented in every node

interface IProcess {                 // Interface definition
    public function processNode();   // process the node
}

// define the leaf class (which only holds a floating point number)
class Leaf implements IProcess {
    private $value;

    public function processNode(){ // leaf has nothing much to do
        return ($this->value);
    }
    // the set the value of the leaf
    public function __construct($value){
        $this->value = $value;
    }
}

abstract class Composite Implements IProcess {
    // we assume the parse tree is a binary tree so role of the / node is to 
    // add it's 2 children and return the product
    protected $child1;    // ref to child 1
    protected $child2;    // ref to child 1

    // the constructor links its 2 children
    public function __construct($child1,$child2){
        $this->child1 = $child1;
        $this->child2 = $child2;
    }
    abstract public function processNode(); // process the node
}

// define the "*" composite
class MultComposite extends Composite {
    public function processNode(){ // returns product of 2 children
        $val1 =  $this->child1->processNode();
        $val2 =  $this->child2->processNode();
        $result = $val1*$val2;
        echo $val1." * ".$val2." = ".$result.PHP_EOL;
        return($result);
        //return ($this->child1->processNode() * $this->child2->processNode());
    }
}

// define the "+" composite
class AddComposite extends Composite {
    public function processNode(){ // returns sum of 2 children
        $val1 =  $this->child1->processNode();
        $val2 =  $this->child2->processNode();
        $result = $val1+$val2;
        echo $val1." + ".$val2." = ".$result.PHP_EOL;
        return($result);
        //return ($this->child1->processNode() + $this->child2->processNode());
    }
}

// define the "/" composite
class DivComposite extends Composite { // returns diviion of 2 children
    public function processNode(){
        $val1 =  $this->child1->processNode();
        $val2 =  $this->child2->processNode();
        $result = $val1/$val2;
        echo $val1." / ".$val2." = ".$result.PHP_EOL;
        return($result);
        //return ($this->child1->processNode() / $this->child2->processNode());
    }
}

//---------------------------------------------------------------------
// Construct the tree by creating the nodes and linking them to get evaluate correctly
// ((a+b)*(c+d))/e
// the order of calculation is a+b then c+d then * sums then / by e

// create the nodes
$a = new Leaf(100);
$b = new Leaf(200);
$sum1 = new AddComposite($a,$b);
$c = new Leaf(300);
$d = new Leaf(400);
$sum2 = new AddComposite($c,$d);
$product1 = new MultComposite ($sum1,$sum2);
$e = new Leaf(77);
$div1 = new DivComposite($product1,$e);
// Tell the top node to process()
$div1->processNode();  // should traverse the tree and print result

