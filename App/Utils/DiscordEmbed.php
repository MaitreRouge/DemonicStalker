<?php
namespace App\Utils;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class DiscordEmbed {

    /**
     * @var array
     */
    private array $embed = [
        "title"       => null,
        "type"        => "rich",
        "description" => null,
        "url"         => null,
        "timestamp"   => null,
        "color"       => null,
        "footer"      => null,
        "author"      => null,
        "fields"      => null
    ];

    /**
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * DiscordEmbed constructor.
     * @param ClientInterface $httpClient
     */
    public function __construct(
        ClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Permet de définir un titre à l'embed
     * @param string $title
     * @return DiscordEmbed Retourne l'objet modifié
     */
    public function setTitle(string $title): DiscordEmbed
    {
        $this->embed["title"] = $title;
        return $this;
    }

    /**
     * Permet de définir une description à l'embed
     * @param string $description
     * @return DiscordEmbed Retourne l'objet modifié
     */
    public function setDescription(string $description): DiscordEmbed
    {
        $this->embed["description"] = $description;
        return $this;
    }

    /**
     * Permet de définir la couleur
     * @param int $color
     * @return DiscordEmbed
     */
    public function setColor(int $color): DiscordEmbed
    {
        $this->embed["color"] = $color;
        return $this;
    }

    /**
     *
     * @param string $name
     * @param string $value
     * @param bool $inline
     */
    public function addField(string $name, string $value, bool $inline = false): DiscordEmbed
    {
        $this->embed["fields"][] = [
            "name" => $name,
            "value" => $value,
            "inline" => $inline
        ];
        return $this;
    }


    /**
     * @param string $channelId ID du channel dans lequel le bot doit envoyer l'embed
     * @return string|null Retourne l'id du message envoyé si réussi
     */
    public function sendMessage(string $channelId): ?string
    {
        $body = json_encode([
            "content" => "",
            "embed" => $this->embed
        ]);
        $request = new Request("POST", "https://discord.com/api/v8/channels/".$channelId."/messages", [
            "Authorization" => "Bot ".BOT_TOKEN,
            "Content-Type" => "application/json"
        ], $body);
        $response = $this->httpClient->sendRequest($request);
        return json_decode($response->getBody()->getContents())->id;
    }


    /**
     * @param string $channelId ID du channel dans lequel le bot doit remplacer le message
     * @param string $messageId ID du message a remplacer
     * @return string|null Retourne l'id du message modifié si réussi
     */
    public function updadeMessage(string $channelId, string $messageId): ?string
    {
        $body = json_encode([
            "content" => "",
            "embed" => $this->embed
        ]);
        $request = new Request("PATCH", "https://discord.com/api/v8/channels/".$channelId."/messages/".$messageId, [
            "Authorization" => "Bot ".BOT_TOKEN,
            "Content-Type" => "application/json"
        ], $body);
        $response = $this->httpClient->sendRequest($request);
        if($messageId === "830053489275174972") dd(json_decode($response->getBody()->getContents()));
        return json_decode($response->getBody()->getContents())->id;
    }

}