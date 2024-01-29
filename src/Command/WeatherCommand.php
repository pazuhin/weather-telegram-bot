<?php

namespace App\Command;

use App\DTO\WeatherDTO;
use App\Service\WeatherService;
use App\Storage\Storage;
use BoShurik\TelegramBotBundle\Telegram\Command\AbstractCommand;
use BoShurik\TelegramBotBundle\Telegram\Command\PublicCommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

class WeatherCommand extends AbstractCommand implements PublicCommandInterface
{

    public function __construct
    (
        private Storage $storage,
        private WeatherService $weatherService
    ) { }

    public function getName(): string
    {
        return '/weather';
    }

    public function getDescription(): string
    {
        return 'Weather command';
    }

    protected function step0(BotApi $api, Update $update, string $chatId): bool
    {
        $api->sendMessage($chatId, 'Укажите город, в котором хотите узнать прогноз погоды');

        return true;
    }

    protected function step1(BotApi $api, Update $update, string $chatId): bool
    {
        $response = $this->weatherService->getCurrentWeatherByCity($update->getMessage()->getText());
        $city = $update->getMessage()->getText();
        $api->sendMessage($chatId, $this->weatherService->makeWeatherHtml(new WeatherDTO($response), $city), 'MarkdownV2');

        return true;
    }

    public function execute(BotApi $api, Update $update): void
    {
        $id = (string) $update->getMessage()->getChat()->getId();
        if (parent::isApplicable($update)) {
            $step = 0;
        } else {
            $step = $this->storage->getCurrentStep($id);
        }

        $method = sprintf('step%d', $step);
        $nextMethod = sprintf('step%d', $step + 1);

        $result = $this->$method($api, $update, $id);
        if (!$result) {
            return;
        }

        if (method_exists($this, $nextMethod)) {
            $this->storage->setCurrentStep($id, $step + 1);
        } else {
            $this->storage->clearData($id);
        }
    }

    public function isApplicable(Update $update): bool
    {
        if (parent::isApplicable($update)) {
            return true;
        }
        if (!$update->getMessage()) {
            return false;
        }

        return $this->storage->hasData((string) $update->getMessage()->getChat()->getId());
    }
}