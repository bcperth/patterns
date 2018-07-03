<?php

/**
 * The 'observer' pattern implements a 'push' solution for services delivered from a central server.
 * Typically the clients register with the server to receive notifications when new data is available
 * from the server. This contrasts with polling or 'pull' solutions which require the clients to poll
 * server to ask for updates. 
 * 
 * The 'observer' pattern is appropriate when there are many concurrent clients and when server data changes
 * frequently at random times. eg a chat room, or multiple displays (tabular, bar chart, line graph) of the 
 * same real time data.
 * 
 * The key players are the Observable (subject) and the Observer(s).
 * 0-many Observers may register with the Observable to receive notifications.
 * The Observable keeps a list of registered Observers.
 * The Observable notifies all registered Observers whenever its data (state) changes.
 * Each Observer then requests the Observable for data.
 * 
 * In the example below a time source is created (Observable) which notifies its Observers(0-many clocks)
 * when its time changes ( faked in this case to notify every second).
 * There are 3 clock types (12hr, 24hrs and epoc seconds)
 */
// -------------------------------------------------------------------------
/* Define the observable class (using IObserver interface) */
interface IObservable
{
    /* Interface definition for Subject */ 
    public function addObserver(timeObserver $observer);       
    public function removeObserver(timeObserver $observer);    
    public function getTime();                                
}

class TimeServer Implements IObservable
{
    // Observable (Subject) class

    private $observerList = []; 
    private $timeVal = 0;    // this is the changing state that needs to be notified

    public function addObserver(timeObserver $observer)       // register an Observer
    {
        $this->observerList[]=$observer;  // add the Observer
        echo "Observer Registered. ".count($this->observerList)." users now registered".PHP_EOL;
    }

    public function removeObserver(timeObserver $observer)    // de-register an Observer
    {
        $found = false;
        $index = 0;
        // Find the index of the Observer
        if (count($this->observerList) < 1) { return;
        }

        for ($x = 0; $x < count($this->observerList); $x++) {
            if ($this->observerList[$x] === $observer) {
                $found = true;
                $index = $x; 
                break;
            }
        }

        if ($found == true) {
            unset($this->observerList[$index]);
            // now realign the array to avoid references to bad indices
            $this->observerList = array_values($this->observerList);
            echo "Observer Removed. ".count($this->observerList)." users now registered".PHP_EOL;
        }
        else {
            "Observer not found. ".count($this->observerList)." users Still registered".PHP_EOL;
        } 
    }

    public function getTime()            // allow Observers to get the time after being notified
    {
        return($this->timeVal);
    }

    public function increment()
    {
        $this->timeVal+=1;
        $this->notifyAll();  // server (Observable) notifies registered observers
    }

    private function notifyAll()           // Notify all observers of state change
    {
        //echo "There are ".count($observerList)." observers".PHP_EOL;
        foreach ($this->observerList as $observer) {
            //echo "Server is notifying".PHP_EOL;
            $observer->update();
        }
    }
} // end class TimeServer

// -------------------------------------------------------------------------
// Define the observer class (using IObserver interface)

interface IObserver
{
    // Interface definition for Observer
    public function update();       // request update data from Observer
}

class timeObserver implements IObserver
{

    // remember the service we are registered to
    public $name;
    public $timeServer;
    public $timeVal;

    // the constructor registers the TimeObserver with the time
    public function __construct(TimeServer $timeServer,string $name)
    {
        $this->timeServer = $timeServer;
        $this->name = $name;
        echo "Creating Observer ".$this->name.PHP_EOL;
    }

    public function update()
    {
        $this->timeVal = $this->timeServer->getTime();
        echo "Time of ".$this->name.": ".$this->timeVal." secs.".PHP_EOL;
    }

} // end class TimeObserverr

// Create the Observable and Observer objects
$timeServer1 = new TimeServer;
$timeObserver1 = new TimeObserver($timeServer1, "First Observer");
$timeObserver2 = new TimeObserver($timeServer1, "Second Observer");
$timeObserver3 = new TimeObserver($timeServer1, "Third Observer");

// register the Observers
$timeServer1->addObserver($timeObserver1);
$timeServer1->addObserver($timeObserver2);
$timeServer1->addObserver($timeObserver3);

echo "Now server will notify all registered Observers of a time update every 2 seconds".PHP_EOL;
// now just loop - with timeServer updating its state and notitying
for ($x = 0; $x < 4; $x++) {
    sleep(1);
    $timeServer1->increment();  // server (Observable) updates its state
} 

echo "Now we deregister Second Timer".PHP_EOL;
$timeServer1->removeObserver($timeObserver2);

echo "Now server will notify all registered Observers of a time update every 2 seconds".PHP_EOL;
echo "Second Observer should be missing".PHP_EOL;
// now just loop for a while
for ($x = 0; $x < 4; $x++) {
    sleep(1);
    $timeServer1->increment();
} 

echo "Now we re-register Second Timer".PHP_EOL;
$timeServer1->addObserver($timeObserver2);
echo "Now we deregister First Timer".PHP_EOL;
$timeServer1->removeObserver($timeObserver1);
echo "Now we deregister Third Timer".PHP_EOL;
$timeServer1->removeObserver($timeObserver3);

echo "Now server will notify all registered Observers of a time update every 2 seconds".PHP_EOL;
echo "Second Observer should be present only".PHP_EOL;
// now just loop for a while
for ($x = 0; $x < 4; $x++) {
    sleep(1);
    $timeServer1->increment();
} 



