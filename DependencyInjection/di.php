<?php

/**
 * Dependency Injection (DI) involves injecting one or more Dependency objects into a Client object.
 * "Injection" is done via the constructor (for mandatory dependents) and
 * via a setter method (for optional dependents) ... as a rule of thumb.
 * 
 * Dependent objects are first created external to the Client.
 * Object creation can be done in may ways (apart from the obvious)
 * including Factory Method, Abstract Factory and 
 * DI Containers / Inversion of Control (IoC) Containers / Service Locators (all the same)
 * 
 * Its a key point of DI that the Client does not "know" anything about how its dependents are
 * created.
 * 
 * However, the Client DOES know with "Factory Method", "Abstact Factory" and "Service Location".
 * These three patterns give a level of control to the Client to create its dependents.
 * 
 * With the Client has NO control over dependency creation. 
 * This is explains the term "Inversion of Control".
 * 
 * In this mini project we will create a DI container in steps from the simplest possible
 * to the most feature-rich that is practical without the use of external libraries. 
 */
// -------------------------------------------------------------------------
/* Simplest Possible DI Container */
class S01_DependentObj{  // Simple Dependency Object
    public function doDependencyStuff(){
        echo "I am the simplest possible dependency object".PHP_EOL;
    }
}

class S01_DIContainer {   // Simplest Possible DI Container 
    public function makeS01_DependentObject(){
        return new S01_DependentObj;
    }
}

class Client{        // Simplest Possible Client Object
    private $DependentObj;

    public function __construct($DependentObj){  // DI using constructor method
        $this->DependentObj=$DependentObj;
    }

    public function doClientStuff(){
        echo "I am the Client Object - NEVER CHANGING".PHP_EOL;
        $this->DependentObj->doDependencyStuff();
    }
}
//make it all work
$container = new S01_DIContainer;
$dependency1 = $container->makeS01_DependentObject();
$client = new Client($dependency1);  // Injecting the dependent object
echo PHP_EOL."Simplest Possible DI Container.....".PHP_EOL;
$client->doClientStuff();

// Could also make multiple Dependent objects or subclasses of S01_DependentObj
// as or anything that implements the "public function doDepencyStuff()" interface.
// The point is the Client has no control over creation BUT expects its 
// depencies to have the correct Interface.

// -------------------------------------------------------------------------
/* DI Container with Parameters to configure the Dependent object*/
class S02_DependentObj{  // Dependency Object with parameters
    private $message;
    private $number;

    public function doDependencyStuff(){
        echo $this->message.$this->number." parameters".PHP_EOL;
    }

    public function __construct(array $params){
        $this->message = $params[0];
        $this->number = $params[1];
    }
}

class S02_DIContainer {   // DI Container with Parameter Array
    public function makeS02_DependentObject(array $params){
        return new S02_DependentObj($params);
    }
}

// Client class stays the same so no need for new class

//make it all work
$container = new S02_DIContainer;
$dependency1 = $container->makeS02_DependentObject(array("I am modified dependent object with array of ",2));
$client = new Client($dependency1);  // Injecting the dependent object
echo PHP_EOL."DI Container modifies the Dependent object ......".PHP_EOL;
$client->doClientStuff();

// -------------------------------------------------------------------------
/* DI Container that generates a Dependency Graph (tree) */
class S03_DependentObj{ // Dependency Object with parameters
    private $subDepObj1;  // dependency of the Dependent Object
    private $subDepObj2;  // dependency of the Dependent Object

    public function doDependencyStuff(){
        echo "I am dependent Object with 2 sub-dependents".PHP_EOL;
        echo $this->subDepObj1->doSubDepencyStuff();
        echo $this->subDepObj2->doSubDepencyStuff();
    }

    public function __construct($subDepObj1,$subDepObj2){
        $this->subDepObj1 = $subDepObj1;
        $this->subDepObj2 = $subDepObj2;
    }
}

Class S03_SubDependent{
    private $text;
    public function doSubDepencyStuff(){
        echo $this->text.PHP_EOL;
    }
    public function setText($text) {$this->text = $text;}
}

class S03_DIContainer {   // DI Container with hard-wired dependency tree
    public function makeS03_DependentObject(){
        $subDep1 = new S03_SubDependent;
        $subDep1->setText("I am Sub-dependent object 1");  // hard configuring via setter
        $subDep2 = new S03_SubDependent;
        $subDep2->setText("I am Sub-dependent object 2");  // hard configuring via setter
        return new S03_DependentObj($subDep1,$subDep2);   // hard-wiring the sub-dependents
    }
}

// Client class stays the same so no need for new class

//make it all work
$container = new S03_DIContainer;
$dependency1 = $container->makeS03_DependentObject();  // with 2 subdependents this time
$client = new Client($dependency1);  // Injecting the dependent object
echo PHP_EOL."DI Container hard-wired to make a dependency tree (SO3).....".PHP_EOL;
$client->doClientStuff();

/**
 * This is as far as we can go with hard-wired DI Containers, except to explore different ways
 * to set parameters etc.
 * The next step is to make the DI Container more general purpose.
 * It will be able to make multiple object types - still with hardwired dependencies
 * 
 * The last step will be to try to automate it using Introspection and Reflection
 * 
 * After that the exercise will be to use a publicly available DI Container for PHP
 * To be decided which one.
 */


