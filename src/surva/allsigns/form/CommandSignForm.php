<?php

/**
 * AllSigns | create/edit command sign form
 */

namespace surva\allsigns\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use surva\allsigns\AllSigns;
use surva\allsigns\sign\CommandSign;
use surva\allsigns\util\ExecutionContext;

class CommandSignForm implements Form
{
    private AllSigns $allSigns;

    private CommandSign $sign;

    private string $type = "custom_form";

    private string $title;

    private array $content;

    /**
     * @param  \surva\allsigns\AllSigns  $allSigns
     * @param  \surva\allsigns\sign\CommandSign  $commandSign
     */
    public function __construct(AllSigns $allSigns, CommandSign $commandSign)
    {
        $this->allSigns = $allSigns;
        $this->sign     = $commandSign;

        $existingData = $this->sign->getData();

        $this->title   = $allSigns->getMessage("form.commandsign.title");
        $this->content = [
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.commandsign.command"),
            "default" => $existingData !== null ? $existingData["settings"]["command"] : "",
          ],
          [
            "type"    => "dropdown",
            "text"    => $allSigns->getMessage("form.commandsign.context"),
            "options" => [
              $allSigns->getMessage("form.commandsign.player"),
              $allSigns->getMessage("form.commandsign.server"),
            ],
            "default" => $existingData !== null ? $existingData["settings"]["context"]
              : ExecutionContext::CONTEXT_PLAYER,
          ],
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.commandsign.text"),
            "default" => $existingData !== null ? $existingData["settings"]["text"] : "",
          ],
          [
            "type"    => "input",
            "text"    => $allSigns->getMessage("form.commandsign.permission"),
            "default" => $existingData !== null ? $existingData["settings"]["permission"] : "",
          ],
        ];
    }

    /**
     * Getting a response from the client form
     *
     * @param  \pocketmine\player\Player  $player
     * @param  mixed  $data
     */
    public function handleResponse(Player $player, $data): void
    {
        if (!is_array($data)) {
            return;
        }

        if (count($data) !== 4) {
            return;
        }

        $signData = [
          "command" => $data[0],
          "context" => $data[1],
        ];

        $text       = $data[2];
        $permission = $data[3];

        if ($this->sign->createSign($signData, $text, $permission)) {
            $player->sendMessage($this->allSigns->getMessage("form.commandsign.success"));
        } else {
            $player->sendMessage($this->allSigns->getMessage("form.commandsign.error"));
        }
    }

    /**
     * Return JSON data of the form
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
          "type"    => $this->type,
          "title"   => $this->title,
          "content" => $this->content,
        ];
    }
}
