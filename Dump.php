<?php
/**
*   Function and class to dump
*   XML_RPC_Value objects in a nice way
*
*   Should be helpful as a normal var_dump(..) displays
*   all internals which doesn't really give you an overview
*   due to too much information
*
*   @author Christian Weiske <cweiske@php.net>
*/

require_once('XML/RPC.php');


/**
*   Generates the dump of the XML_RPC_Value
*   and echoes it
*
*   @param  XML_RPC_Value   The object to dump
*/
function XML_RPC_Dump($value)
{
    $dumper = new XML_RPC_Dump();
    echo $dumper->generateDump($value);
}



/**
*   Class which generates a dump of a
*   XML_RPC_Value object
*/
class XML_RPC_Dump
{
    /**
    *   The indentation array cache
    *   @var array
    */
    var $arIndent      = array();
    var $strBaseIndent = '    ';
    
    
    
    /**
    *   Returns the dump
    *   Does not print it out
    *
    *   @param  XML_RPC_Value   The object which dump shall be generated
    *   @return string          The dump
    */
    function generateDump($value, $nLevel = 0)
    {
        if (!is_object($value) && get_class($value) != 'xml_rpc_value') {
            require_once('PEAR.php');
            PEAR::raiseError('Tried to dump non-XML_RPC_Value variable' . "\r\n", 0, PEAR_ERROR_PRINT);
            if (is_object($value)) {
                $strType    = get_class($value);
            } else {
                $strType    = gettype($value);
            }
            return $this->getIndent($nLevel) . 'NOT A XML_RPC_Value: ' . $strType . "\r\n";
        }
        switch ($value->kindOf())
        {
            case 'struct':
                $ret = $this->genStruct($value, $nLevel);
                break;
            case 'array':
                $ret = $this->genArray($value, $nLevel);
                break;
            case 'scalar':
                $ret = $this->genScalar($value->scalarval(), $nLevel);
                break;
            default:
                require_once('PEAR.php');
                PEAR::raiseError('Illegal type "' . $value->kindOf() . '" in XML_RPC_Value' . "\r\n", 0, PEAR_ERROR_PRINT);
                break;
        }
        return $ret;
    }//function generateDump($value, $nLevel = 0)
    
    
    
    /**
    *   returns the scalar value dump
    *   @param  XML_RPC_Value   Scalar value
    *   @param  int             Level of indentation
    *   @return string  Dumped version of the scalar value
    */
    function genScalar($value, $nLevel)
    {
        if (gettype($value) == 'object') {
            $strClass = ' ' . get_class($value);
        } else {
            $strClass = '';
        }
        return $this->getIndent($nLevel) . gettype($value) . $strClass . ' ' . $value . "\r\n";
    }//function genScalar($value, $nLevel)
    
    
    
    /**
    *   returns the dump of a struct
    *   @param  XML_RPC_Value   Struct value
    *   @param  int             Level of indentation
    *   @return string  Dumped version of the scalar value
    */
    function genStruct($value, $nLevel)
    {
        $value->structreset();
        $strOutput = $this->getIndent($nLevel) . 'struct' . "\r\n";
        while (list($key, $keyval) = $value->structeach())
        {
            $strOutput .= $this->getIndent($nLevel + 1) . $key . "\r\n";
            $strOutput .= $this->generateDump($keyval, $nLevel + 2);
        }
        return $strOutput;
    }//function genStruct($value, $nLevel)
    
    
    
    /**
    *   returns the dump of an array 
    *   @param  XML_RPC_Value   Array value
    *   @param  int             Level of indentation
    *   @return string  Dumped version of the scalar value
    */
    function genArray($value, $nLevel)
    {
        $nSize     = $value->arraysize();
        $strOutput = $this->getIndent($nLevel) . 'array' . "\r\n";
        for($nA = 0; $nA < $nSize; $nA++) {
            $strOutput .= $this->getIndent($nLevel + 1) . $nA . "\r\n";
            $strOutput .= $this->generateDump($value->arraymem($nA), $nLevel + 2);
        }
        return $strOutput;
    }//function genArray($value, $nLevel)
    
    
    
    /**
    *   returns the indent for a specific level
    *   caches it for faster use
    *   @param  int     Level
    *   @return string  Indented string
    */
    function getIndent($nLevel)
    {
        if (!isset($this->arIndent[$nLevel])) {
            $this->arIndent[$nLevel] = str_repeat($this->strBaseIndent, $nLevel);
        }
        return $this->arIndent[$nLevel];
    }//function getIndent($nLevel)
}//class XML_RPC_Dump
?>