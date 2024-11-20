<?php

namespace Perspective\Game\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

class LogError extends Action
{
    protected LoggerInterface $logger;

    public function __construct(
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    public function execute()
    {
        $data = $this->getRequest()->getContent();
        $errorData = json_decode($data, true);
        if ($errorData) {
            $logFilePath = BP . '/var/log/tic-tac-exceptions.log';
            file_put_contents($logFilePath, $errorData['timestamp'] . ' ' . $errorData['error'] . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData(['status' => 'success']);
    }
}
