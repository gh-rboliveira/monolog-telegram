<?php
namespace rahimi\TelegramHandler;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Telegram Handler For Monolog
 *
 * This class helps you in logging your application events
 * into telegram using it's API.
 *
 */

class TelegramHandler extends AbstractProcessingHandler
{

    private $token;
    private $recipients = [];
    private $dateFormat = 'F j, Y, g:i a';
    const host = 'https://api.telegram.org/bot';

    /**
     * create Telegram Handler Object.
     *
     * @param int $level - The minimum logging level at which this handler will be triggered
     * @param string $bubble - Whether the messages that are handled can bubble up the stack or not
	 *
     */

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
		parent::__construct($level, $bubble);
        if (!extension_loaded('curl')) {
            throw new Exception('curl is needed to use this library');
        }
    }
	
	/**
	 * set bot token
	 *
	 * @param string $token
	 * @return void
	 */
	public function setBotToken(string $token) {
		$this->token = $token;
	}
	
	/**
	 * set message recipients
	 *
	 * @param array $recipients
	 * @return void
	 */
	public function setRecipients(array $recipients) {
		$this->recipients = $recipients;
	}
	
	/**
	 * set date format to be used on message
	 * 
	 * @param string $dateFormat
	 * @param string $timezone_abbreviations_list
	 * @return void
	 */
	public function setDateFormat(string $dateFormat, string $timeZone = 'UTC') {
		$this->dateFormat = $dateFormat;
        date_default_timezone_set($timeZone);
	}

    /**
     * format the log to send
     * @param $record[] log data
     * @return void
     */
    public function write(array $record)
    {
        $format = new LineFormatter;
        $context = $record['context'] ? $format->stringify($record['context']) : '';
        $date =  date($this->dateFormat);
        $message = $date . PHP_EOL . $this->getEmoji($record['level']) . ' ' . $record['message'] . $context;
        $this->send($message);

    }

    /**
     *    send log to telegram channel
     *    @param string $message Text Message
     *    @return void
     *
     */
    public function send($message)
    {
		
      try{
		  
		if (!isset($this->token)) {
			throw new Exception('No token added. No bot!');
		}
		if (!count($this->recipients)) {
			throw new Exception('No recipients added. No one to send the message!');
		}
		
        $ch = curl_init();
        $url = self::host . $this->token . "/SendMessage";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        foreach ($this->recipients as $recipient) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'text'    => $message,
            'chat_id' => $recipient,
        )));
		}
         $result = curl_exec($ch);
         $result = json_decode($result,1);
         if($result['ok'] === false){
            echo  'telegram api response : ' .$result['description'];
         }
       }catch(Exception $error){
         echo $error;
       }
    }

    /**
     * make emoji for log events
     * @return array
     *
     */
    protected function emojiMap()
    {
        return [
            Logger::DEBUG     => '🚧',
            Logger::INFO      => '‍🗨',
            Logger::NOTICE    => '🕵',
            Logger::WARNING   => '⚡️',
            Logger::ERROR     => '🚨',
            Logger::CRITICAL  => '🤒',
            Logger::ALERT     => '👀',
            Logger::EMERGENCY => '🤕',
        ];
    }

    /**
     * return emoji for given level
     *
     * @param $level
     * @return string
     */
    protected function getEmoji($level)
    {
        $levelEmojiMap = $this->emojiMap();
        return $levelEmojiMap[$level];
    }

}
