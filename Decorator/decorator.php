<?php

/**
* The decorator pattern provides for adding layers of additional behaviour to a base object  
* For example consider a class that reads text from a file.
* We may want to do additional processing such as ,spell check, compress, translate, covert case etc
* Its not feasible to have a subclasses representing each combination of postprocessing options
* The decorator pattern allows adding of layers to a base class, with each layer taking care of 
* one responsibility. Obviously the order in which the decorators are applied matter in this case.
* In other use cases, order may not matter and decorators can be applied in any order to get the same result.
* The decorator class acts like a pipe mechanism - working from the inner - outwards through all layers (decorators).
* 
* Decorators work like this:
* An abstract component base class is defined having one or more methods - say readStream()
* One or more concrete component classes are defined - such as MemoryStream and FileStream
* Each of these provide implementations of readStream()
*
* An abstract decorator class is defined inheriting from the abstract component class
* The abstract decorator class retains the abstract readStream() method.
* The abstract decorator class adds a reference to its parent component object.
* (The decorator object therefore "is a component" and "has a component").
* Create multiple decorator classes - each providing and implementation of readStream().
* The implementation first calls parent->readStream() then follows with its own decorator processing. 
*
* Create an instance of one of the component classes - eg FileStream
* Create an instance of one of the decorator classes - passing the component class in the constructor.
* Create another instance of a decorator class - passing the above decorator class (a component) in the constructor.
* Create as many decorator instances as needed.
* We are effectively creating a linked list of objects.
* Processing via readStream() starts at the base component and works outward - with each decorator
* adding additional processing.
* Note in the file stream example it is advisable that the text buffer is not part of the 
* component as otherwise it will be duplicated in each component. Instead include a reference to an external buffer.
* The base component and each decorator will process the buffer according to its assigned task.
*
* In the example below the base class reads some text from a file
* We will add a decorator that converts to uppercase
* We will add a decorator to covert it into a HTML paragraph.
* We will add a decorator to covert it into a HTML div.
*/
// -------------------------------------------------------------------------

$textBuffer;

// Create the abstract base component class
abstract class AbstactTextComponent  { 
    abstract function readBuffer();
}

// make a concrete component class
class  FileTextComponent extends AbstactTextComponent {// Concrete class that reads from a file into a buffer
    
        public function readbuffer(){
        global $textBuffer;   // where to put our read stuff
        $textBuffer = file_get_contents("fileToRead.txt");   // reads a file into a string
        echo "text as read by Component .....";
        echo $textBuffer,PHP_EOL;
    }
}
// -------------------------------------------------------------------------
// create an abstract decorator class
abstract class AbstractTextDecorator {
   
    public $parentTextComponent;
    abstract function readBuffer();

    public function __construct($parentTextComponent){
        $this->parentTextComponent = $parentTextComponent;
    }
}

// create a concrete decorator class to convert to upper case
class ToUpperTextDecorator extends AbstractTextDecorator {
    public function readBuffer(){ // implement the abstract readBuffer() method
        global $textBuffer;
        $this->parentTextComponent->readBuffer();  // buffer should now have the text read from readme file
        $textBuffer = strtoupper($textBuffer);   // buffer is now upper case
        echo "text after UCase decorator ....";
        echo $textBuffer,PHP_EOL;
    }
}

// create a concrete decorator class to convert to HTML paragraph
class ToHTMLParaTextDecorator extends AbstractTextDecorator {
    public function readBuffer(){ // implement the abstract readBuffer() method
        global $textBuffer;
        $this->parentTextComponent->readBuffer();  // buffer should now have the text read from readme file
        $textBuffer = "<p>".$textBuffer."<p>";     // buffer is now an HTMP paragraph
        echo "text after HTMLp decorator ....";
        echo $textBuffer,PHP_EOL;
    }
}

// create a concrete decorator class to convert  to HTML div
class ToHTMLDivTextDecorator extends AbstractTextDecorator {
    public function readBuffer(){ // implement the abstract readBuffer() method
        global $textBuffer;
        $this->parentTextComponent->readBuffer();  // buffer should now have the text read from readme file
        $textBuffer = "<div>".$textBuffer."<div>";     // buffer is now an HTMP Div
        echo "text after <div> decorator ....";
        echo $textBuffer,PHP_EOL;
    }
}

// ------------------------------------------------------------------------- 
// make a component and add decorators
$fileTextComponent = new FileTextComponent;  // create the base Component
$ToUpperTextDecorator = new ToUpperTextDecorator($fileTextComponent);          // Decorate to upper Case
$ToHTMLParaTextDecorator = new ToHTMLParaTextDecorator($ToUpperTextDecorator); // Decorate to HTML para
$ToHTMLDivTextDecorator = new ToHTMLDivTextDecorator($ToHTMLParaTextDecorator); // Decorate to HTML div
$ToHTMLDivTextDecorator->readBuffer();  // this executes 4 x readFile() starting at component and working out to last decorator

// end of program