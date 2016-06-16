<?php
namespace IgorRinkovec\CroNLP\Services;

use IgorRinkovec\CroNLP\DatasetAdapters\AbstractDatasetAdapter;

/**
 * Class TfidfService
 * @package IgorRinkovec\CroNLP\Services
 */
class TfidfService
{

    /**
     * Stores the dataset adapter used for interacting with the dataset.
     * @var AbstractDatasetAdapter
     */
    protected $datasetAdapter = null;

    /**
     * TFIDFService constructor.
     * @param AbstractDatasetAdapter $adapter
     */
    public function __construct(AbstractDatasetAdapter $adapter)
    {
        $this->datasetAdapter = $adapter;
    }

    /**
     * Returns IDF for the given word form.
     * @param $wordForm
     * @return float
     */
    public function getInverseDocumentFrequency($wordForm)
    {
        $frequency = $this->datasetAdapter->getWordFrequency($wordForm);
        return log($this->datasetAdapter->getExaminedDocumentCount() / $frequency);
    }
}