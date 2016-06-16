<?php
namespace IgorRinkovec\CroNLP\DatasetAdapters;

/**
 * Class AbstractDatasetAdapter
 * @package IgorRinkovec\CroNLP\DatasetAdapters
 */
abstract class AbstractDatasetAdapter
{

    /**
     * Returns the amount of documents examined in the creation
     * of the dataset.
     *
     * Check the README for that number in the provided dataset.
     * @return integer
     */
    abstract function getExaminedDocumentCount();

    /**
     * When given the word in any form it returns its lemma.
     *
     * This can be retrieved from the WordVariations table
     * from the provided dataset.
     * @param $wordForm
     * @return string
     */
    abstract function getLemmaFromForm($wordForm);

    /**
     * Returns the number of times this word has appeared
     * in the examined document set.
     *
     * This can be retrieved from the WordFrequency table
     * from the provided dataset.
     * @param $word
     * @return integer
     */
    abstract function getWordFrequency($word);
}