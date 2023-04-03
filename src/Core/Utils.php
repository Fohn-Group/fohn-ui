<?php

declare(strict_types=1);
/**
 * Core utility.
 */

namespace Fohn\Ui\Core;

use DeepCopy\DeepCopy;

class Utils
{
    /**
     * Shorten name.
     * Thanks to Roy Tanck
     * https://roytanck.com/2021/10/17/generating-short-hashes-in-php/.
     */
    public static function generateId(string $longName, string $keep = '', int $length = 10): string
    {
        if ($keep) {
            $longName = str_replace($keep, '', $longName);
            $keep = '-' . $keep;
        }
        // Create a raw binary sha256 hash and base64 encode it.
        $hashBase64 = base64_encode(hash('sha256', $longName, true));
        // Replace non-urlsafe chars to make the string urlsafe.
        $hashUrlsafe = strtr($hashBase64, '+/', '__');
        // Trim base64 padding characters from the end.
        $hashUrlsafe = rtrim($hashUrlsafe, '=');

        // Shorten the string before returning.
        return substr($hashUrlsafe, 0, $length) . $keep;
    }

    public static function hasValidOptions(array $options, array $validKeys): bool
    {
        $isValid = true;
        foreach ($options as $key => $value) {
            if (!in_array($key, $validKeys, true)) {
                $isValid = false;

                break;
            }
        }

        return $isValid;
    }

    public static function getFromClassName(string $className): string
    {
        if (strpos($className, 'class@anonymous') !== false) {
            return 'anonymous';
        }

        $matches = [];
        preg_match('/(?<name>[^\\\\]+$)/m', $className, $matches);

        $str = lcfirst($matches['name']);
        $str = preg_replace('/[A-Z]/', '-$0', $str);

        return strtolower($str);
    }

    /**
     * Return a deep copy of an Object.
     */
    public static function copy(object $object): object
    {
        return (new DeepCopy(true))->copy($object);
    }

    /**
     * Merge object seeds together but prioritizing the first one.
     *
     * Utils::mergeSeeds(['classA', 'name' = 'A'], ['classB', 'name' => 'B', 'prop1' => 'P1']
     * will return ['classA', 'name' = 'A', 'prop1' => 'P1'].
     */
    public static function mergeSeeds(array ...$seeds): array
    {
        // move numerical keys to the beginning and sort them
        $arguments = [];
        $injection = [];
        foreach ($seeds as $seedIndex => $seed) {
            foreach ($seed as $k => $v) {
                if (is_int($k)) {
                    if (!isset($arguments[$k])) {
                        $arguments[$k] = $v;
                    }
                } else {
                    if (!isset($injection[$k])) {
                        $injection[$k] = $v;
                    }
                }
            }
        }

        ksort($arguments, \SORT_NUMERIC);

        return $arguments + $injection;
    }

    public static function decodeJson(string $json, bool $isAssociative = true): array
    {
        $data = json_decode($json, $isAssociative, 512, \JSON_THROW_ON_ERROR);

        return $data;
    }

    /**
     * Encode Json data for Javascript.
     * When integer exceed max integer value in Javascript - it will convert
     * it to Js Big Int string. It is to the Js Parser job to properly convert the string
     * to a Big integer value.
     * ex: "{"jsInt": 9007199254740991,"jsBigInt": "9007199254740992n"}".
     *
     * @param mixed $data
     */
    public static function encodeJson($data, bool $forceObject = false, bool $prettyPrint = false): string
    {
        $options = \JSON_UNESCAPED_SLASHES | \JSON_PRESERVE_ZERO_FRACTION | \JSON_UNESCAPED_UNICODE;
        if ($forceObject) {
            $options |= \JSON_FORCE_OBJECT;
        }

        if ($prettyPrint) {
            $options |= \JSON_PRETTY_PRINT;
        }

        $json = json_encode($data, $options | \JSON_THROW_ON_ERROR, 512);

        // Convert integer value for Javascript.
        // If value is larger than max javascript integer value then it will be convert to a big int.
        $pattern = '~"(?:[^"\\\\]+|\\\\.)*+"\K|\'(?:[^\'\\\\]+|\\\\.)*+\'\K|(?:^|[{\[,:])[ \n\r\t]*\K-?[1-9]\d{15,}(?=[ \n\r\t]*(?:$|[}\],:]))~s';
        $json = preg_replace_callback($pattern, function ($matches) {
            if ($matches[0] === '' || abs((int) $matches[0]) < (2 ** 53)) {
                return $matches[0];
            }
            // return big int.
            return '"' . $matches[0] . 'n"';
        }, $json);

        return $json;
    }

    /**
     * Construct HTML tag with supplied attributes.
     *
     * $html = buildTag('img/', ['src'=>'foo.gif','border'=>0]);
     * // "<img src="foo.gif" border="0"/>"
     *
     *
     * The following rules are respected:
     *
     * 1. all array key=>val elements appear as attributes with value escaped.
     * buildTag('div/', ['data'=>'he"llo']);
     * --> <div data="he\"llo"/>
     *
     * 2. boolean value true will add attribute without value
     * buildTag('td', ['nowrap'=>true]);
     * --> <td nowrap>
     *
     * 3. null and false value will ignore the attribute
     * buildTag('img', ['src'=>false]);
     * --> <img>
     *
     * 4. passing key 0=>"val" will re-define the element itself
     * buildTag('img', ['input', 'type'=>'picture']);
     * --> <input type="picture" src="foo.gif">
     *
     * 5. use '/' at end of tag to close it.
     * buildTag('img/', ['src'=>'foo.gif']);
     * --> <img src="foo.gif"/>
     *
     * 6. if main tag is self-closing, overriding it keeps it self-closing
     * buildTag('img/', ['input', 'type'=>'picture']);
     * --> <input type="picture" src="foo.gif"/>
     *
     * 7. simple way to close tag. Any attributes to closing tags are ignored
     * buildTag('/td');
     * --> </td>
     *
     * 7b. except for 0=>'newtag'
     * buildTag('/td', ['th', 'align'=>'left']);
     * --> </th>
     *
     * 8. using $value will add value inside tag. It will also encode value.
     * buildTag('a', ['href'=>'foo.html'] ,'click here >>');
     * --> <a href="foo.html">click here &gt;&gt;</a>
     *
     * 9. you may skip attribute argument.
     * buildTag('b','text in bold');
     * --> <b>text in bold</b>
     *
     * 10. pass array as 3rd parameter to nest tags. Each element can be either string (inserted as-is) or
     * array (passed to getTag recursively)
     * buildTag('a', ['href'=>'foo.html'], [['b','click here'], ' for fun']);
     * --> <a href="foo.html"><b>click here</b> for fun</a>
     *
     * 11. extended example:
     * buildTag('a', ['href'=>'hello'], [
     *    ['b', 'class'=>'red', [
     *        ['i', 'class'=>'blue', 'welcome']
     *    ]]
     * ]);
     * --> <a href="hello"><b class="red"><i class="blue">welcome</i></b></a>'
     *
     * @param string|array $tag
     * @param string|array $attr
     * @param string|array $value
     */
    public static function buildTag($tag = null, $attr = null, $value = null): string
    {
        if ($tag === null) {
            $tag = 'div';
        } elseif (is_array($tag)) {
            $tmp = $tag;

            if (isset($tmp[0])) {
                $tag = $tmp[0];

                if (is_array($tag)) {
                    // OH a bunch of tags
                    $output = '';
                    foreach ($tmp as $subtag) {
                        $output .= self::buildTag($subtag);
                    }

                    return $output;
                }

                unset($tmp[0]);
            } else {
                $tag = 'div';
            }

            if (isset($tmp[1])) {
                $value = $tmp[1];
                unset($tmp[1]);
            } else {
                $value = null;
            }

            $attr = $tmp;
        }

        $tag = strtolower($tag);

        if ($tag[0] === '<') {
            return $tag;
        }
        if (is_string($attr)) {
            $value = $attr;
            $attr = null;
        }

        if ($value !== null) {
            $result = [];
            foreach ((array) $value as $v) {
                if (is_array($v)) {
                    $result[] = self::buildTag(...$v);
                } elseif (in_array($tag, ['script', 'style'], true)) {
                    // see https://mathiasbynens.be/notes/etago
                    $result[] = preg_replace('~(?<=<)(?=/\s*' . preg_quote($tag, '~') . '|!--)~', '\\\\', $v);
                } elseif (is_array($value)) {
                    $result[] = $v;
                } else {
                    $result[] = self::encodeHtml($v);
                }
            }
            $value = implode('', $result);
        }

        if (!$attr) {
            return "<{$tag}>" . ($value !== null ? $value . "</{$tag}>" : '');
        }
        $tmp = [];
        if (substr($tag, -1) === '/') {
            $tag = substr($tag, 0, -1);
            $postfix = '/';
        } elseif (substr($tag, 0, 1) === '/') {
            return '</' . ($attr[0] ?? substr($tag, 1)) . '>';
        } else {
            $postfix = '';
        }
        foreach ($attr as $key => $val) {
            if ($val === false) {
                continue;
            }
            if ($val === true) {
                $tmp[] = "{$key}";
            } elseif ($key === 0) {
                $tag = $val;
            } else {
                $tmp[] = "{$key}=\"" . self::encodeAttribute($val) . '"';
            }
        }

        return "<{$tag}" . ($tmp ? (' ' . implode(' ', $tmp)) : '') . $postfix . '>' . ($value !== null ? $value . "</{$tag}>" : '');
    }

    /**
     * Encodes string - removes HTML special chars.
     *
     * @param string $val
     */
    public static function encodeAttribute($val): string
    {
        return htmlspecialchars((string) $val);
    }

    /**
     * Encodes string - removes HTML entities.
     */
    public static function encodeHtml(string $val): string
    {
        return htmlentities($val);
    }

    public static function getLoremIpsum(int $numberOfWords): string
    {
        $punctuation = ['. ', '. ', '. ', '. ', '. ', '. ', '. ', '. ', '... ', '! ', '? '];

        $dictionary = ['abbas', 'abdo', 'abico', 'abigo', 'abluo', 'accumsan',
            'acsi', 'ad', 'adipiscing', 'aliquam', 'aliquip', 'amet', 'antehabeo',
            'appellatio', 'aptent', 'at', 'augue', 'autem', 'bene', 'blandit',
            'brevitas', 'caecus', 'camur', 'capto', 'causa', 'cogo', 'comis',
            'commodo', 'commoveo', 'consectetuer', 'consequat', 'conventio', 'cui',
            'damnum', 'decet', 'defui', 'diam', 'dignissim', 'distineo', 'dolor',
            'dolore', 'dolus', 'duis', 'ea', 'eligo', 'elit', 'enim', 'erat',
            'eros', 'esca', 'esse', 'et', 'eu', 'euismod', 'eum', 'ex', 'exerci',
            'exputo', 'facilisi', 'facilisis', 'fere', 'feugiat', 'gemino',
            'genitus', 'gilvus', 'gravis', 'haero', 'hendrerit', 'hos', 'huic',
            'humo', 'iaceo', 'ibidem', 'ideo', 'ille', 'illum', 'immitto',
            'importunus', 'imputo', 'in', 'incassum', 'inhibeo', 'interdico',
            'iriure', 'iusto', 'iustum', 'jugis', 'jumentum', 'jus', 'laoreet',
            'lenis', 'letalis', 'lobortis', 'loquor', 'lucidus', 'luctus', 'ludus',
            'luptatum', 'macto', 'magna', 'mauris', 'melior', 'metuo', 'meus',
            'minim', 'modo', 'molior', 'mos', 'natu', 'neo', 'neque', 'nibh',
            'nimis', 'nisl', 'nobis', 'nostrud', 'nulla', 'nunc', 'nutus', 'obruo',
            'occuro', 'odio', 'olim', 'oppeto', 'os', 'pagus', 'pala', 'paratus',
            'patria', 'paulatim', 'pecus', 'persto', 'pertineo', 'plaga', 'pneum',
            'populus', 'praemitto', 'praesent', 'premo', 'probo', 'proprius',
            'quadrum', 'quae', 'qui', 'quia', 'quibus', 'quidem', 'quidne', 'quis',
            'ratis', 'refero', 'refoveo', 'roto', 'rusticus', 'saepius',
            'sagaciter', 'saluto', 'scisco', 'secundum', 'sed', 'si', 'similis',
            'singularis', 'sino', 'sit', 'sudo', 'suscipere', 'suscipit', 'tamen',
            'tation', 'te', 'tego', 'tincidunt', 'torqueo', 'tum', 'turpis',
            'typicus', 'ulciscor', 'ullamcorper', 'usitas', 'ut', 'utinam',
            'utrum', 'uxor', 'valde', 'valetudo', 'validus', 'vel', 'velit',
            'veniam', 'venio', 'vereor', 'vero', 'verto', 'vicis', 'vindico',
            'virtus', 'voco', 'volutpat', 'vulpes', 'vulputate', 'wisi', 'ymo',
            'zelus', ];

        $lorem = '';

        while ($numberOfWords > 0) {
            $sentence_length = random_int(3, 10);

            $lorem .= ucfirst($dictionary[array_rand($dictionary)]);
            for ($i = 1; $i < $sentence_length; ++$i) {
                $lorem .= ' ' . $dictionary[array_rand($dictionary)];
            }

            $lorem .= $punctuation[array_rand($punctuation)];
            $numberOfWords -= $sentence_length;
        }

        return $lorem;
    }
}
