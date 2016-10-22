<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use \DateTimeZone;
/**
 * Timezone utility functions.
 * @package usni\library\utils
 */
class TimezoneUtil
{
    /**
     * Gets timezone select options.
     * @return array
     */
    public static function getTimezoneSelectOptions()
    {
        $timezoneList = \DateTimeZone::listIdentifiers();
        return array_combine($timezoneList, $timezoneList);
    }

    /**
     * Get country time zone.
     * @param string $countryCode Code for the country.
     * @return string or null in case no country is provided
     */
    public static function getCountryTimezone($countryCode = null)
    {
        assert(is_string($countryCode) || is_null($countryCode));
        if($countryCode == null)
        {
            return null;
        }
        $timeZone = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);
        assert(is_array($timeZone));
        return $timeZone[0];
    }
}
?>