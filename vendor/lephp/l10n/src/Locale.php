<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/30/16
 * Time: 12:37 AM
 * @copyright LeEco
 * @since 1.0.0
 */
namespace L10N;

class Locale
{
    /**
     * Stores the translated strings for the full weekday names.
     *
     * @since 2.1.0
     * @var array
     * @access private
     */
    public $weekday;

    /**
     * Stores the translated strings for the one character weekday names.
     *
     * There is a hack to make sure that Tuesday and Thursday, as well
     * as Sunday and Saturday, don't conflict. See init() method for more.
     *
     * @see WP_Locale::init() for how to handle the hack.
     *
     * @since 2.1.0
     * @var array
     * @access private
     */
    public $weekday_initial;

    /**
     * Stores the translated strings for the abbreviated weekday names.
     *
     * @since 2.1.0
     * @var array
     * @access private
     */
    public $weekday_abbrev;

    /**
     * Stores the translated strings for the full month names.
     *
     * @since 2.1.0
     * @var array
     * @access private
     */
    public $month;

    /**
     * Stores the translated strings for the abbreviated month names.
     *
     * @since 2.1.0
     * @var array
     * @access private
     */
    public $month_abbrev;

    /**
     * Stores the translated strings for 'am' and 'pm'.
     *
     * Also the capitalized versions.
     *
     * @since 2.1.0
     * @var array
     * @access private
     */
    public $meridiem;

    /**
     * The text direction of the locale language.
     *
     * Default is left to right 'ltr'.
     *
     * @since 2.1.0
     * @var string
     * @access private
     */
    public $text_direction = 'ltr';

    /**
     * @var array
     */
    public $number_format;

    /**
     * Sets up the translated strings and object properties.
     *
     * The method creates the translatable strings for various
     * calendar elements. Which allows for specifying locale
     * specific calendar names and text direction.
     *
     * @since 2.1.0
     * @access private
     */
    public function init()
    {
        // The Weekdays
        $this->weekday[0] = /* translators: weekday */
            L10N::__('Sunday');
        $this->weekday[1] = /* translators: weekday */
            L10N::__('Monday');
        $this->weekday[2] = /* translators: weekday */
            L10N::__('Tuesday');
        $this->weekday[3] = /* translators: weekday */
            L10N::__('Wednesday');
        $this->weekday[4] = /* translators: weekday */
            L10N::__('Thursday');
        $this->weekday[5] = /* translators: weekday */
            L10N::__('Friday');
        $this->weekday[6] = /* translators: weekday */
            L10N::__('Saturday');

        // The first letter of each day. The _%day%_initial suffix is a hack to make
        // sure the day initials are unique.
        $this->weekday_initial[L10N::__('Sunday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('S_Sunday_initial');
        $this->weekday_initial[L10N::__('Monday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('M_Monday_initial');
        $this->weekday_initial[L10N::__('Tuesday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('T_Tuesday_initial');
        $this->weekday_initial[L10N::__('Wednesday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('W_Wednesday_initial');
        $this->weekday_initial[L10N::__('Thursday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('T_Thursday_initial');
        $this->weekday_initial[L10N::__('Friday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('F_Friday_initial');
        $this->weekday_initial[L10N::__('Saturday')] = /* translators: one-letter abbreviation of the weekday */
            L10N::__('S_Saturday_initial');

        foreach ($this->weekday_initial as $weekday_ => $weekday_initial_) {
            $this->weekday_initial[$weekday_] = preg_replace('/_.+_initial$/', '', $weekday_initial_);
        }

        // Abbreviations for each day.
        $this->weekday_abbrev[L10N::__('Sunday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Sun');
        $this->weekday_abbrev[L10N::__('Monday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Mon');
        $this->weekday_abbrev[L10N::__('Tuesday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Tue');
        $this->weekday_abbrev[L10N::__('Wednesday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Wed');
        $this->weekday_abbrev[L10N::__('Thursday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Thu');
        $this->weekday_abbrev[L10N::__('Friday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Fri');
        $this->weekday_abbrev[L10N::__('Saturday')] = /* translators: three-letter abbreviation of the weekday */
            L10N::__('Sat');

        // The Months
        $this->month['01'] = /* translators: month name */
            L10N::__('January');
        $this->month['02'] = /* translators: month name */
            L10N::__('February');
        $this->month['03'] = /* translators: month name */
            L10N::__('March');
        $this->month['04'] = /* translators: month name */
            L10N::__('April');
        $this->month['05'] = /* translators: month name */
            L10N::__('May');
        $this->month['06'] = /* translators: month name */
            L10N::__('June');
        $this->month['07'] = /* translators: month name */
            L10N::__('July');
        $this->month['08'] = /* translators: month name */
            L10N::__('August');
        $this->month['09'] = /* translators: month name */
            L10N::__('September');
        $this->month['10'] = /* translators: month name */
            L10N::__('October');
        $this->month['11'] = /* translators: month name */
            L10N::__('November');
        $this->month['12'] = /* translators: month name */
            L10N::__('December');

        // Abbreviations for each month. Uses the same hack as above to get around the
        // 'May' duplication.
        $this->month_abbrev[L10N::__('January')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Jan_January_abbreviation');
        $this->month_abbrev[L10N::__('February')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Feb_February_abbreviation');
        $this->month_abbrev[L10N::__('March')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Mar_March_abbreviation');
        $this->month_abbrev[L10N::__('April')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Apr_April_abbreviation');
        $this->month_abbrev[L10N::__('May')] = /* translators: three-letter abbreviation of the month */
            L10N::__('May_May_abbreviation');
        $this->month_abbrev[L10N::__('June')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Jun_June_abbreviation');
        $this->month_abbrev[L10N::__('July')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Jul_July_abbreviation');
        $this->month_abbrev[L10N::__('August')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Aug_August_abbreviation');
        $this->month_abbrev[L10N::__('September')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Sep_September_abbreviation');
        $this->month_abbrev[L10N::__('October')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Oct_October_abbreviation');
        $this->month_abbrev[L10N::__('November')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Nov_November_abbreviation');
        $this->month_abbrev[L10N::__('December')] = /* translators: three-letter abbreviation of the month */
            L10N::__('Dec_December_abbreviation');

        foreach ($this->month_abbrev as $month_ => $month_abbrev_) {
            $this->month_abbrev[$month_] = preg_replace('/_.+_abbreviation$/', '', $month_abbrev_);
        }

        // The Meridiems
        $this->meridiem['am'] = L10N::__('am');
        $this->meridiem['pm'] = L10N::__('pm');
        $this->meridiem['AM'] = L10N::__('AM');
        $this->meridiem['PM'] = L10N::__('PM');

        // Numbers formatting
        // See http://php.net/number_format

        /* translators: $thousands_sep argument for http://php.net/number_format, default is , */
        $trans = L10N::__('number_format_thousands_sep');
        $this->number_format['thousands_sep'] = ('number_format_thousands_sep' == $trans) ? ',' : $trans;

        /* translators: $dec_point argument for http://php.net/number_format, default is . */
        $trans = L10N::__('number_format_decimal_point');
        $this->number_format['decimal_point'] = ('number_format_decimal_point' == $trans) ? '.' : $trans;

        // Set text direction.
        if (isset($GLOBALS['text_direction']))
            $this->text_direction = $GLOBALS['text_direction'];
        /* translators: 'rtl' or 'ltr'. This sets the text direction for WordPress. */
        elseif ('rtl' == L10N::_x('ltr', 'text direction'))
            $this->text_direction = 'rtl';

        if ('rtl' === $this->text_direction && strpos($GLOBALS['wp_version'], '-src')) {
            $this->text_direction = 'ltr';
        }
    }

    public function rtl_src_admin_notice()
    {
        echo '<div class="error"><p>' . 'The <code>build</code> directory of the develop repository must be used for RTL.' . '</p></div>';
    }

    /**
     * Retrieve the full translated weekday word.
     *
     * Week starts on translated Sunday and can be fetched
     * by using 0 (zero). So the week starts with 0 (zero)
     * and ends on Saturday with is fetched by using 6 (six).
     *
     * @since 2.1.0
     * @access public
     *
     * @param int $weekday_number 0 for Sunday through 6 Saturday
     * @return string Full translated weekday
     */
    public function get_weekday($weekday_number)
    {
        return $this->weekday[$weekday_number];
    }

    /**
     * Retrieve the translated weekday initial.
     *
     * The weekday initial is retrieved by the translated
     * full weekday word. When translating the weekday initial
     * pay attention to make sure that the starting letter does
     * not conflict.
     *
     * @since 2.1.0
     * @access public
     *
     * @param string $weekday_name
     * @return string
     */
    public function get_weekday_initial($weekday_name)
    {
        return $this->weekday_initial[$weekday_name];
    }

    /**
     * Retrieve the translated weekday abbreviation.
     *
     * The weekday abbreviation is retrieved by the translated
     * full weekday word.
     *
     * @since 2.1.0
     * @access public
     *
     * @param string $weekday_name Full translated weekday word
     * @return string Translated weekday abbreviation
     */
    public function get_weekday_abbrev($weekday_name)
    {
        return $this->weekday_abbrev[$weekday_name];
    }

    /**
     * Retrieve the full translated month by month number.
     *
     * The $month_number parameter has to be a string
     * because it must have the '0' in front of any number
     * that is less than 10. Starts from '01' and ends at
     * '12'.
     *
     * You can use an integer instead and it will add the
     * '0' before the numbers less than 10 for you.
     *
     * @since 2.1.0
     * @access public
     *
     * @param string|int $month_number '01' through '12'
     * @return string Translated full month name
     */
    public function get_month($month_number)
    {
        return $this->month[zeroise($month_number, 2)];
    }

    /**
     * Retrieve translated version of month abbreviation string.
     *
     * The $month_name parameter is expected to be the translated or
     * translatable version of the month.
     *
     * @since 2.1.0
     * @access public
     *
     * @param string $month_name Translated month to get abbreviated version
     * @return string Translated abbreviated month
     */
    public function get_month_abbrev($month_name)
    {
        return $this->month_abbrev[$month_name];
    }

    /**
     * Retrieve translated version of meridiem string.
     *
     * The $meridiem parameter is expected to not be translated.
     *
     * @since 2.1.0
     * @access public
     *
     * @param string $meridiem Either 'am', 'pm', 'AM', or 'PM'. Not translated version.
     * @return string Translated version
     */
    public function get_meridiem($meridiem)
    {
        return $this->meridiem[$meridiem];
    }

    /**
     * Global variables are deprecated. For backwards compatibility only.
     *
     * @deprecated For backwards compatibility only.
     * @access private
     *
     * @since 2.1.0
     */
    public function register_globals()
    {
        $GLOBALS['weekday'] = $this->weekday;
        $GLOBALS['weekday_initial'] = $this->weekday_initial;
        $GLOBALS['weekday_abbrev'] = $this->weekday_abbrev;
        $GLOBALS['month'] = $this->month;
        $GLOBALS['month_abbrev'] = $this->month_abbrev;
    }

    /**
     * Constructor which calls helper methods to set up object variables
     *
     * @uses WP_Locale::init()
     * @uses WP_Locale::register_globals()
     * @since 2.1.0
     *
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Checks if current locale is RTL.
     *
     * @since 3.0.0
     * @return bool Whether locale is RTL.
     */
    public function is_rtl()
    {
        return 'rtl' == $this->text_direction;
    }

    /**
     * Register date/time format strings for general POT.
     *
     * Private, unused method to add some date/time formats translated
     * on wp-admin/options-general.php to the general POT that would
     * otherwise be added to the admin POT.
     *
     * @since 3.6.0
     */
    public function _strings_for_pot()
    {
        /* translators: localized date format, see http://php.net/date */
        L10N::__('F j, Y');
        /* translators: localized time format, see http://php.net/date */
        L10N::__('g:i a');
        /* translators: localized date and time format, see http://php.net/date */
        L10N::__('F j, Y g:i a');
    }
}