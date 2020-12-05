<?php declare(strict_types=1);

namespace Krasilnikovm\Compiler;

/**
 * Class Scanner
 *
 * @author Mihail Krasilnikov <mihail.krasilnikov.j@gmail.com>
 */
final class Scanner
{
    private const KEYWORDS = [
        'while',
        'nil',
        'do',
        'if',
        'then',
        'end',
        'begin',
    ];

    private const ONE_DELIMITER = [
        '=',
        '+',
        ';',
        '.',
        '^',
        '/',
        '(',
        ')',
    ];

    private const TWO_DELIMITER = [
        '<>',
        ':=',
    ];

    private FileReader $fileReader;
    private string $word = '';
    private ?string $char = '';

    /**
     * Scanner constructor.
     * @param FileReader $fileReader
     */
    public function __construct(FileReader $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    public function scan(): \Generator
    {
        $this->char = $this->fileReader->getChar();

        while ($this->char !== null) {
            $this->word = '';

            if (ctype_space($this->char) || $this->char === '\n') {
                $this->char = $this->fileReader->getChar();
                continue;
            }

            switch (true) {
                case ctype_alpha($this->char):
                    yield $this->handleIdentifierState();
                    break;
                case ctype_digit($this->char):
                    yield $this->handleLiteralState();
                    break;
                case in_array($this->char, [':', '<'], true):
                    yield $this->handleTwoDelimiterState();
                    break;
                case in_array($this->char, self::ONE_DELIMITER, true):
                    yield $this->handleOneDelimiterState();
                    break;
                default:
                    $position = $this->fileReader->getCurrentPosition() - strlen($this->word);
                    yield [$this->char, Constants::ERROR_TYPE, "Unexpected lexem found on line {$this->fileReader->getCurrentLine()} and on position $position"];
                    $this->char = $this->fileReader->getChar();
            }
        }
    }

    private function handleIdentifierState(): array
    {
        $isCorrectIdentifier = true;
        do {
            if (!ctype_digit($this->char) && !ctype_alpha($this->char)) {
                $isCorrectIdentifier = false;
            }
            $this->word .= $this->char;
            $this->char = $this->fileReader->getChar();
        } while (!ctype_space($this->char) && !in_array($this->char, self::ONE_DELIMITER) && !in_array($this->char, ['<', ':']));

        if (!$isCorrectIdentifier) {
            $position = $this->fileReader->getCurrentPosition() - strlen($this->word);
            $message = "Unexpected lexem found on line {$this->fileReader->getCurrentLine()} and on position $position";
            return [$this->word, 'error', $message];
        }
        $type = in_array(strtolower($this->word), self::KEYWORDS) ? Constants::KEYWORD : Constants::IDENTIFIER_TYPE;
        return [$this->word, $type, null];
    }

    private function handleLiteralState(): array
    {
        do {
            $this->word .= $this->char;
            $this->char = $this->fileReader->getChar();
        } while (ctype_digit($this->char) || ctype_alpha($this->char));

        $position = $this->fileReader->getCurrentPosition() - strlen($this->word);
        $message = null;
        $type = Constants::LITERAL_TYPE;

        if (!ctype_digit($this->word)) {
            $message = "Unexpected lexem found on line {$this->fileReader->getCurrentLine()} and on position $position";
            $type = Constants::ERROR_TYPE;
        }

        return [$this->word, $type, $message];
    }

    private function handleTwoDelimiterState(): array
    {
        $this->word .= $this->char;
        $this->char = $this->fileReader->getChar();
        $this->word .= $this->char;

        $type = in_array($this->word, self::TWO_DELIMITER) ? Constants::DELIMITER : Constants::ERROR_TYPE;

        if ($type === 'error') {
            $position = $this->fileReader->getCurrentPosition() - strlen($this->word);
            $message = "Unexpected lexem found on line {$this->fileReader->getCurrentLine()} and on position $position";
        }

        $this->char = $this->fileReader->getChar();
        return [$this->word, $type, $message ?? null];
    }

    private function handleOneDelimiterState(): array
    {
        $this->word .= $this->char;
        $this->char = $this->fileReader->getChar();
        return [$this->word, Constants::DELIMITER, null];
    }
}