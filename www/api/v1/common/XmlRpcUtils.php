<?php

/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                           |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id:$
*/

/**
 * @package    Openads
 * @author     Andriy Petlyovanyy <apetlyovanyy@lohika.com>
 *
 * A file to description XmlRpcUtils class.
 *
 */

// Require the XMLRPC classes
require_once MAX_PATH . '/lib/pear/XML/RPC/Server.php';

// Require the Pear::Date class
require_once MAX_PATH . '/lib/pear/Date.php';

/**
 * Class to description XmlRpc methods.
 *
 */
class XmlRpcUtils
{
    /**
     * Generate Error message.
     *
     * @param string $errorMessage
     * @return XML_RPC_Response
     */
    function generateError($errorMessage)
    {
        // import user errcode value
        global $XML_RPC_erruser;

        $errorCode = $XML_RPC_erruser + 1;
        return new XML_RPC_Response(0, $errorCode, $errorMessage);
    }

    /**
     * Response string.
     *
     * @param string $string
     * @return XML_RPC_Response
     */
    function stringTypeResponse($string)
    {
        $value = new XML_RPC_Value($string, $GLOBALS['XML_RPC_String']);
        return new XML_RPC_Response($value);
    }

    /**
     * Response boolean.
     *
     * @param boolean $boolean
     * @return XML_RPC_Response
     */
    function booleanTypeResponse($boolean)
    {
        $value = new XML_RPC_Value($boolean, $GLOBALS['XML_RPC_Boolean']);
        return new XML_RPC_Response($value);
    }

    /**
     * Response integer.
     *
     * @param integer $integer
     * @return XML_RPC_Response
     */
    function integerTypeResponse($integer)
    {
        $value = new XML_RPC_Value($integer, $GLOBALS['XML_RPC_Int']);
        return new XML_RPC_Response($value);
    }

    /**
     * Convert RecordSet with into array of structures XML_RPC_Response.
     *
     * @param array $aFieldTypes  field name - field type
     * @param RecordSet &$rsAllData   Record Set with all data
     * @return XML_RPC_Response
     */
    function arrayOfStructuresResponse($aFieldTypes, &$rsAllData)
    {
        $rsAllData->find();
        $cRecords = 0;
   		while($rsAllData->fetch()) {
   		    $aRowData = $rsAllData->toArray();
            foreach ($aRowData as $fieldName => $fieldValue) {
                $aReturnData[$cRecords][$fieldName] = XmlRpcUtils::_setRPCType(
                                                        $aFieldTypes[$fieldName], $fieldValue);
            }

            $aReturnData[$cRecords] = new XML_RPC_Value($aReturnData[$cRecords],
                                                           $GLOBALS['XML_RPC_Struct']);
            $cRecords++;
		}

        $value = new XML_RPC_Value($aReturnData, $GLOBALS['XML_RPC_Array']);

        return new XML_RPC_Response($value);
    }

    /**
     * Set RPC type for variable.
     *
     * @param string $type
     * @param mixed $variable
     * @return XML_RPC_Value or false
     */
    function _setRPCType($type, $variable)
    {
        switch ($type) {
            case 'string':
                if (is_null($variable)) {
                    $variable = '';
                }
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_String']);

            case 'integer':
                if (is_null($variable)) {
                    $variable = 0;
                }
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_Int']);

            case 'float':
                if (is_null($variable)) {
                    $variable = 0.0;
                }
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_Double']);

            case 'date':
                $dateArr = explode('-', $variable);
                $variable = $dateArr[0] . $dateArr[1] . $dateArr[2] . 'T00:00:00';
                return new XML_RPC_Value($variable, $GLOBALS['XML_RPC_DateTime']);
        }
        die('Unsupported Xml Rpc type');
    }

    /**
     * Convert Date from iso 8601 format.
     *
     * @param string $date date string in ISO 8601 format
     * @param PEAR::Date &$oResult transformed date
     * @param XML_RPC_Response &$oResponseWithError response with error message
     * @return boolean shows if method was executed successfully
     */
    function _convertDateFromIso8601Format($date, &$oResult, &$oResponseWithError) 
    {
        $datetime = explode('T', $date);
        $year     = substr($datetime[0], 0, (strlen($datetime[0]) - 4));
        $month    = substr($datetime[0], -4, 2);
        $day      = substr($datetime[0], -2, 2);
        
        if (($year < 1970) || ($year > 2038)) {
            
            $oResponseWithError = XmlRpcUtils::generateError('Year should be in range 1970-2038');
            return false;
            
        } elseif (($month < 1) || ($month > 12)) {

            $oResponseWithError = XmlRpcUtils::generateError('Month should be in range 1-12');
            return false;
            
        } elseif (($day < 1) || ($day > 31)) {

            $oResponseWithError = XmlRpcUtils::generateError('Day should be in range 1-31');
            return false;
            
        } else {
            
            $oResult = new Date();
            $oResult->setYear($year);
            $oResult->setMonth($month);
            $oResult->setDay($day);
            
            return true;
        }
    }
    
    /**
     * Get scalar value from parameter
     *
     * @param mixed &$result
     * @param XML_RPC_Value &$oParam
     * @param XML_RPC_Response &$oResponseWithError
     * @return boolean shows if method was executed successfully
     */
    function _getScalarValue(&$result, &$oParam, &$oResponseWithError) 
    {
        if ($oParam->scalartyp() == $GLOBALS['XML_RPC_Int']) {
            $result = (int) $oParam->scalarval();
            return true;
        } elseif ($oParam->scalartyp() == $GLOBALS['XML_RPC_DateTime']) {
            
            return XmlRpcUtils::_convertDateFromIso8601Format($oParam->scalarval(), 
                $result, $oResponseWithError);
        } elseif ($oParam->scalartyp() == $GLOBALS['XML_RPC_Boolean']) {
            $result = (bool) $oParam->scalarval();
            return true;
        } else {
            $result = $oParam->scalarval();
            return true;
        }
    }
    
    /**
     * Get required scalar value
     *
     * @param mixed &$result
     * @param XML_RPC_Message  &$oParams
     * @param integer $idxParam
     * @param XML_RPC_Response &$oResponseWithError
     * @return boolean shows if method was executed successfully
     */
    function getRequiredScalarValue(&$result, &$oParams, $idxParam, &$oResponseWithError)
    { 
        $oParam = $oParams->getParam($idxParam);
        return XmlRpcUtils::_getScalarValue($result, $oParam, $oResponseWithError);
    }

    /**
     * Get not required scalar value
     *
     * @param mixed &$result value or null
     * @param XML_RPC_Message  &$oParams
     * @param integer $idxParam
     * @param XML_RPC_Response &$oResponseWithError
     * @return boolean shows if method was executed successfully
     */
    function _getNotRequiredScalarValue(&$result, &$oParams, $idxParam, &$oResponseWithError)
    {
        $cParams = $oParams->getNumParams();
        if ($cParams > $idxParam) {
            $oParam = $oParams->getParam($idxParam);
            
            return XmlRpcUtils::_getScalarValue($result, $oParam, $oResponseWithError);
        } else {
            
            $result = null;
            return true;
        }

    }
    
    /**
     * Get scalar values from parameters
     *
     * @param array $aReferencesOnVariables array of refrence on variables
     * @param array $aRequired array of boolean values to indicate which field is required
     * @param XML_RPC_Message  $oParams
     * @param XML_RPC_Response &$oResponseWithError
     * @param integer $idxStart Index of parameter from which values start
     * @return boolean shows if method was executed successfully
     */
    function getScalarValues($aReferencesOnVariables, $aRequired, &$oParams, &$oResponseWithError,
        $idxStart = 0)
    {
        if (count($aReferencesOnVariables) != count($aRequired)) {
            die('$aReferencesOnVariables & $aRequired arrays should have the same length');
        }
        
        $cVariables = count($aReferencesOnVariables);
        for ($i = 0; $i < $cVariables; $i++) {
            if ($aRequired[$i]) {
                if (!XmlRpcUtils::getRequiredScalarValue($aReferencesOnVariables[$i], 
                    $oParams, $i + $idxStart, $oResponseWithError)) {
                    return false;   
                }
            } else {
                if (!XmlRpcUtils::_getNotRequiredScalarValue($aReferencesOnVariables[$i], 
                    $oParams, $i + $idxStart, $oResponseWithError)) {
                    return false;   
                }
            }
        }
        return true;
    }

    /**
     * Gets Structure Scalar field from XML RPC Value parameter
     *
     * @param structure &$oStructure  to return data
     * @param XML_RPC_Value $oStructParam
     * @param string $fieldName
     * @param XML_RPC_Response &$responseWithError
     * @return boolean shows if method was executed successfully
     */
    function _getStructureScalarField(&$oStructure, &$oStructParam, $fieldName, 
        &$oResponseWithError)
    {
        $oParam = $oStructParam->structmem($fieldName);
        if (isset($oParam)) {

            if ($oParam->kindOf() == 'scalar') {

                return XmlRpcUtils::_getScalarValue($oStructure->$fieldName, $oParam, $oResponseWithError);

            } else {

                $oResponseWithError = XmlRpcUtils::generateError(
                    'Structure field \'' . $fieldName .'\' should be scalar type ');
                return false;
            }

        } else {

            return true;

        }
    }

    /**
     * Gets Structure Scalar fields
     *
     * @param structure &$oStructure  to return data
     * @param XML_RPC_Message &$oParams
     * @param integer $idxParam
     * @param array $aFieldNames
     * @param XML_RPC_Response &$oResponseWithError
     * @return boolean shows if method was executed successfully
     */
    function getStructureScalarFields(&$oStructure, &$oParams, $idxParam,
        $aFieldNames, &$oResponseWithError)
    {
        $oStructParam = $oParams->getParam($idxParam);
        
        foreach ($aFieldNames as $fieldName) {

            if (!XmlRpcUtils::_getStructureScalarField($oStructure, $oStructParam,
                $fieldName, $oResponseWithError)) {

                return false;
            }
        }
        return true;
    }
}



?>
