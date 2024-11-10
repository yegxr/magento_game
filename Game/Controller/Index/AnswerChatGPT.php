<?php

namespace Perspective\Game\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
class AnswerChatGPT extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ){
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute(): mixed
    {
        $result = $this->resultJsonFactory->create();

        try {
            $body = $this->getRequest()->getContent();
            $request = json_decode($body, true);
            if (!isset($request['game_state']) || !isset($request['move'])) {
                throw new \InvalidArgumentException('Недостаточно данных в запросе');
            }

            $gameState = $request['game_state'];
            $userMove = $request['move'];
            $response = $this->callChatGPT($gameState, $userMove);

            return $result->setData(['game_state' => $response]);
        } catch (\Exception $e) {
            return $result->setData(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param $gameState
     * @param $userMove
     * @return mixed
     */
    protected function callChatGPT($gameState, $userMove): mixed
    {
        $apiKey = 'INPUT GPT KEY';
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $prompt = $this->generatePrompt($gameState, $userMove);

        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are the second player in a game of tic-tac-toe. You will be sent an array, you have to make a move (replace "null" with "0"), Dont write anything else'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 100,
            'temperature' => 0.5,
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);
        $decodeResponse = json_decode($response, true);
        $answer = json_decode($decodeResponse['choices'][0]['message']['content'], true);
        return $answer;
    }

    /**
     * @param $gameState
     * @param $userMove
     * @return string
     */
    protected function generatePrompt($gameState, $userMove): string
    {
        $gameStateStr = json_encode($gameState);
        $userMoveStr = json_encode($userMove);

        return "Play tic-tac-toe. Current game state data: {$gameStateStr}. " .
            "Player made a move: {$userMoveStr}. What will be your move?";
    }
}
