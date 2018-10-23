<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/28/16
 * Time: 2:56 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;


class GettextTranslations extends Translations
{
    private $_nplurals;

    /**
     * @param $count
     * @return mixed
     */
    public function gettextSelectPluralForm($count)
    {
        if (!isset($this->_gettext_select_plural_form) || is_null($this->_gettext_select_plural_form)) {
            list($nplurals, $expression) = $this->npluralsAndExpressionFromHeader($this->getHeader('Plural-Forms'));
            $this->_nplurals = $nplurals;
            $this->_gettext_select_plural_form = $this->makePluralFormFunction($nplurals, $expression);
        }
        return call_user_func($this->_gettext_select_plural_form, $count);
    }

    public function npluralsAndExpressionFromHeader($header)
    {
        if (preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches)) {
            $nplurals = (int)$matches[1];
            $expression = trim($this->parenthesizePluralExpression($matches[2]));
            return [
                $nplurals,
                $expression
            ];
        } else {
            return [
                2,
                'n != 1'
            ];
        }
    }

    /**
     * @param $nplurals
     * @param $expression
     * @return mixed
     */
    public function makePluralFormFunction($nplurals, $expression)
    {
        $expression = str_replace('n', '$n', $expression);
        $func_body = "
			\$index = (int)($expression);
			return (\$index < $nplurals)? \$index : $nplurals - 1;";
        return create_function('$n', $func_body);
    }

    public function parenthesizePluralExpression($expression)
    {
        $expression .= ';';
        $res = '';
        $depth = 0;
        for ($i = 0; $i < strlen($expression); ++$i) {
            $char = $expression[$i];
            switch ($char) {
                case '?':
                    $res .= ' ? (';
                    $depth++;
                    break;
                case ':':
                    $res .= ') : (';
                    break;
                case ';':
                    $res .= str_repeat(')', $depth) . ';';
                    $depth = 0;
                    break;
                default:
                    $res .= $char;
            }
        }
        return rtrim($res, ';');
    }

    public function makeHeaders($translation)
    {
        $headers = [];
        // sometimes \ns are used instead of real new lines
        $translation = str_replace('\n', "\n", $translation);
        $lines = explode("\n", $translation);
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (!isset($parts[1])) continue;
            $headers[trim($parts[0])] = trim($parts[1]);
        }
        return $headers;
    }

    public function setHeader($header, $value)
    {
        parent::setHeader($header, $value);
        if ('Plural-Forms' == $header) {
            list($nplurals, $expression) = $this->npluralsAndExpressionFromHeader($this->getHeader('Plural-Forms'));
            $this->_nplurals = $nplurals;
            $this->_gettext_select_plural_form = $this->makePluralFormFunction($nplurals, $expression);
        }
    }

}