
// This application can be found at
// C:\Users\Brendan\source\repos\ConsoleApplication1, Compiled as a Visual Studio 2017.
// This is an example of Factory Method Pattern to reply to a Stackoverflow question.
    #include "stdafx.h"
    #include <iostream>
    using namespace std;
    
    class IRose
    {
    public:
    	virtual const char * Color(void) = 0;
    };

    class RedRose : public IRose
    {
    public:
    	const char * Color(void)
    	{
    		return "I am a Red rose";
    	}
    };

    class YellowRose : public IRose
    {
    public:
    	const char * Color(void)
    	{
    		return "I am a Yellow rose";
    	}
    };

    class RoseGarden
    {
    protected: class IRose* rose;   // a pointer to the garden's rose

    public:
    	virtual void createRose() { }  // abstract Factory Method

    public: void sayColor() {
    		cout << rose->Color() << '\n';
    	}
    };

    class RedRoseGarden : public RoseGarden
    {
    public:
    	void createRose()
    	{
    		this->rose = new RedRose();   // concrete factory method
    	}
    };

    class YellowRoseGarden : public RoseGarden
    {
    public:
    	void createRose()
    	{
    		this->rose = new YellowRose();  // concrete factory method
    	}
    };

    int main()
    {
    	RoseGarden * garden = NULL;
    
    	garden = new YellowRoseGarden;
    	garden->createRose();      // correct factory method is chosen via inheritance
    	garden->sayColor();
    	delete garden;

    	garden = new RedRoseGarden;
    	garden->createRose();      // correct factory method is chosen via inheritance
    	garden->sayColor();
    	delete garden;

    	return 1;
    }