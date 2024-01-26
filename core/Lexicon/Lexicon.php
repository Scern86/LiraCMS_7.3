<?php

namespace Scern\Lira\Lexicon;

use Scern\Lira\Lexicon\Lang;

class Lexicon
{
    public Lang $currentLang;

    public function __construct(public readonly Lang $defaultLang, public readonly array $languagesList, protected array $lexicon=[])
    {
        $this->currentLang = $this->defaultLang;
    }

    public function get(string $key, string $defaultValue='', ?string $langCode = null): string
    {
        if (is_null($langCode)) $langCode = $this->currentLang->code;
        return $this->lexicon[$langCode][$key] ?? $defaultValue;
    }

    public function load(array $lexicon): void
    {
        $this->lexicon = array_merge($this->lexicon,$lexicon);
    }
}