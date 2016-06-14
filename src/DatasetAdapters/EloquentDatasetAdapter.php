<?php
namespace IgorRinkovec\CroNLP\DatasetAdapters;

use App\WordFrequency;
use App\WordVariation;

/**
 * Class EloquentDatasetAdapter
 * @package IgorRinkovec\CroNLP\DatasetAdapters
 */
class EloquentDatasetAdapter extends AbstractDatasetAdapter
{
    /**
     * Returns the amount of documents examined in the creation
     * of the dataset.
     *
     * Check the README for that number in the provided dataset.
     * @return integer
     */
    static function getExaminedDocumentCount()
    {
        return 706134;
    }

    /**
     * When given the word in any form it returns its lemma.
     *
     * This can be retrieved from the WordVariations table
     * from the provided dataset.
     * @param $wordForm
     * @return string
     */
    static function getLemmaFromForm($wordForm)
    {
        $word = mb_strtolower($wordForm);
        $variation = WordVariation::where('variation', $word)->first();
        if($variation === NULL) {
            return $word;
        }
        return $variation->word;
    }

    /**
     * Returns the number of times this word has appeared
     * in the examined document set.
     *
     * This can be retrieved from the WordFrequency table
     * from the provided dataset.
     * @param $word
     * @return integer
     */
    static function getWordFrequency($word)
    {
        $lemma = self::getLemmaFromForm($word);
        $entry = WordFrequency::where('word', $lemma)->first();
        if($entry === NULL) {
            return 1;
        }
        return $entry->frequency;
    }
}