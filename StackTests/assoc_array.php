<?php
/*
$categories = [
    'Budget:Necessities:Phone' => 200.00,
    'Budget:Necessities' => 0,
    'Budget:Necessities:Home' => 0,
    'Budget:Necessities:Home:Rent' => 450.00,
    'Budget:Savings' => 500,
    'Budget:Savings:Vacation' => 300,
];


foreach ($categories as $key => $value){
        $indices = explode(":",$key);
        //echo "next key".PHP_EOL;
        //print_r($arr);
        $n=0;
        $arrayStr="";
        foreach($indices as $index){
            if ($n>0){
                $arrayStr.="[\"".$index."\"]";
            }
            $n++;
        };
        $outStr="\$out".$arrayStr."=10;";
        echo $outStr.PHP_EOL;
        eval($outStr);
        print_r($out);
        //break; 
}
$x="abc";
$str = "\$temp[\"x\"] = 6;";
echo $str.PHP_EOL;
eval ($str);
//echo $temp;
print_r($temp);

$arrx["abc"] = 10;
$arrx["xyz"]["def"] = 20;

print_r($arrx);
*/

    $AdditionalChargeINS = array(
        'Quantity' => '1',
        'ChargeTemplateID' => "ChargeTemplateID_INS",
        'Amount' => array(
            'AmountBeforeTax' => "fee_insurance",
            'Taxes' => array(
                'Amount' => '0.00'
            )
        )
    );
    
    $AdditionalChargeDAM = array(
        'Quantity' => '1',
        'ChargeTemplateID' => "ChargeTemplateID_DAM",
        'Amount' => array(
            'AmountBeforeTax' => "fee_damage",
            'Taxes' => array(
                'Amount' => '0.00'
            )
        )
    );
    $arr1["additionalCharge"] = (object)$AdditionalChargeDAM;
    $arr2["additionalCharge"] = (object)$AdditionalChargeDAM;
    $arr  = (object)array((object)$arr1,(object)$arr2);
    
    print_r($arr);