<?php

/**
 * Factory Method: The pattern has Creators (clients) that need to create Products (objects). 
 * Usually, Creator sub-classes need different versions of Products.
 * Delegating the creation of Products to a factory method has advantages over
 * instantiating via "new" in the code.
 * The factory method has 3 variations, as described in GoF.
 * The examples below cover each variation with comments.
 * 
 * Variation 1.  Abstract Creator
 * Used when every Creator subclass must have its own specific product 
 * 
 * Variation 2.  Concrete Creator
 * Used when a Creator or its sub-classes can either use the default product or a special product
 * 
 * Variation 3.  Concrete Creator, parameterised
 * Used when a Creator or its sub-classes can specify a product from a range of available products
 * or take a default, or sub-classes can have additional product choices etc
 */

//--------------------------------------------------
// Variation 1.  Abstract Creator
// Used when every Creator subclass must have its own specific product 
//--------------------------------------------------
// Creator classes
abstract class Flower {  // Abstract Creator
    protected $fertiliser;
    protected $name;
    abstract function createFertiliser();    // abstract factory method
    public function show(){
        echo "I am a ".$this->name." and I use ";
        $this->fertiliser->whoami();
    }
}

class Rose extends Flower { // Concrete Creator
    public function createFertiliser()      // concrete factory method
    {
        $this->fertiliser = new RoseFertiliser;
    }
    public function __construct(){$this->name="rose";}
}

class Tulip extends Flower { // Concrete Creator
    public function createFertiliser()      // concrete factory method
    {
        $this->fertiliser = new TulipFertiliser;
    }
    public function __construct(){$this->name="tulip";}
}
//----------------------------
// Fertiliser Classes all have the same Interface
interface IFertiliser {  // All fertilisers must implement 
    public function whoami();
}
class RoseFertiliser implements IFertiliser { // Rose
    public function whoami()  {echo "rose fertiliser".PHP_EOL;}
}
class TulipFertiliser implements IFertiliser { // Tulip
    public function whoami()  {echo "tulip fertiliser".PHP_EOL;}
}
class GeneralFertiliser implements IFertiliser { // General
    public function whoami() { echo "general fertiliser".PHP_EOL;}
}
class OrganicFertiliser implements IFertiliser { // Organic
    public function whoami() { echo "organic fertiliser".PHP_EOL;}
}
class FirFertiliser implements IFertiliser { // Organic
    public function whoami() { echo "special fir fertiliser".PHP_EOL;}
}

// Now create an array of flowers
$flowers[] = new Tulip;
$flowers[] = new Rose;
$flowers[] = new Tulip;
$flowers[] = new Rose;

// From here the code is independent of flower variations
// the subclasses of Creator decide what type of Product they need
echo "Variation 1: every flower has a flower specific fertiliser".PHP_EOL;
foreach($flowers as $flower){
    $flower->createFertiliser();   // create the matching fertiliser for each flower
    $flower->show();
}

//--------------------------------------------------
// Variation 2.  Concrete Creator
// Used when a Creator or its sub-classes can use either the default product or a special product
//--------------------------------------------------

// Creator classes
class Shrub {  // concrete Creator
    protected $fertiliser;
    protected $name;
    function createFertiliser()    // concrete factory method
    {
        $this->fertiliser = new GeneralFertiliser; 
    }
    public function show(){
        echo "I am a ".$this->name." and I use ";
        $this->fertiliser->whoami();
    }
}

class Hybiscus extends Shrub { // Concrete Creator
    // I need special organic fertiliser
    public function createFertiliser()      // override factory method
    {
        $this->fertiliser = new OrganicFertiliser;
    }
    public function __construct(){$this->name="hybiscus";}
}

class Privet extends Shrub { // Concrete Creator
    // i'm good with the default fertiliser
    public function __construct(){$this->name="privet";}
}
//----------------------------

// Now create an array of shrubs
$shrubs[] = new Hybiscus;
$shrubs[] = new Privet;

// From here the code is independent of shrub variations
// the subclasses of Creator may or not need special fertiliser
echo PHP_EOL."Variation 2: Some shrubs can use general fertiliser".PHP_EOL;
foreach($shrubs as $shrub){
    $shrub->createFertiliser();   // create fertiliser for each shrub
    $shrub->show();
}

//--------------------------------------------------
// Variation 3.  Concrete Creator, parameterised
// Used when a Creator or its sub-classes can specify a product from a range of available products
// or take a default, or sub-classes can have additional product choices etc
//--------------------------------------------------
// Creator classes
class Tree {  // concrete Creator
    protected $fertiliser;
    protected $name;
    function createFertiliser($fertiliserType)    // concrete factory method
    {
        switch($fertiliserType)
        {
            case "organic":
                $this->fertiliser = new OrganicFertiliser;
                break;
            case "fir":
                $this->fertiliser = new OrganicFertiliser;
                break;                
            case "pine":
                $this->fertiliser = new GeneralFertiliser;
                break; 
            default:
                $this->fertiliser = new GeneralFertiliser;
        }
 
    }
    public function show(){
        echo "I am a ".$this->name." and I use ";
        $this->fertiliser->whoami();
    }
}

class Fir extends Tree { // Concrete Creator
    public function createFertiliser($fertiliserType)  // override factory method
    {
            if($fertiliserType == "fir"){
                $this->fertiliser = new FirFertiliser;
            }
            else if ($fertiliserType == "general"){ 
                $this->fertiliser = new OrganicFertiliser;;
            }      
            else{ 
                parent::createFertiliser($fertiliserType);
            }
    }
    public function __construct(){$this->name="fir";}
}

class Pine extends Tree { // Concrete Creator
    // i'm good with the default fertiliser
    public function __construct(){$this->name="pine";}
}
//----------------------------

// Now create an array of trees, mixing and matching the fertilisers
$trees[] = new Fir;
$trees[0]->createFertiliser("fir");    // will be "special fir"
$trees[] = new Fir;
$trees[1]->createFertiliser("general"); // will change to organic "organic"
$trees[] = new Fir;
$trees[2]->createFertiliser("organic"); // will be "organic"
$trees[] = new Fir;
$trees[3]->createFertiliser("any");     // will change to "general"
$trees[] = new Pine;
$trees[4]->createFertiliser("any");    // will change to "general"
$trees[] = new Pine;
$trees[5]->createFertiliser("fir");    // will change to "organic"

// From here the code is independent of tree variations
// the subclasses of Creator may or not need special fertiliser
echo PHP_EOL."Variation 3: Parameterisation allows fertilisers to be 'mixed and matched'".PHP_EOL;
foreach($trees as $tree){
    $tree->show();
}