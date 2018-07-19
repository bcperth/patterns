<?php
 
//Assuming the question can be paraphrased as:
 
//"I need a PHP object to process data in a standard format, 
//but I want to read the data from multiple sources and convert 
//the data to the format my object expects". 

//Here is one way that is easy to expand and based on Strategy Pattern - as per GoF.
//Here are the classes you need to set up.

   //Create a concrete processData class
   class processDataObj{
       private $readDataObj = null;    // a specialised object that will read your data from API or file
       private $formatDataObj = null;  // a specialised object to vert the source format to the desired format
       private $inputRawData;          // data read from any source in the source format
       private $convertedData; // data converted frm any format to the desired format               
       
       public function processData($InputName){   // knows nothing of the details how to read or convert data
          $this->inputRawData = $this->readDataObj->readData("filePath");
          $this->convertedData = $this->formatDataObj->convertData($this->inputRawData); 

          // put your source and format independent code here
          echo "This data was read from some source I don't care about:  ".$this->inputRawData.PHP_EOL;
          echo "This is data converted from a format I dont care about:  ".$this->inputRawData.PHP_EOL;
       }

       // Inject the desired objects via the constructor
       public function __construct($readDataObject, $formatDataObject){
 		  $this->readDataObj = $readDataObject;
		  $this->formatDataObj = $formatDataObject;
       }
   } // end class processData


   // Define an interface for readData objects
   interface readData {
	   public function readData($sourceName);
   }   

   // Define an interface for convertData objects
   interface convertData {
	    public function convertData($rawData);
   } 

   // Define concrete classes for reading each type of input (file shown)	
   class readFileDataObj implements readData{
        public function readData($fileName) {
            $rawData = "Read from file";
           // do whatever you need here to query the API
           return ($rawData);  // in the known format for the API
        }
   }
   // Define concrete class to convert from each source format
    class convertJSONDataObj implements convertData{ 
        public function convertData($filepath) {
            $convertedData = "Converted from Json";
           // do stuff to convert Json to your desired format
           return ($convertedData);  // in the known format for the file
        }
    }

// Create an object to read from a file and convert from Json to whatever.
$readerObj = new readFileDataObj;
$convertorObj = new convertJSONDataObj;
$myPHPObject = new processDataObj($readerObj,$convertorObj);
$myPHPObject->processData("filePath");
  