This folder contains tutorial material related to well know SW design patterns.
There is one sub directory per pattern.
Each subdirectory is named after its target pattern:
A minimal implementation of each pattern is constructed, in PHP or nodel.js as far as possible, together with some explanatory notes.

Strategy: When one or more methods of a class (Component) can have multiple variations (strategies) eg sorting algorithms, the Strategy pattern allows you to "inject" the required mix of method implementations (strategies) into the constructor. Each "strategy" has its own Interface, and implementations of each strategy are instantiated as separate objects.
The concrete strategy is injected into the component via its constructor and the component saves a reference to the concrete strategy.  

Observer: When multiple classes (Observers) needs to be informed of changes occurring in another class (Observable), the Observer pattern allows Observers to register with the Observable. The Observable subsequently informs each Observer of state changes via the Observer's inform() method. The Observer then gets the state update by calling the Observables's update() method.

Decorator: When method of a class can have multiple "layers" ie variations that are built upon each other, the Decorator pattern allows derived classes to add additional functionality to the base method. The end result is a linked list of a base object and any number of derived objects. This operates like a pipe, where the output of inner objects become the input of the outer objects until the outermost decorator is reached.

State: When a system can be modelled as a "state machine"  ie system has a fixed number states. The system behaves differently in each state AND has fixed rules to transition between states. (ref a state transition diagrams used to model such systems). The State Pattern implements the system as a "Context" object and a collection of "State" objects. State objects have 2 roles: 1 to implement the state specific behaviour: 2 to transition to the next state ( ie set the new current state in the context). The net effect is that the system is made to behave differently according to its current state. ( used in protocols, parsers etc).

Iterator: This provides a standard set of methods (an Iteractor object ) to iterate through any collection of objects (Iterable) such as sets, arrays, lists, trees, networks etc. The Iterator object implements the Iterator Interface - which defines at least 3 basic methods: hasNext(), next() and current().  The Iterable object has a factory method to create concrete Iterator objects on request from clients. The clients use the Iterator to iterate through the collection (and presumable process each item in some way). The "foreach" clause - found in many OOP languages - relies on the iterator pattern ie is implemented using the 3 iteratr methods.

Singleton: This is a mini-pattern that provides a way to prevent more than one object of a class to be instantiated. It does this by making the constructor private (prevent "new" from being used) and by providing a static method eg called Instance(), to create an instance. There is also a static variable eg called $inst (php) which is either NULL (no object create yet) or = the referece to the created object. Instance() checks $inst and create a new object if NULL and set $inst to that object. It then returns $inst as the new object (which may not be new!).



 

 