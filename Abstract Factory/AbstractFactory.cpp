// This application can be found at
// C:\Users\Brendan\source\repos\ConsoleApplication2, Compiled as a Visual Studio 2017.
// This is an example of Abstract Factory Pattern to reply to a Stackoverflow question. 
#include <stdafx.h>
#include <iostream>
using namespace std;

// make different types of fridges
class IFridge
{
public:
	virtual const char* Run(void) = 0;
};

class FridgeSamsung : public IFridge
{
public:
	const char* Run(void)
	{
		return "This house has a Samsung Fridge\n";
	}
};

class FridgeWhirlpool : public IFridge
{
public:
	const char* Run(void)
	{
		return "This house has a Whirlpool Fridge\n";
	}
};

// make different types of washing machine 
class IWashingMachine
{
public:
	virtual const char*  Run(void) = 0;
};

class WashingMachineSamsung : public IWashingMachine
{
public:
	const char* Run(void)
	{
		return "This house has a Samsung Washing Machine\n";
	}
};

class WashingMachineWhirlpool : public IWashingMachine
{
public:
	const char* Run(void)
	{
		return "This house has a Whirlpool Washing Machine\n";
	}
};

// make different type of factory
class IFactory
{
public:
	virtual IFridge* GetFridge(void) = 0;
	virtual IWashingMachine* GetWashingMachine(void) = 0;
};

class FactorySamsung : public IFactory
{
	IFridge* GetFridge(void)
	{
		return new FridgeSamsung();
	}

	IWashingMachine* GetWashingMachine(void)
	{
		return new WashingMachineSamsung();
	}
};

class FactoryWhirlpool : public IFactory
{
	IFridge* GetFridge(void)
	{
		return new FridgeWhirlpool();
	}

	IWashingMachine* GetWashingMachine(void)
	{
		return new WashingMachineWhirlpool();
	}
};

// Make a house object that has a fridge and a washing machine
class House
{
private:
	class  IWashingMachine * washingMachine;
	class  IFridge * fridge;

public:
	House(IFactory * houseFactory) {
		washingMachine = houseFactory->GetWashingMachine();
		fridge = houseFactory->GetFridge();
	}
	void showAppliances() {
		cout << washingMachine->Run();
		cout << fridge->Run();
	}
};

int main()
{
	class IFactory * factory;  
	class House * house;       

	// make a samsung house
	factory = new FactorySamsung;
	house = new House(factory);     // passing the factory by injection
	house->showAppliances();        // now we have a Samsung house
	cout << '\n';

	// clean up
	delete house;
	delete factory;

	// make a whirlpool house
	factory = new FactoryWhirlpool;
	house = new House(factory);    // passing the factory by injection
	house->showAppliances();       // now we have a WHilepool house
	cout << '\n';

	// clean up
	delete house;
	delete factory;
	
	return 1;
}