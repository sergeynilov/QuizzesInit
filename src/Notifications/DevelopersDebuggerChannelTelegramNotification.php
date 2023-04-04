<?php

namespace sergeynilov\QuizzesInit\Notifications;

use Illuminate\Notifications\Notification;

use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use sergeynilov\QuizzesInit\Library\Facades\QuizzesInitFacade;

class DevelopersDebuggerChannelTelegramNotification extends Notification
{
    private string $errorMsg;
    private string $exceptionClass;
    private string $file;
    private int $line;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        string $errorMsg,
        string $exceptionClass,
        string $file,
        int $line
    ) {
        $this->errorMsg       = $errorMsg;
        $this->exceptionClass = $exceptionClass;
        $this->file           = $file;
        $this->line           = $line;
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        $contentText    = 'Error message " ' . $this->errorMsg . '" with "' . $this->exceptionClass .
                          '" exception, in file "' . $this->file . '" at line "' . $this->line . "'";
        $telegramBotApi = config('services.telegram-bot-api');
        $telegramChatId = $telegramBotApi['developers-debugger-channel-user-id'] ?? null;

        /*     'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'developers-debugger-channel-user-id' => env('TELEGRAM_DEVELOPERS_DEBUGGER_CHANNEL_USER_ID'),
    ],
 */
        return TelegramMessage::create()
            ->to($telegramChatId)
            ->content($contentText . ' => ' . QuizzesInitFacade::getOSInfo(false));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
