<?php
/**
 * Created for LeEco User Center
 * User: Wei Zhu<zhuwei1@le.com>
 * Date: 4/27/16
 * Time: 6:20 PM
 * @copyright LeEco
 * @since 1.0.0
 */

namespace L10N;

class Mo extends GettextTranslations
{
    public $_nplurals = 2;

    public function importFromFile($fileName)
    {
        $reader = new GettextFileReader($fileName);
        if (!$reader->isResource())
            return false;
        return $this->importFromReader($reader);
    }

    public static function getByteOrder($magic)
    {
        // The magic is 0x950412de

        // bug in PHP 5.0.2, see https://savannah.nongnu.org/bugs/?func=detailitem&item_id=10565
        $magicLittle = (int)-1794895138;
        $magicLittle64 = (int)2500072158;
        // 0xde120495
        $magicBig = ((int)-569244523) & 0xFFFFFFFF;
        if ($magicLittle == $magic || $magicLittle64 == $magic) {
            return 'little';
        } else if ($magicBig == $magic) {
            return 'big';
        } else {
            return false;
        }
    }

    /**
     * @param $reader GettextFileReader
     * @return bool
     */
    public function importFromReader($reader)
    {
        $endian_string = self::getByteOrder($reader->readInt32());
        if (false === $endian_string) {
            return false;
        }
        $reader->setEndian($endian_string);

        $endian = ('big' == $endian_string) ? 'N' : 'V';

        $header = $reader->read(24);
        if ($reader->strLen($header) != 24)
            return false;

        // parse header
        $header = unpack("{$endian}revision/{$endian}total/{$endian}originals_lenghts_addr/{$endian}translations_lenghts_addr/{$endian}hash_length/{$endian}hash_addr",
                         $header);
        if (!is_array($header))
            return false;

        // support revision 0 of MO format specs, only
        if ($header['revision'] != 0) {
            return false;
        }

        // seek to data blocks
        $reader->seekTo($header['originals_lenghts_addr']);

        // read originals' indices
        $originals_lengths_length = $header['translations_lenghts_addr'] - $header['originals_lenghts_addr'];
        if ($originals_lengths_length != $header['total'] * 8) {
            return false;
        }

        $originals = $reader->read($originals_lengths_length);
        if ($reader->strLen($originals) != $originals_lengths_length) {
            return false;
        }

        // read translations' indices
        $translations_lenghts_length = $header['hash_addr'] - $header['translations_lenghts_addr'];
        if ($translations_lenghts_length != $header['total'] * 8) {
            return false;
        }

        $translations = $reader->read($translations_lenghts_length);
        if ($reader->strLen($translations) != $translations_lenghts_length) {
            return false;
        }

        // transform raw data into set of indices
        $originals = $reader->strSplit($originals, 8);
        $translations = $reader->strSplit($translations, 8);

        // skip hash table
        $strings_addr = $header['hash_addr'] + $header['hash_length'] * 4;

        $reader->seekTo($strings_addr);

        $strings = $reader->readAll();
        $reader->close();

        for ($i = 0; $i < $header['total']; $i++) {
            $o = unpack("{$endian}length/{$endian}pos", $originals[$i]);
            $t = unpack("{$endian}length/{$endian}pos", $translations[$i]);
            if (!$o || !$t) return false;

            // adjust offset due to reading strings to separate space before
            $o['pos'] -= $strings_addr;
            $t['pos'] -= $strings_addr;

            $original = $reader->subStr($strings, $o['pos'], $o['length']);
            $translation = $reader->subStr($strings, $t['pos'], $t['length']);

            if ('' === $original) {
                $this->setHeaders($this->makeHeaders($translation));
            } else {
                $entry = $this->makeEntry($original, $translation);
                $this->entries[$entry->key()] = $entry;
            }
        }
        return true;
    }

    public function selectPluralForm($count)
    {
        parent::gettextSelectPluralForm($count); // TODO: Change the autogenerated stub
    }

    /**
     * TODO Export to mo
     */
    public function exportToFile($filename)
    {
    }

    public function export()
    {
    }

    public function isEntryReadyForExport()
    {
    }

    public function exportToFileHandle()
    {
    }

    public function exportOriginal()
    {
    }

    public function exportTranslations()
    {
    }

    public function exportHeaders()
    {
    }

    public function makeEntry($original, $translation)
    {
        $entry = new TranslationEntry();
        // look for context
        $parts = explode(chr(4), $original);
        if (isset($parts[1])) {
            $original = $parts[1];
            $entry->context = $parts[0];
        }
        // look for plural original
        $parts = explode(chr(0), $original);
        $entry->singular = $parts[0];
        if (isset($parts[1])) {
            $entry->isPlural = true;
            $entry->plural = $parts[1];
        }
        // plural translations are also separated by \0
        $entry->translations = explode(chr(0), $translation);
        return $entry;
    }

}