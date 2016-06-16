<?php
namespace IgorRinkovec\CroNLP\DatasetAdapters;

/**
 * Class EloquentDatasetAdapter
 * @package IgorRinkovec\CroNLP\DatasetAdapters
 */
class EloquentDatasetAdapter extends AbstractDatasetAdapter
{

    /**
     * Stores the eloquent model for the word_variation dataset.
     * @var
     */
    protected $WordVariation;

    /**
     * Stores the eloquent model for the word_variation dataset.
     * @var
     */
    protected $WordFrequency;

    /**
     * EloquentDatasetAdapter constructor.
     * @param $WordVariationModel
     * @param $WordFrequencyModel
     */
    public function __construct($WordVariationModel, $WordFrequencyModel)
    {
        $this->WordVariation = new $WordVariationModel();
        $this->WordFrequency = new $WordFrequencyModel();
    }

    /**
     * Returns the amount of documents examined in the creation
     * of the dataset.
     *
     * Check the README for that number in the provided dataset.
     * @return integer
     */
    function getExaminedDocumentCount()
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
    function getLemmaFromForm($wordForm)
    {
        $word = mb_strtolower($wordForm);
        $variation = $this->WordVariation->where('variation', $word)->first();
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
    function getWordFrequency($word)
    {
        $lemma = $this->getLemmaFromForm($word);
        $entry = $this->WordFrequency->where('word', $lemma)->first();
        if($entry === NULL) {
            return 1;
        }
        return $entry->frequency;
    }
}