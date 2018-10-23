<?php
/**
 * Created for LeEco User Center
 * User: Yishu Gong<gongyishu@le.com>
 * Date: 2016/5/11
 * Time: 7:33
 * @copyright LeEco
 * @since 1.0.0
 */

namespace Lephp\Core;


class Security
{
    /**
     * Csrf 加密KEY
     * @var string
     */
    private static $sec = '7gtc096z1wW8';

    private $allow_http_value = false;
    private $allow_htmlspecialchars = true;
    private $input;
    private $preg_patterns = [
        // Fix &entity\n
        '!(&#0+[0-9]+)!'                                                                                                                                                                                => '$1;',
        '/(&#*\w+)[\x00-\x20]+;/u'                                                                                                                                                                      => '$1;>',
        '/(&#x*[0-9A-F]+);*/iu'                                                                                                                                                                         => '$1;',
        //any attribute starting with "on" or xmlns
        '#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu'                                                                                                                                                => '$1>',
        //javascript: and vbscript: protocols
        '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu' => '$1=$2nojavascript...',
        '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu'                                        => '$1=$2novbscript...',
        '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u'                                                                                                                         => '$1=$2nomozbinding...',
        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i'                                                                                                           => '$1>',
        '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu'                                                  => '$1>',
        // namespaced elements
        '#</*\w+:\w[^>]*+>#i'                                                                                                                                                                           => '',
        //unwanted tags
        '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i'                                                         => ''
    ];

    private $normal_patterns = [
        '\'' => '&lsquo;',
        '"'  => '&quot;',
        '&'  => '&amp;',
        '<'  => '&lt;',
        '>'  => '&gt;'
    ];


    /**
     * 生成CSRF加密串
     * @return string
     */
    public static function createCsrf()
    {
        $time = time();
        $csrf_code = md5($time . self::$sec) . '.' . $time;
        return $csrf_code;
    }

    /**
     * 验证CSRF串的有效性
     * @param $csrf_code
     * @param int $expire
     * @return bool
     */
    public static function checkCsrf($csrf_code, $expire = 0)
    {
        if (strpos($csrf_code, '.') !== false) {
            list($md5_code, $code_ctime) = explode(".", $csrf_code);
            if (md5($code_ctime . self::$sec) != $md5_code) {
                return false;
            }
        } else {
            return false;
        }

        if (is_numeric($expire) && $expire > 0) {
            if ((time() - $code_ctime) > $expire) {
                return false;
            }
        }
        return true;
    }

    /**
     * xss_filter::filter_it()
     * @param $in
     * @return string
     */
    public function filter_it($in)
    {
        $this->input = html_entity_decode($in, ENT_NOQUOTES, 'UTF-8');
        $this->normal_replace();
        $this->do_grep();
        return $this->input;
    }

    /**
     * xss_filter::allow_http()
     *
     */
    public function allow_http()
    {
        $this->allow_http_value = true;
    }

    /**
     * xss_filter::disallow_http()
     *
     */
    public function disallow_http()
    {
        $this->allow_http_value = false;
    }

    /**
     * xss_filter::allow_htmlspecialchars()
     *
     */
    public function allow_htmlspecialchars()
    {
        $this->allow_htmlspecialchars = true;
    }

    /**
     * xss_filter::disallow_htmlspecialchars()
     *
     */
    public function disallow_htmlspecialchars()
    {
        $this->allow_htmlspecialchars = false;
    }

    /**
     * xss_filter::normal_replace()
     *
     * @access private
     */
    private function normal_replace()
    {
        $this->input = str_replace([
                                       '&amp;',
                                       '&lt;',
                                       '&gt;'
                                   ], [
                                       '&amp;amp;',
                                       '&amp;lt;',
                                       '&amp;gt;'
                                   ], $this->input);
        if ($this->allow_http_value == false) {

            $this->input = str_replace([
                                           '&',
                                           '%',
                                           'script',
                                           //'http',
                                           'localhost'
                                       ], [
                                           '',
                                           '',
                                           '',
                                           '',
                                           ''
                                       ], $this->input);

        } else {

            $this->input = str_replace([
                                           '&',
                                           '%',
                                           'script',
                                           'localhost'
                                       ], [
                                           '',
                                           '',
                                           '',
                                           ''
                                       ], $this->input);
        }

        if ($this->allow_htmlspecialchars == true) {

            foreach ($this->normal_patterns as $pattern => $replacement) {
                $this->input = str_replace($pattern, $replacement, $this->input);
            }
        }

    }

    /**
     * xss_filter::do_grep()
     *
     * @access private
     */
    private function do_grep()
    {
        foreach ($this->preg_patterns as $pattern => $replacement) {
            $this->input = preg_replace($pattern, $replacement, $this->input);
        }
    }

    /**
     * XSS Clean
     *
     * **************************************************************
     * *********** This function and other functions that it uses
     * *********** are taken from Codeigniter 2.1.3 and modified
     * *********** them to our needs. In turn, I have taken this from
     * *********** JasonMortonNZ.
     ***************************************************************
     *
     *
     * Sanitizes data so that Cross Site Scripting Hacks can be
     * prevented.  This function does a fair amount of work but
     * it is extremely thorough, designed to prevent even the
     * most obscure XSS attempts.  Nothing is ever 100% foolproof,
     * of course, but I haven't been able to get anything passed
     * the filter.
     *
     * Note: This function should only be used to deal with data
     * upon submission.  It's not something that should
     * be used for general runtime processing.
     *
     * This function was based in part on some code and ideas I
     * got from Bitflux: http://channel.bitflux.ch/wiki/XSS_Prevention
     *
     * To help develop this script I used this great list of
     * vulnerabilities along with a few other hacks I've
     * harvested from examining vulnerabilities in other programs:
     * http://ha.ckers.org/xss.html
     *
     * @param   mixed   string or array
     * @param   bool
     * @return  string
     */
    public static function xss_clean($str, $is_image = FALSE)
    {
        /*
         * Is the string an array?
         *
         */
        if (is_array($str)) {
            while (list($key) = each($str)) {
                $str[$key] = self::xss_clean($str[$key]);
            }
            return $str;
        }
        /*
         * Remove Invisible Characters
         */
        $str = self::remove_invisible_characters($str);
        // Validate Entities in URLs
        $str = self::validate_entities($str);
        /*
         * URL Decode
         *
         * Just in case stuff like this is submitted:
         *
         * <a href="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">Google</a>
         *
         * Note: Use rawurldecode() so it does not remove plus signs
         *
         */
        $str = rawurldecode($str);
        /*
         * Convert character entities to ASCII
         *
         * This permits our tests below to work reliably.
         * We only convert entities that are within tags since
         * these are the ones that will pose security problems.
         *
         */
        $str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", function ($match) {
            return str_replace([
                                   '>',
                                   '<',
                                   '\\'
                               ], [
                                   '&gt;',
                                   '&lt;',
                                   '\\\\'
                               ], $match[0]);
        }, $str);
        $str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", 'self::entity_decode', $str);
        /*
         * Remove Invisible Characters Again!
         */
        $str = self::remove_invisible_characters($str);
        /*
         * Convert all tabs to spaces
         *
         * This prevents strings like this: ja  vascript
         * NOTE: we deal with spaces between characters later.
         * NOTE: preg_replace was found to be amazingly slow here on
         * large blocks of data, so we use str_replace.
         */
        if (strpos($str, "\t") !== FALSE) {
            $str = str_replace("\t", ' ', $str);
        }
        /*
         * Capture converted string for later comparison
         */
        $converted_string = $str;
        // Remove Strings that are never allowed
        $str = self::do_never_allowed($str);
        /*
         * Makes PHP tags safe
         *
         * Note: XML tags are inadvertently replaced too:
         *
         * <?xml
         *
         * But it doesn't seem to pose a problem.
         */
        if ($is_image === TRUE) {
            // Images have a tendency to have the PHP short opening and
            // closing tags every so often so we skip those and only
            // do the long opening tags.
            $str = preg_replace('/<\?(php)/i', "&lt;?\\1", $str);
        } else {
            $str = str_replace([
                                   '<?',
                                   '?' . '>'
                               ], [
                                   '&lt;?',
                                   '?&gt;'
                               ], $str);
        }
        /*
         * Compact any exploded words
         *
         * This corrects words like:  j a v a s c r i p t
         * These words are compacted back to their correct state.
         */
        $words = [
            'javascript',
            'expression',
            'vbscript',
            'script',
            'base64',
            'applet',
            'alert',
            'document',
            'write',
            'cookie',
            'window'
        ];
        foreach ($words as $word) {
            $temp = '';
            for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++) {
                $temp .= substr($word, $i, 1) . "\s*";
            }
            // We only want to do this when it is followed by a non-word character
            // That way valid stuff like "dealer to" does not become "dealerto"
            $str = preg_replace_callback('#(' . substr($temp, 0, -3) . ')(\W)#is', function ($matches) {
                return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
            }, $str);
        }
        /*
         * Remove disallowed Javascript in links or img tags
         * We used to do some version comparisons and use of stripos for PHP5,
         * but it is dog slow compared to these simplified non-capturing
         * preg_match(), especially if the pattern exists in the string
         */
        do {
            $original = $str;
            if (preg_match("/<a/i", $str)) {
                $str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", function ($match) {
                    return str_replace(
                        $match[1],
                        preg_replace(
                            '#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
                            '',
                            self::filter_attributes(str_replace([
                                                                    '<',
                                                                    '>'
                                                                ], '', $match[1]))
                        ),
                        $match[0]
                    );
                }, $str);
            }
            if (preg_match("/<img/i", $str)) {
                $str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", function ($match) {
                    return str_replace(
                        $match[1],
                        preg_replace(
                            '#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
                            '',
                            self::filter_attributes(str_replace([
                                                                    '<',
                                                                    '>'
                                                                ], '', $match[1]))
                        ),
                        $match[0]
                    );
                }, $str);
            }
            if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str)) {
                $str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
            }
        } while ($original != $str);
        unset($original);
        // Remove evil attributes such as style, onclick and xmlns
        $str = self::remove_evil_attributes($str, $is_image);
        /*
         * Sanitize naughty HTML elements
         *
         * If a tag containing any of the words in the list
         * below is found, the tag gets converted to entities.
         *
         * So this: <blink>
         * Becomes: &lt;blink&gt;
         */
        $naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
        $str = preg_replace_callback('#<(/*\s*)(' . $naughty . ')([^><]*)([><]*)#is', function ($matches) {
            // encode opening brace
            $str = '&lt;' . $matches[1] . $matches[2] . $matches[3];
            // encode captured opening or closing brace to prevent recursive vectors
            return $str .= str_replace([
                                           '>',
                                           '<'
                                       ], [
                                           '&gt;',
                                           '&lt;'
                                       ], $matches[4]);
        }, $str);
        /*
         * Sanitize naughty scripting elements
         *
         * Similar to above, only instead of looking for
         * tags it looks for PHP and JavaScript commands
         * that are disallowed.  Rather than removing the
         * code, it simply converts the parenthesis to entities
         * rendering the code un-executable.
         *
         * For example: eval('some code')
         * Becomes:     eval&#40;'some code'&#41;
         */
        $str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
                            "\\1\\2&#40;\\3&#41;", $str);
        // Final clean up
        // This adds a bit of extra precaution in case
        // something got through the above filters
        $str = self::do_never_allowed($str);
        /*
         * Images are Handled in a Special Way
         * - Essentially, we want to know that after all of the character
         * conversion is done whether any unwanted, likely XSS, code was found.
         * If not, we return TRUE, as the image is clean.
         * However, if the string post-conversion does not matched the
         * string post-removal of XSS, then it fails, as there was unwanted XSS
         * code found and removed/changed during processing.
         */
        if ($is_image === TRUE) {
            return ($str == $converted_string) ? TRUE : FALSE;
        }
        return $str;
    }

    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @access  public
     * @param   string
     * @return  string
     */
    protected static function remove_invisible_characters($str, $url_encoded = TRUE)
    {

        $non_displayables = [];

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/';  // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';   // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127
        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);
        return $str;
    }

    /**
     * Validate URL entities
     *
     * Called by xss_clean()
     *
     * @param   string
     * @return  string
     */
    protected static function validate_entities($str)
    {
        /*
         * Protect GET variables in URLs
         */
        $xss_hash = md5(time() + mt_rand(0, 1999999999));
        $str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $xss_hash . "\\1=\\2", $str);
        /*
         * Validate standard character entities
         *
         * Add a semicolon if missing.  We do this to enable
         * the conversion of entities to ASCII later.
         *
         */
        $str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);
        /*
         * Validate UTF16 two byte encoding (x00)
         *
         * Just as above, adds a semicolon if missing.
         *
         */
        $str = preg_replace('#(&\#x?)([0-9A-F]+);?#i', "\\1\\2;", $str);
        /*
         * Un-Protect GET variables in URLs
         */
        $str = str_replace($xss_hash, '&', $str);
        return $str;
    }

    /**
     * Do Never Allowed
     *
     * A utility function for xss_clean()
     *
     * @param   string
     * @return  string
     */
    protected static function do_never_allowed($str)
    {
        /**
         * List of never allowed strings
         */
        $never_allowed_str = [
            'document.cookie' => '[removed]',
            'document.write'  => '[removed]',
            '.parentNode'     => '[removed]',
            '.innerHTML'      => '[removed]',
            'window.location' => '[removed]',
            '-moz-binding'    => '[removed]',
            '<!--'            => '&lt;!--',
            '-->'             => '--&gt;',
            '<![CDATA['       => '&lt;![CDATA[',
            '<comment>'       => '&lt;comment&gt;'
        ];
        /**
         * List of never allowed regex replacement
         */
        $never_allowed_regex = [
            'javascript\s*:',
            'expression\s*(\(|&\#40;)',
            // CSS and IE
            'vbscript\s*:',
            // IE, surprise!
            'Redirect\s+302',
            "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
        ];
        $str = str_replace(array_keys($never_allowed_str), $never_allowed_str, $str);
        foreach ($never_allowed_regex as $regex) {
            $str = preg_replace('#' . $regex . '#is', '[removed]', $str);
        }
        return $str;
    }

    /*
     * Remove Evil HTML Attributes (like evenhandlers and style)
     *
     * It removes the evil attribute and either:
     *  - Everything up until a space
     *      For example, everything between the pipes:
     *      <a |style=document.write('hello');alert('world');| class=link>
     *  - Everything inside the quotes
     *      For example, everything between the pipes:
     *      <a |style="document.write('hello'); alert('world');"| class="link">
     *
     * @param string $str The string to check
     * @param boolean $is_image TRUE if this is an image
     * @return string The string with the evil attributes removed
     */
    protected static function remove_evil_attributes($str, $is_image)
    {
        $evil_attributes = [
            'on\w*',
            'style',
            'xmlns',
            'formaction'
        ];
        if ($is_image === TRUE) {
            unset($evil_attributes[array_search('xmlns', $evil_attributes)]);
        }
        do {
            $count = 0;
            $attribs = [];
            // find occurrences of illegal attribute strings without quotes
            preg_match_all('/(' . implode('|', $evil_attributes) . ')\s*=\s*([^\s>]*)/is', $str, $matches,
                           PREG_SET_ORDER);
            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }
            // find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
            preg_match_all("/(" . implode('|', $evil_attributes) . ")\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is", $str,
                           $matches, PREG_SET_ORDER);
            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }
            // replace illegal attribute strings that are inside an html tag
            if (count($attribs) > 0) {
                $str = preg_replace("/<(\/?[^><]+?)([^A-Za-z<>\-])(.*?)(" . implode('|',
                                                                                    $attribs) . ")(.*?)([\s><])([><]*)/i",
                                    '<$1 $3$5$6$7', $str, -1, $count);
            }
        } while ($count);
        return $str;
    }

    /**
     * HTML Entities Decode
     *
     * This function is a replacement for html_entity_decode()
     *
     * The reason we are not using html_entity_decode() by itself is because
     * while it is not technically correct to leave out the semicolon
     * at the end of an entity most browsers will still interpret the entity
     * correctly.  html_entity_decode() does not convert entities without
     * semicolons, so we are left with our own little solution here. Bummer.
     *
     * @param   string
     * @param   string
     * @return  string
     */
    protected static function entity_decode($arr, $charset = 'UTF-8')
    {
        $str = $arr[0];
        if (stristr($str, '&') === FALSE) {
            return $str;
        }
        $str = html_entity_decode($str, ENT_COMPAT, $charset);
        $str = preg_replace_callback('~&#x(0*[0-9a-f]{2,5})~i',
                                     create_function('$matches', 'return chr(hexdec($matches[1]));'), $str);
        return preg_replace_callback('~&#([0-9]{2,4})~', create_function('$matches', 'return chr($matches[1]);'), $str);
    }

    /**
     * Filter Attributes
     *
     * Filters tag attributes for consistency and safety
     *
     * @param   string
     * @return  string
     */
    protected static function filter_attributes($str)
    {
        $out = '';
        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace("#/\*.*?\*/#s", '', $match);
            }
        }
        return $out;
    }
}