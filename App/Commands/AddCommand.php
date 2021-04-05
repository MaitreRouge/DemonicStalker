<?php
namespace App\Commands;

use App\Database\Tables\PacketsTable;
use SlashCommands\Utils\Commands\Interaction;
use SlashCommands\Utils\Commands\InteractionApplicationCommandCallbackData;
use SlashCommands\Utils\Commands\InteractionResponse;
use SlashCommands\Utils\Commands\Responses\JsonResponse;

class AddCommand {

    /**
     * @var PacketsTable
     */
    private PacketsTable $packetsTable;

    public function __construct(
        PacketsTable $packetsTable
    ) {
        $this->packetsTable = $packetsTable;
    }


    public function add(Interaction $interaction): InteractionResponse
    {
        $packetId = $interaction->data->options->debugOptions[0]->value;
        $name = $interaction->data->options->debugOptions[1]->value??null;

        if ($this->packetsTable->exists($packetId)) return new InteractionResponse(
            InteractionResponse::TYPE_MESSAGE_WITH_SOURCE,
                new InteractionApplicationCommandCallbackData("Ce packet est déjà enregstré dans la base données")
        );

        $v = $this->packetsTable->insert([
            "packetName" => $name,
            "packetId" => $packetId
        ]);

        return new InteractionResponse(
            InteractionResponse::TYPE_MESSAGE_WITH_SOURCE,
            new InteractionApplicationCommandCallbackData("Le packet avec le numéro de suivi ".$packetId." à bien été enregistré")
        );
    }

}
