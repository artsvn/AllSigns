<?php

/**
 * AllSigns | translated messages utils
 */

namespace surva\allsigns\util;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use surva\allsigns\AllSigns;

class Messages
{
    private AllSigns $allSigns;

    private ?CommandSender $sender;

    public function __construct(AllSigns $allSigns, ?CommandSender $sender = null)
    {
        $this->allSigns = $allSigns;
        $this->sender = $sender;
    }

    /**
     * Get a translated message
     *
     * @param  string  $key
     * @param  array  $replaces
     *
     * @return string
     */
    public function getMessage(string $key, array $replaces = []): string
    {
        $prefLangId = null;

        if ($this->sender instanceof Player && $this->allSigns->getConfig()->get("autodetectlanguage", true)) {
            preg_match("/^[a-z][a-z]/", $this->sender->getLocale(), $localeRes);

            if (isset($localeRes[0])) {
                $prefLangId = $localeRes[0];
            }
        }

        $defaultLangId = $this->allSigns->getConfig()->get("language", "en");

        if ($prefLangId !== null && isset($this->allSigns->getTranslationMessages()[$prefLangId])) {
            $langConfig = $this->allSigns->getTranslationMessages()[$prefLangId];
        } else {
            $langConfig = $this->allSigns->getTranslationMessages()[$defaultLangId];
        }

        $rawMessage = $langConfig->getNested($key);

        if ($rawMessage === null || $rawMessage === "") {
            $rawMessage = $this->allSigns->getDefaultMessages()->getNested($key);
        }

        if ($rawMessage === null) {
            return $key;
        }

        foreach ($replaces as $replace => $value) {
            $rawMessage = str_replace("{" . $replace . "}", $value, $rawMessage);
        }

        return $rawMessage;
    }
}
