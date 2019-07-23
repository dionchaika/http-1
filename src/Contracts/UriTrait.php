<?php

namespace Lazy\Http\Contracts;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    public static function removeDotSegments($path)
    {
        $input = $path;
        $output = [];

        while ('' !== $input) {
            /**
             * A.  If the input buffer begins with a prefix of "../" or "./",
             *     then remove that prefix from the input buffer; otherwise,
             */
            if (0 === strpos($input, './')) {
                $input = substr($input, 2);
                continue;
            }
            if (0 === strpos($input, '../')) {
                $input = substr($input, 3);
                continue;
            }

            /**
             * B.  if the input buffer begins with a prefix of "/./" or "/.",
             *     where "." is a complete path segment, then replace that
             *     prefix with "/" in the input buffer; otherwise,
             */
            if ('/.' === $input) {
                $output[] = '/';
                break;
            }
            if (0 === strpos($input, '/./')) {
                $input = substr($input, 2);
                continue;
            }

            /**
             * C.  if the input buffer begins with a prefix of "/../" or "/..",
             *     where ".." is a complete path segment, then replace that
             *     prefix with "/" in the input buffer and remove the last
             *     segment and its preceding "/" (if any) from the output
             *     buffer; otherwise,
             */
            if ('/..' === $input) {
                array_pop($output);
                $output[] = '/';

                break;
            }
            if (0 === strpos($input, '/../')) {
                array_pop($output);
                $input = substr($input, 3);

                continue;
            }

            /**
             * D.  if the input buffer consists only of "." or "..", then remove
             *     that from the input buffer; otherwise,
             */
            if ('.' === $input || '..' === $input) {
                break;
            }

            /**
             * E.  move the first path segment in the input buffer to the end of
             *     the output buffer, including the initial "/" character (if
             *     any) and any subsequent characters up to, but not including,
             *     the next "/" character or the end of the input buffer.
             */
            if (false === $slashPos = strpos($input, '/', 1)) {
                $output[] = $input;
                break;
            } else {
                $output[] = substr($input, 0, $slashPos);
                $input = substr($input, $slashPos);
            }
        }

        return implode($output);
    }
}
