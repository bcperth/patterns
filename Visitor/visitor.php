<?php
/** Visitor: This pattern is about extending the functionality of a class, without changing the class.
 * 
 * Often Visitor pattern is applied to a structure* of Elements. (*array, linked list, tree etc)
 * The only requirement of the Element objects is that their classes implement the Visitor Interface.
 * Note: With Composite Pattern and Iterator Pattern the Elements must all be subclasses of a common parent. 
 * In contrast you can iterate a structure of Elements of any object type with Visitor Pattern provided
 * each element class implements the acceptVisitor Interface or method().
 * 
 * Now suppose we have an array of document-like objects and we want to add a new function to parse
 * the document content. We could define a Parse Interface with a parse() method and then implement 
 * parse() in each class. BUT ... for whatever reason, we cannot modify the classes. 
 * 
 * If the designers anticipate that such extension of functionality would likely be needed, 
 * then (adopting the Visitor Pattern) they add acceptVisitor() method to every element class.
 * This is very simply:
 *      public function acceptVisitor($visitor){ 
 *          $visitor->visit($this);
 *      } 
 * 
 * The additional functionality is provided in a set of Visitor classes.
 * Each visitor class implements a separate visitElement() method for each type of element.
 * Using the parse example this might be simply:
 *      class parseVisitor{
 *          // pretending for now that PHP allowed overloaded methods .....
 *          $visitElement(PHPElement $element)  { // PHP parser };
 *          $visitElement(HTMLElement $element) { // HTML parser }; 
 *          $visitElement(CPPlement $element)   { // CPP parser };  
 *      }
 * 
 * To apply the parser to a Elements of different types.
 * 
 *      $parseVisitor = new ParseVisitor;
 *      $htmlDoc = new HTMLDoc;
 *      $htmlDoc->acceptVisitor($parseVisitor);
 * // acceptVisitor() passes $this (ref to an HTMLDoc) to visitElement() method of $parseVisitor.
 * // This should result in visitElement(HTMLElement $element) to be called.
 * 
 * Finally if we had an array of Docs of different types:
 *      foreach ($arrayOfDocs as $doc){
 *           $doc->acceptVisitor($parseVisitor);  // should invoke the correct parser
 *      }
 * 
 * In languages like c++ or Java, where overloaded methods are allowed, this would work.
 * Before addressing the PHP specific problem here are some observations:
 * 
 * 1. Visitor Pattern implements "double dispatch" (see separate doc "single and multiple dispatch")
 *    That is at runtime we want to selected the correct version of visitElement based both on
 *    the type of the Element and the type of the Visitor.
 * 1a. 
 * 2. Processing of the array of Elements using the Visitor object is similar to extending
 *    the Element classes with a parse() method (for example). However, the parse() method does
 *    not have access to private or protected parts of Element. This may or not matter, but in 
 *    general, the interface to Element classes needs to be broad enough to let Visitor objects
 *    do what they must do.  
 * 
 * 
 */   


// Define the class and sub-classes 
Class Client {
    public function accept(Visitor $vis){
        $vis->visit($this);
    }
}

class HTMLClient extends Client {
    public function whoami(){echo "I am an HTML Client".PHP_EOL;}
}
class PHPClient extends Client {
    public function whoami(){echo "I am an PHP Client".PHP_EOL;}
}
class CPPClient extends Client {
    public function whoami(){echo "I am an CPP Client".PHP_EOL;}
}


abstract class Visitor {
    abstract function visit(Client $client);
}

class HelloVisitor extends Visitor {

    public function visit(Client $vis){
        echo "I am HelloVisitor to client".PHP_EOL;
        //$vis->whoami();
    }
}

$visitor = new HelloVisitor;

$clients = array();
$clients[] = new HTMLClient;
$clients[] = new PHPClient;
$clients[] = new CPPClient;

foreach($clients as $client){
    $client->whoami();
    $client->accept($visitor);
}