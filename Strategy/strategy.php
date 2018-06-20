<?php

/**
* The strategy pattern solves potential problems with inheritance, where real world objects
* of a certain type have multiple methods (algorithms) each of which can have several variations. 
* In these situations, the inheritance tree cannot remain simple,  with resorts to duplication
* of method code, or by using multiple inheritance, traits (PHP) or mixins.
*
* For example, let's say we need to create instances of "programmer" objects, where each programmer has
* different procedures (methods) that depend on language, dev methodology and source control. 
* The methods have names design(), build() and save(). We can have multiple versions of each method
* according to the design methodology - Agile, Scrum etc, language - php, nodejs etc and source control
* git, sccm etc.
* One way to solve the problem is to use 3 attributes 
* int designProc;
* int buildLang;
* int saveTool;
* ... and then define design(), build() and save() with case statements on the attribute values
* to do the specific tasks needed depending on the language, dev methodology and source control.
*  
* The strategy pattern allows different "programmer" objects to be created by "injecting" 
* the appropriate versions of each of the design(), build() and save() methods.
* This allows new versions of "programmer" to be created purely by adding new versions of the
* design(), build() and save() methods - with no changes to any other code.
* The example below uses dependency injection, to direct the constructor of the programmer object
* which mix of methods to use.  
*  
*  
*/
// -------------------------------------------------------------------------
interface IDesign {  // Interface definition for design method (abstract Strategy)
    public function design();
}

class ScrumDesigner implements IDesign { // Concrete TDDdesign strategy variation
    public function design(){
        echo "I am a Scrum designer".PHP_EOL;
    }
}

class AgileDesigner implements IDesign {// Concrete Agiledesign strategy variation
    public function design(){
        echo "I am an Agile designer".PHP_EOL;
    }
}
// -------------------------------------------------------------------------
interface IBuild {// Interface definition for build method (abstract Strategy)
    public function build();
}
class PHPBuilder implements IBuild {// Concrete PHPBuild strategy variation
    public function build(){
        echo "I am a PHP programmer".PHP_EOL;
    }
}

class NodejsBuilder implements IBuild {// Concrete NodejsBuild strategy variation
    public function build(){
        echo "I am a Nodejs programmer".PHP_EOL;
    }
}
// ------------------------------------------------------------------------- 
interface ISave {// Interface definition for save method (abstract Strategy)
    public function save();
}

class GitSaver implements ISave {// Concrete GitSave strategy variation
    public function save(){
        echo "I use Git for source control".PHP_EOL;
    }
}
class SccMSaver implements ISave {// Concrete SccMSave strategy variation
    public function save(){
        echo "I use SccM for source control".PHP_EOL;
    }
}
// ------------------------------------------------------------------------- 
/* Here we define the programmer class.

*/
class Programmer {

    private $designObj;  
    private $buildObj;
    private $saveObj;
    
    // the constructor injects the required algorithm (method) variations.
    public function __construct($designObj,$buildObj,$saveObj){
        $this->designObj = $designObj;
        $this->buildObj  = $buildObj;
        $this->saveObj   = $saveObj;
    }
     
    public function design(){
        $this->designObj->design();
    }
    public function build(){
        $this->buildObj->build();
    }
    public function save(){
        $this->saveObj->save();
    }

    // a method to exercise the other methods
    public function whoAmI(){ 
       echo PHP_EOL."Each object's methods depend on what was injected".PHP_EOL;
       $this->design();
       $this->build();
       $this->save();
    }
}

// make an object for every possible strategy
$AgileDesigner = new AgileDesigner;
$ScrumDesigner = new ScrumDesigner;

$PHPBuilder = new PHPBuilder;
$NodejsBuilder = new NodejsBuilder;

$GitSaver = new GitSaver;
$SccMSaver = new SccMSaver;

// make programmer instances mixing and matching the variations using dependency injection
$ProgrammerType1 = new Programmer($AgileDesigner,$PHPBuilder,$SccMSaver);
$ProgrammerType2 = new Programmer($AgileDesigner,$NodejsBuilder,$GitSaver);
$ProgrammerType3 = new Programmer($ScrumDesigner,$PHPBuilder,$GitSaver);
$ProgrammerType4 = new Programmer($ScrumDesigner,$NodejsBuilder,$SccMSaver);

// let each object exercie its design(), build() and save() methods
$ProgrammerType1->whoAmI();
$ProgrammerType2->whoAmI();
$ProgrammerType3->whoAmI();
$ProgrammerType4->whoAmI();
// end of program