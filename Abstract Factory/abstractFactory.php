<?php
// Abstract Factory: 
// This pattern is an extension of Factory Method, allowing families of product classes 
// to be created at run time. //ie the client will need a coordinated "set" of products 
// from a pallette of 'n' sets.
//
// An editor/IDE requires a coordinated set of Scanner and Parser objects
// dependig on which type of document is being edited.
//
// Taking the Document example first make an abstract Factory class
// with concrete sub-classes for each language. 
abstract class AbstractFactory {      
    abstract function createParser();
    abstract function createScanner();
}      
class HTMLFactory extends AbstractFactory {
    public function createParser() { return new HTMLParser();}
    public function createScanner(){ return new HTMLScanner();}
}          
class JSFactory extends AbstractFactory {
    public function createParser() { return new JSParser();}
    public function createScanner(){ return new JSScanner();}
} 
class PHPFactory extends AbstractFactory {
    public function createParser() { return new PHPParser();}
    public function createScanner(){ return new PHPScanner();}
} 
// now make the Parser and Scanner classes
interface IScanner {public function scan();}
class HTMLScanner implements IScanner{ public function scan(){echo "I scan HTML Docs".PHP_EOL;}}
class JSScanner implements IScanner{ public function scan(){echo "I scan JS Docs".PHP_EOL;}}
class PHPScanner implements IScanner{ public function scan(){echo "I scan PHP Docs".PHP_EOL;}}
interface IParser {public function parse();}
class HTMLParser implements IParser{ public function parse(){echo "I parse HTML Docs".PHP_EOL;}}
class JSParser implements IParser{ public function parse(){echo "I parse JS Docs".PHP_EOL;}}
class PHPParser implements IParser{ public function parse(){echo "I parse PHP Docs".PHP_EOL;}}

// now make the editor class
class Editor{
      protected $parser;    // to hold the parser object
      protected $scanner;   // to hold the scanner object

      public function parse() {$this->parser->parse();}
      public function scan()  {$this->scanner->scan();}

      public function __construct($factory){
          $this->parser = $factory->createParser();
          $this->scanner = $factory->createScanner();
      }
}      
       
// Now create some docs and see what happens
$HTMLfactory = new HTMLFactory;
$JSfactory = new JSFactory;
$PHPfactory = new PHPFactory;

$editor = new Editor($PHPfactory);  // should create a PHP Editor
$editor->scan();      // does language specific scanning
$editor->parse();     // does language specific parsing
echo PHP_EOL;
$editor = new Editor($HTMLfactory);  // should create a HTML Editor
$editor->scan();      // does language specific scanning
$editor->parse();     // does language specific parsing
echo PHP_EOL;
$editor = new Editor($JSfactory);  // should create a JS Editor
$editor->scan();      // does language specific scanning
$editor->parse();     // does language specific parsing


