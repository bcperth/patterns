<?php
/* Singleton: This is a mini-pattern that provides a way to prevent more than one object of a class
* to be instantiated. It does this by making the constructor private (prevent "new" from being used) 
* and by providing a static method eg called Instance(), to create an instance. 
* There is also a static variable eg called $inst (php) which is either NULL (no object create yet)
*  or = the referece to the created object. Instance() checks $inst and create a new object if NULL and 
* set $inst to that object. It then returns $inst as the new object (which may not be new!).
*
* Singletons are criticised buy may be useful in certain situations.
* They used in the stateV1.php pattern example
*
*/
// Define the singleton class
class Singleton { 

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Singleton;
        }
        return $inst;
    }
    private function __construct() { }  // preventing any using "new"

    public function sayHello($objectName){
        echo "I am ".$objectName."singleton object".PHP_EOL;
    }
}

// now create some instances
$object1 = Singleton::Instance();
$object1->sayHello("Object1: first instance of a ");
$object2 = Singleton::Instance();
$object1->sayHello("Object2: second instance of a ");
if  ($object1===$object2){
    echo "We are equal and are the same object!".PHP_EOL;
}
else{
    echo "We are different objects!".PHP_EOL;
}