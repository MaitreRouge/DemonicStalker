<?php
namespace App\Controllers;

use App\Database\Tables\PacketsTable;
use App\Utils\DiscordEmbed;
use GuzzleHttp\Psr7\Request;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestInterface;
use function DI\add;

class ParcelController {

    /**
     * @var PacketsTable
     */
    private PacketsTable $packetsTable;
    /**
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    public function __construct(
        PacketsTable $packetsTable,
        ClientInterface $httpClient
    )
    {
        $this->packetsTable = $packetsTable;
        $this->httpClient = $httpClient;
    }


    public function traceAllPackets(ServerRequestInterface $request)
    {
        if($request->getQueryParams()['key'] !== API_KEY) return new RedirectResponse("/");

        $packets = $this->packetsTable->findAll();

        foreach ($packets as $packet){

            $embed = (new DiscordEmbed($this->httpClient))
                ->setTitle("Suivi du colis " . ($packet->packetName !== null ? $packet->packetName : $packet->packetId));

            switch ($packet->deliveryService){
                case "LaPoste":
                    $request = new Request("GET", "https://api.laposte.fr/suivi/v2/idships/".$packet->packetId, [
                        "Accept" => "application/json",
                        "X-Okapi-Key" => "YWuInrZm3+aS1PjJ4JEdRmJXevjCk3H4PjSD/5+SmzmFiASv32Tm9By5pBQogcj0"
                    ]);
                    $response = $this->httpClient->sendRequest($request);

                    if($response->getStatusCode() === 400) {
                        $this->packetsTable->delete($packet->packetId);
                        $this->sendDiscordMessage("<@".$packet->userId.">, le colis avec le numéro de suivi ``".$packet->packetId."`` du transporteur ``La Poste`` est invalide et en conséquent a été supprimé de la base de donnée");
                        break;
                    } else if($response->getStatusCode() === 429 or $response->getStatusCode() === 503 or $response->getStatusCode() === 504) {
                        break;
                    } else if($response->getStatusCode() !== 200) {
                        $this->sendDiscordMessage("<@".$packet->userId."> une erreur inconnue (".$response->getStatusCode().") est survenue avec le colis ``".$packet->packetId."``");
                        break;
                    }

                    $content = json_decode($response->getBody()->getContents())->shipment;
                    $timeline = $content->timeline;
                    $events = $content->event;
                    $date = new \DateTime($content->entryDate);
                    $eta = "---";
                    $lastUpdate = $timeline[$content->holder];

                    if(!$content->isFinal and $content->holder === 4){
                        if(isset($timeline[4]->date)) {
                            $eta = (new \DateTime($timeline[4]->date))->format("d/m/Y");
                        } else {
                            $eta = "---";
                        }
                        foreach ($timeline as $item){
                            if($item->status){
                                $lastUpdate = $item;
                            }
                        }
                    }

                    $embed->addField("Numéro du colis", $packet->packetId, true);
                    $embed->addField("Transporteur", "LaPoste", true);
                    $embed->addField("Etat du colis", ($content->isFinal ? "Livré" : "En transit"), true);
                    $embed->addField("Derniere mise a jour", $lastUpdate->shortLabel, false);
                    $embed->addField("Dernier event", $events[0]->label, false);
                    $embed->addField("Colis pris en charge le", $date->format("d/m/Y"), true);
                    $embed->addField((isset($content->deliveryDate) ? "Colis livré le" : "Date de livraison estimée"), (isset($content->deliveryDate) ? (new \DateTime($content->deliveryDate))->format("d/m/Y") : (string)$eta), true);

                    switch ($content->holder){
                        case 1:
                            $embed->setColor(15944722);
                            break;
                        case 2:
                        case 3:
                            $embed->setColor(15963154);
                            break;
                        case 4:
                            if($content->isFinal) $embed->setColor(2224914);
                            else $embed->setColor(15661067);
                            break;
                        default:
                            $embed->setColor(12587763);
                            break;
                    }

                    if($packet->messageId === null) {
                        $mid = $embed->sendMessage(CHANNEL_ID);
                        $this->packetsTable->update($packet->packetId, [
                            "messageId" => $mid
                        ]);

                        break;
                    } else {

                    $mid = $embed->updadeMessage(CHANNEL_ID, $packet->messageId);
                    $this->packetsTable->update($packet->packetId, [
                        "messageId" => $mid
                    ]);

                    break;
                    }
            }

        }

        die('everything went fine (i think)');
    }

    private function sendDiscordMessage(string $message): string
    {
        $body = json_encode([
            "content" => $message,
            "tts" => false
        ]);
        $request = new Request("POST", "https://discord.com/api/v8/channels/".CHANNEL_ID."/messages", [
            "Authorization" => "Bot ".BOT_TOKEN,
            "Content-Type" => "application/json"
        ], $body);
        $response = $this->httpClient->sendRequest($request);
        return json_decode($response->getBody()->getContents())->id;
    }

}