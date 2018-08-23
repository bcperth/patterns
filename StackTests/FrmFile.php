    <?php
    $fileName = "pb00k.frm";
    
    // read file into an array of char
    //---------------------------------
    $handle = fopen($fileName, "rb");
    $contents = fread($handle, filesize($fileName));
    fclose($handle);
    $fileSize=strlen($contents);  // save the filesize fot later printing

    // locate the column data near the end of the file
    //-------------------------------------------------
    $index = 6;    // location of io_size
    $io_size_lo = ord($contents[$index]);  
    $io_size_hi = ord($contents[$index+1]);
    $io_size = $io_size_hi *0x100 + $io_size_lo; // read IO_SIZE

    $index = 10;  // location of record length
    $rec_len_lo = ord($contents[$index]);
    $rec_len_hi = ord($contents[$index+1]);
    $rec_len = $rec_len_hi * 0x100 + $rec_len_lo; // read rec_length

    // this formula uses io_size and rec_length to get to column data
    $colIndex = ( (  (($io_size + $rec_len)/$io_size)   + 1) * $io_size ) + 258;
    $colIndex -= 0x3000;   // this is not documented but seems to work!

    // find number of columns in the table
    //------------------------------------------------- 
    echo PHP_EOL."Col data at 0x".dechex($colIndex).PHP_EOL;
    $numCols = ord($contents[$colIndex]);
    
    //Extract the column names
    //--------------------------------------
    $colNameIndex = $colIndex+0x50;   //0X50 by inspection
    echo "Col names at 0x".dechex($colNameIndex).PHP_EOL;
    $cols=array();
    for ($col = 0; $col < $numCols; $col++){
        $nameLen = ord($contents[$colNameIndex++]);          // name length is at ist posn
        $cols[]['ColumnName']= substr($contents,$colNameIndex,$nameLen-1); // read the name
        $colNameIndex+=$nameLen+2;        // skip ahead to next name (2 byte gap after \0)
    }
    print_r($cols);

    // locate the start of the type and size information
    $colDataIndex = $colNameIndex -2;   // undo the last 2 byte gap

    //Extract column types.
    $colTypesIndex =  $colDataIndex +13; //$colIndex+0xb6+13;   // 0xb1b8+13;
    echo "Col types at 0x".dechex($colTypesIndex).PHP_EOL;
    for ($n = 0; $n < $numCols; $n++){
        switch (ord($contents[$colTypesIndex])){  
            case 0X00:
                $type = "DECIMAL";
                break; 
            case 0X01:
                $type = "TINYINT";
                break;        
            case 0X02:
                $type = "SMALLINT";
                break;
            case 0X03:
                $type = "MEDIUMINT";
                break;
            case 0X04:
                $type = "FLOAT";
                break;
            case 0X05:
                $type = "DOUBLE";
                break;
            case 0X08:
                $type = "INT";
                break;
            case 0X09:
                $type = "INT24";
                break;
            case 0X0A:
                $type = "DATE";
                break;
            case 0X0B:
                $type = "TIME";
                break;  
            case 0X0C:
                $type = "DATETIME";
                break;
            case 0X0D:
                $type = "YEAR";
                break; 
            case 0X0E:
                $type = "NEWDATE";
                break; 
            case 0X0F:
                $type = "VARCHAR";
                break;
            case 0X10:
                $type = "BIT";
                break; 
            case 0X12:
                $type = "DATETIME";
                break;                
            case 0Xf6:
                $type = "DECIMAL";
                break;
            case 0Xf7:
                $type = "ENUM";
                break;           
            case 0Xf8:
                $type = "SET";
                break;
            case 0Xf9:
                $type = "TINY_BLOB";
                break;            
            case 0Xfa:
                $type = "MEDIUM_BLOB";
                break;            
            case 0Xfb:
                $type = "LONG_BLOB";
                break;            
            case 0Xfc:
                $type = "BLOB";
                break;            
            case 0Xfd:
                $type = "VAR_STRING";
                break;
            case 0Xfe:
                $type = "STRING";
                break;
            case 0Xff:
                $type = "GEOMETRY";
                break;
            default:
                $type = "UNKNOWN";
                break;
        }
        $cols[$n]['Type']= $type; 
        $colTypesIndex+=17;      // skip ahead to next type
    }
    //print_r($cols);

    //Extract column sizes
    $colSizesIndex = $colDataIndex + 3; //0xb1b8+4;
    echo "Col sizes at 0x".dechex($colSizesIndex).PHP_EOL;
    for ($n = 0; $n < $numCols; $n++){
        $hiByte = ord($contents[$colSizesIndex+1]);
        $loByte = ord($contents[$colSizesIndex]);
        $colSize = $hiByte * 0X100 + $loByte;
        if ($cols[$n]['Type'] == 'VARCHAR') $colSize = $colSize/3;
        $cols[$n]['Size'] = $colSize;
        $colSizesIndex+=17;      // skip ahead to next size
    }
    //print_r($cols);

    // print out the create table command
    echo PHP_EOL."# Reading .FRM file for ".$fileName." (size = ".$fileSize." bytes.)".PHP_EOL;
    echo "# The .frm file is a TABLE".PHP_EOL;
    echo "# CREATE TABLE Statement:".PHP_EOL;
    echo PHP_EOL;
    echo "CREATE TABLE '".$fileName."'  (".PHP_EOL;
    foreach ($cols as $col){
        echo "    '".$col['ColumnName']."' ".$col['Type']."(".$col['Size']."),".PHP_EOL;
    }
    echo ") ENGINE=InnoDB;".PHP_EOL;
    echo PHP_EOL;

    // print the hex table of col data ( located after col names in .frm file)
    echo "Dumping from ".dechex($colDataIndex)." .....".PHP_EOL;
    $hex = '';
    for ($n=0; $n<$numCols; $n++){
        for ($i=0; $i<17; $i++){
            if ($colDataIndex>$fileSize){
                echo PHP_EOL."Index too high".PHP_EOL;
                exit(1);
            }
            $ord = ord($contents[$colDataIndex++]);
            $hexCode = dechex($ord);
            if (strlen($hexCode)==1){
                $hexCode = '0'.$hexCode;
            }
            echo $hexCode.' ';
        }
        echo PHP_EOL;
    }
