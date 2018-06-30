<?php

/**
* State: When a system can be modelled as a "finite state machine (FSM)"  ie system has a constant fixed number states. 
* The system behaves differently in each state AND has fixed rules to transition between states. 
* (ref a state transition diagrams used to model such systems). 
* All FSM's have inputs and outputs. When inputs occur, the outputs depend on the current state (Moore FSM).
* Outputs could also depend on the transition between states (Mealy FSM)
* A trivial example is a counter, that cycles between 0 and 9 - ie outputs 0 to 9.
* The counter recognises the "inputs" clear(), and count() and there are 10 internal states.
* 
* FSM's are good for picking out patterns in a stream of symbols. eg the occurrence of 101 in a binary steam.
* Or the occurence of keywords, symbols and identifiers in a line of computer code.
*
* Regular expressions can be represented by an FSM
*
* The State Pattern has a "Context" class and one or more State classes (one State class per system state)
* The context class contains a reference to a State object, representing its curent state.
* The Context class has one or more methods - implementing an Interface.
* Each State class implements the same Interface (but different state dependent implementations)
* When a method is called on the context object - it delegates execution to the current state object.
* Each state object method should return the next state (or set it in the context).
* The context object effectively "changes class" on each state change.  
* State objects have 2 roles: 
* 
* Our example will implement a FSM for the regex "me|you"  ie will match the words "me" or "you" in an input stream.
* We will use the FSM ( implemented as per State Pattern) to count the number of "me" or "you" found in a line of text.
* 
* Note: Context and State are closely intertwined. At the very least each State object needs a reference
* to the Context object to set the new current state for the Context. Also the State may want to advise
* the Context when some condition happens (like found an instance of "me" or "you").
* The best approach would be for each State to create the next State as needed and return it to Context
* Context in turn could destroy the current state before installing the next stage. However, PHP cannot
* easily destroy objects (was designed for short lived request/response scripts) so the used state
* objects could accumulate. Two solutions are 1) to make the State object singletons ( at the expense of
* larger State objects) and 2) to keep a list of pre-constructed states in the Context.
* There are 2 vesrions of this program (this is version 2 using an array of states in Context)
*/
// -------------------------------------------------------------------------
// Define the Interface
interface IContext {  // Interface definition for the method(s)
    public function processNextChar(string $nextChar);       // process next char (1 char is a string in PHP)
}

// Define the Context object
class Context implements IContext { // Concrete class for the context
    private $currentState;
    private $meCount = 0;
    private $youCount = 0;
    private $arrayOfStates = array(); // contains all state objects

    // implementing the Interface
    public function processNextChar(string $nextChar){
        // delegate processing to the state object
        $nextStateName=$this->currentState->processNextChar($nextChar);
        $this->currentState = $this->arrayOfStates[$nextStateName];
    }
    public function addState($stateName,$stateObj){$this->arrayOfStates[$stateName]=$stateObj;}
    public function setCurrentState($stateName){$this->currentState=$this->arrayOfStates[$stateName];}
    public function foundMatch($matchedItem) {
        if ($matchedItem == "me") $this->meCount++;
        else if ($matchedItem == "you") $this->youCount++;
    }    
    public function printFound() {echo "Found ".$this->meCount." instances of \"me\" and "
                                               .$this->youCount." instances of \"you\".".PHP_EOL;}

}

// -------------------------------------------------------------------------
// The state objects need to be singletons.
// This is to avoid that every state object reuires access to all state objects.

// Begin state
class BeginState implements IContext { // commencing search
    private $context;

    public function __construct($context) {$this->context=$context;}
    public function processNextChar(string $char){
        if ($char == "m"){
            return("M_State");
        }
        else if ($char == "y"){
            return("Y_State");
        }
        else return("BeginState");
    }
}
// --------------------------------------
// M_State  (we have received an "m")
class M_State implements IContext {  
    private $context;

    public function __construct($context) {$this->context=$context;}
    public function processNextChar(string $char){
        if ($char == "e"){
            $this->context->foundMatch("me");
            echo "Found and instance of \"me\"".PHP_EOL;
        }
        return("BeginState");
    }
}
// --------------------------------------
// Y_State  (we have received a "y")
class Y_State implements IContext {
    private $context;

    public function __construct($context) {$this->context=$context;} 
    public function processNextChar(string $char){
        if ($char == "o"){
            return("YO_State");
        }
        else return("BeginState");
    }
}
// --------------------------------------
// YO_State  (we have received a "yo")
class YO_State implements IContext {
    private $context;

    public function __construct($context) {$this->context=$context;}
    public function processNextChar(string $char){
        if ($char == "u"){
            $this->context->foundMatch("you");
            echo "Found and instance of \"you\"".PHP_EOL;
        }
        return("BeginState");
    }
}
//---------------------------------------------------
// now instatiate the context object and state Object
$context = new Context();
$context->addState("BeginState" , new BeginState($context));
$context->addState("M_State"    , new M_State($context));
$context->addState("Y_State"    , new Y_State($context));
$context->addState("YO_State"   , new YO_State($context));
$context->setCurrentState("BeginState");

// make the test string
$textStr = "123 you for me and me for you me me me you you you 123";  // 
echo $textStr.PHP_EOL;

// convert to an array of chars (to easy loop below)
$textArray = str_split($textStr);

// Scan the text array
foreach($textArray as $char){
    $context->processNextChar($char);
}
$context->printFound();
//





