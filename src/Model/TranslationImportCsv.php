<?php
namespace App\Model;

use InvalidArgumentException;

class TranslationImportCsv
{
    public const MAX_BYTES = 1024 * 1024;
    public const MAX_ROWS = 500;
    public const MAX_TEXT_BYTES = 1500;

    /**
     * Read CSV/TSV records, quoted newlines do not increase record numbers.
     *
     * @param string $contents UTF-8 file contents.
     * @param mixed $format Delimiter name: comma, semicolon or tab.
     * @return array Parsed records with their logical record numbers.
     * @throws InvalidArgumentException If the format, contents or limits are invalid.
     */
    public static function parse(string $contents, $format): array
    {
        $delimiters = [
            'comma' => ',',
            'semicolon' => ';',
            'tab' => "\t",
        ];

        if (!is_string($format) || !isset($delimiters[$format])) {
            throw new InvalidArgumentException(
                __('Please select a valid delimiter.')
            );
        }

        if ($contents === '' || strlen($contents) > self::MAX_BYTES) {
            throw new InvalidArgumentException(
                __('The file must contain data and be at most 1 MiB.')
            );
        }

        if (!mb_check_encoding($contents, 'UTF-8') ||
            strpos($contents, "\0") !== false
        ) {
            throw new InvalidArgumentException(
                __('The file must contain valid UTF-8 text.')
            );
        }

        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        $handle = fopen('php://temp', 'w+');

        if ($handle === false) {
            throw new \RuntimeException('Cannot open temporary stream.');
        }

        try {
            fwrite($handle, $contents);
            rewind($handle);

            $rows = [];
            $recordNumber = 0;
            $firstRecord = true;
            $delimiter = $delimiters[$format];
            $separator = preg_quote($delimiter, '~');
            $field = '(?:[^"' . $separator . '\r\n]*|"(?:[^"]|"")*")';
            $recordPattern = '~\A' . $field . '(?:' . $separator . $field .
                ')*\r?\n?\z~';
            $offset = 0;

            while (($fields = fgetcsv(
                $handle,
                0,
                $delimiter,
                '"',
                ''
            )) !== false) {
                $recordNumber++;
                $end = ftell($handle);
                $record = substr($contents, $offset, $end - $offset);
                $offset = $end;

                if ($fields === [null]) {
                    continue;
                }

                // fgetcsv accepts unterminated quotes and trailing text after a
                // closing quote. Reject those rather than importing altered text.
                if (preg_match($recordPattern, $record) !== 1) {
                    throw new InvalidArgumentException(format(
                        __('Record {record} contains invalid CSV quoting.'),
                        ['record' => $recordNumber]
                    ));
                }

                if ($firstRecord && $fields === [
                    'sentence_id',
                    'source_text',
                    'translation_text',
                ]) {
                    $firstRecord = false;
                    continue;
                }

                $firstRecord = false;

                if (count($fields) !== 3) {
                    throw new InvalidArgumentException(
                        format(
                            __('Record {record} must have three columns.'),
                            ['record' => $recordNumber]
                        )
                    );
                }

                $rawId = trim($fields[0]);

                if (!ctype_digit($rawId) ||
                    strlen($rawId) > 10 ||
                    (int)$rawId < 1 ||
                    (int)$rawId > 2147483647
                ) {
                    throw new InvalidArgumentException(
                        format(
                            __('Record {record} has an invalid sentence ID.'),
                            ['record' => $recordNumber]
                        )
                    );
                }

                foreach ([$fields[1], $fields[2]] as $text) {
                    if (trim($text) === '' ||
                        strlen($text) > self::MAX_TEXT_BYTES
                    ) {
                        throw new InvalidArgumentException(
                            format(
                                __('Record {record} has empty or oversized text.'),
                                ['record' => $recordNumber]
                            )
                        );
                    }
                }

                $rows[] = [
                    'record_number' => $recordNumber,
                    'sentence_id' => (int)$rawId,
                    'source_text' => $fields[1],
                    'translation_text' => $fields[2],
                ];

                if (count($rows) > self::MAX_ROWS) {
                    throw new InvalidArgumentException(
                        __('The file contains too many records.')
                    );
                }
            }

            if (!$rows) {
                throw new InvalidArgumentException(
                    __('The file contains no translation records.')
                );
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }
}
