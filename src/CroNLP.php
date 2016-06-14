<?php
namespace IgorRinkovec\CroNLP;

use IgorRinkovec\CroNLP\DatasetAdapters\AbstractDatasetAdapter;
use IgorRinkovec\CroNLP\Services\TextProcessorService;

class CroNLP
{
    /**
     * Stores the dataset adapter used for interacting with the dataset.
     * @var AbstractDatasetAdapter
     */
    protected $datasetAdapter = null;

    /**
     * Stores the TextProcessorService object with the processed text.
     * @var TextProcessorService
     */
    protected $textProcessorService = null;

    /**
     * Contains the MD5 hash of the currently processed text.
     * @var string
     */
    protected $currentlyProcessedTextHash = null;

    /**
     * CroNLP constructor.
     * @param AbstractDatasetAdapter $adapter
     */
    public function __construct(AbstractDatasetAdapter $adapter)
    {
        $this->datasetAdapter = $adapter;
    }

    /**
     * Processes the given text to prepare it for fetching metadata unless
     * the metadata for the given text is already calculated.
     * @param $text
     */
    protected function processText($text)
    {
        if($this->currentlyProcessedTextHash !== md5($text)) {
            $this->currentlyProcessedTextHash = md5($text);
            $this->textProcessorService = new TextProcessorService($this->datasetAdapter);
            $this->textProcessorService->processContent($text);
        }
    }

    /**
     * Extracts the keywords used in the text.
     * @param $text
     * @param int $amount
     * @return array
     */
    public function extractKeywords($text, $amount = 10)
    {
        $this->processText($text);
        return $this->textProcessorService->getKeywords($amount);
    }

    /**
     * Summarizes the text to a certain percentage of the original.
     * @param $text
     * @param int $percentageToCondense
     * @return string
     */
    public function summarize($text, $percentageToCondense = 70)
    {
        $this->processText($text);
        return $this->textProcessorService->getContentDigest($percentageToCondense);
    }

}