<?php
namespace MPerk\HtmlCleaner;

/*
 * This file is part of the MPerk.HtmlCleaner package.
 *
 * (c) Michael Perk
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Interface for AttributeCleaner classes
 * @package MPerk\HtmlCleaner
 */
interface AttributeCleaner
{
    /**
     * This method filters unnecessary information out of an attribute.
     * @param $elementName String Name of the element.
     * @param $attributeName String Name of the attribute.
     * @param $attributeValue String Value of the attribute.
     * @param $advancedOptions mixed Advanced options.
     * @return mixed Cleaned attribute value.
     */
    public static function cleanAttribute($elementName, $attributeName, $attributeValue, $advancedOptions);
}

/**
 * Class DefaultAttributeCleaner
 * @package MPerk\HtmlCleaner
 */
class DefaultAttributeCleaner implements AttributeCleaner
{
    /**
     * @param $elementName String Name of the element.
     * @param $attributeName String Name of the attribute.
     * @param $attributeValue String Value of the attribute.
     * @param $advancedOptions mixed Advanced options.
     * @return mixed Cleaned attribute value.
     */
    public static function cleanAttribute($elementName, $attributeName, $attributeValue, $value)
    {
        if ($attributeValue == $value) {
            return $attributeValue;
        }

        return null;
    }
}

/**
 * Class ClassAttributeCleaner
 * @package MPerk\HtmlCleaner
 */
class ClassAttributeCleaner implements AttributeCleaner
{
    /**
     * @param $elementName String Name of the element.
     * @param $attributeName String Name of the attribute.
     * @param $attributeValue String Value of the attribute.
     * @param $advancedOptions mixed Advanced options.
     * @return mixed Cleaned attribute value.
     */
    public static function cleanAttribute($elementName, $attributeName, $attributeValue, $allowedClasses)
    {
        $newClassAttributeValue = "";
        $currentClasses = explode(' ', $attributeValue);

        foreach($allowedClasses as $currentAllowedClass){
            if(in_array($currentAllowedClass, $currentClasses)){
                $newClassAttributeValue .= $currentAllowedClass." ";
            }
        }

        if(empty($newClassAttributeValue)) {
            return null;
        }

        $newClassAttributeValue = trim($newClassAttributeValue);

        return $newClassAttributeValue;
    }
}

/**
 * Class StyleAttributeCleaner
 * @package MPerk\HtmlCleaner
 */
class StyleAttributeCleaner implements AttributeCleaner
{
    /**
     * @param $elementName String Name of the element.
     * @param $attributeName String Name of the attribute.
     * @param $attributeValue String Value of the attribute.
     * @param $advancedOptions mixed Advanced options.
     * @return mixed Cleaned attribute value.
     */
    public static function cleanAttribute($elementName, $attributeName, $attributeValue, $styleOptions)
    {
        $newStyleAttributeValue = "";
        $currentStyleAttributes = array();
        $tempStyleAttributes = explode(';', $attributeValue);

        foreach ($tempStyleAttributes as &$currentTempStyleAttribute) {
            $currentTempStyleAttribute = explode(':', $currentTempStyleAttribute);
            $currentTempStyleAttribute = array_map('trim', $currentTempStyleAttribute);

            if (!empty($currentTempStyleAttribute[0]) && !empty($currentTempStyleAttribute[1])) {
                $currentStyleAttributes[$currentTempStyleAttribute[0]] = $currentTempStyleAttribute[1];
            }
        }

        $newStyleAttributes = array();

        foreach ($styleOptions as $currentValidStyleAttribute => $currentValidStyleAttributeValue) {

            if (array_key_exists($currentValidStyleAttribute, $currentStyleAttributes)) {

                if ($currentValidStyleAttributeValue == null) {
                    $newStyleAttributes[$currentValidStyleAttribute] = $currentStyleAttributes[$currentValidStyleAttribute];
                    continue;
                }

                if ($currentStyleAttributes[$currentValidStyleAttribute] == $currentValidStyleAttributeValue) {
                    $newStyleAttributes[$currentValidStyleAttribute] = $currentValidStyleAttributeValue;
                    continue;
                }
            }
        }

        if (empty($newStyleAttributes)) {
            return null;
        }

        foreach ($newStyleAttributes as $currentStyleAttribute => $currentStyleAttributeValue) {
            $newStyleAttributeValue .= $currentStyleAttribute . ":" . $currentStyleAttributeValue . ";";
        }

        return $newStyleAttributeValue;
    }
}