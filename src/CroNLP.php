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
     * CroNLP constructor.
     * @param AbstractDatasetAdapter $adapter
     */
    public function __construct(AbstractDatasetAdapter $adapter)
    {
        $this->datasetAdapter = $adapter;
    }

    /**
     * Extracts the keywords used in the text.
     * @param $text
     * @param int $amount
     * @return array
     */
    public function extractKeywords($text, $amount = 10)
    {
        $tps = new TextProcessorService($this->datasetAdapter);
        $tps->processContent($text);
        return $tps->getKeywords($amount);
    }

    /**
     * Summarizes the text to a certain percentage of the original.
     * @param $text
     * @param int $percentageToCondense
     * @return string
     */
    public function summarize($text, $percentageToCondense = 70)
    {
        $tps = new TextProcessorService($this->datasetAdapter);
        $tps->processContent($text);
        return $tps->getContentDigest($percentageToCondense);
    }

}