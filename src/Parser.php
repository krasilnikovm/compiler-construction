<?php declare(strict_types=1);

namespace Krasilnikovm\Compiler;

/**
 * Class Parser
 *
 * @author Mihail Krasilnikov <mihail.krasilnikov.j@gmail.com>
 */
final class Parser
{
    private array $rules;
    private ?string $token = '';
    private ?string $type = '';
    private array $tokens;

    public function __construct(Scanner $scanner, string $configPath)
    {
        $this->tokens = iterator_to_array($scanner->scan());
        $this->rules = include $configPath;
    }

    public function parse(): bool
    {
        $key = array_key_first($this->rules);

        while ($this->getNextToken()) {
            $ruleProcessed = $this->processRules($this->rules[$key]);

            if (!$ruleProcessed) {
                return false;
            }
        }

        return true;
    }

    //[
    //   ['<if_statement>'],
    //   ['<assign_statement>'],
    // ],
    //
    private function processRules(array $rules):bool
    {
        foreach ($rules as $rule) {
            $ruleApplied = $this->applyRule($rule);

            if ($ruleApplied) {
                return true;
            }
        }

        return false;
    }

    // ['if', '<condition>', 'then', '<statement>']
    private function applyRule(array $rule): bool
    {
        foreach ($rule as $key => $element) {
            switch (true) {
                case $this->isSpecialNonTerminal($element):
                    if (!$this->isCorrectTypeProvided($element)) {
                        $this->revert((int)$key);
                        return false;
                    }

                    $this->getNextToken();
                    break;
                case $this->isNonterminal($element):
                    if (!$this->processRules($this->rules[$element])) {
                        return false;
                    }
                    break;
                case $this->token !== $element:
                    $this->revert((int)$key);
                    return false;
                default: $this->getNextToken();
            }
        }

        return true;
    }

    private function isNonterminal(string $element): bool
    {
        return $element !== '<>' && (bool)\preg_match('/^<.*>/', $element);
    }

    private function isSpecialNonTerminal(string $element): bool
    {
        return (bool)\preg_match('/^<!.*!>/', $element);
    }

    private function getNextToken(): bool
    {
        $token = current($this->tokens);
        next($this->tokens);

        if ((bool)$token === false) {
            return false;
        }

        [$token, $type] = $token;
        $this->token = $token;
        $this->type = $type;

        return true;
    }

    private function revert(int $counter): bool
    {
        if ($counter >= 1) {
            $counter++;
        }

        $token = null;
        for ($i = 0; $i < $counter; $i++) {
            prev($this->tokens);
            $token = current($this->tokens);
        }
        if ($counter !== 0) {
            next($this->tokens);
        }

        if ((bool)$token === false) {
            return false;
        }

        [$tokenRes, $type] = $token;
        $this->token = $tokenRes;
        $this->type = $type;

        return true;
    }

    private function isCorrectTypeProvided(string $element): bool
    {
        switch ($element){
            case '<!identifier!>':
                $valid = $this->type === Constants::IDENTIFIER_TYPE;
                break;
            case '<!number!>':
                $valid = $this->type === Constants::LITERAL_TYPE && is_numeric($this->token);
                break;
            default: $valid = false;
        }

        return $valid;
    }
}