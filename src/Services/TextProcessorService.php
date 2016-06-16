<?php
namespace IgorRinkovec\CroNLP\Services;

use IgorRinkovec\CroNLP\DatasetAdapters\AbstractDatasetAdapter;

/**
 * Class TextProcessorService
 * @package IgorRinkovec\CroNLP\Services
 */
class TextProcessorService {

    /**
     * Stores the dataset adapter used for interacting with the dataset.
     * @var AbstractDatasetAdapter
     */
    protected $datasetAdapter = null;

    /**
     * Stores the service used for TFIDF calculations.
     * @var TFIDFService
     */
    protected $tfidfService = null;

    /**
     * Stores the content that needs to be summarized
     * @var
     */
    protected $content;

    /**
     * Stores the content split into sencences
     * @var array
     */
    protected $sentences;

    /**
     * Stores the length and weight for each sencence
     * @var array
     */
    protected $sentenceMetadata;

    /**
     * Stores the maximum weight a sencence has
     * @var int
     */
    protected $maxSentenceWeight = 0;

    /**
     * Stores weights for all words
     * @var array
     */
    protected $wordWeight;


    /**
     * TextProcessorService constructor.
     * @param AbstractDatasetAdapter $adapter
     */
    public function __construct(AbstractDatasetAdapter $adapter)
    {
        $this->datasetAdapter = $adapter;
        $this->tfidfService = new TfidfService($adapter);
    }

    /**
     * @param $content
     */
    public function processContent($content)
    {
        $this->content = $content;
        $this->wordWeight = $this->generateWordWeights($content);
        $this->sentences = $this->splitContentInSentences($this->content);
        // Remove duplicates
        $this->sentences = array_intersect_key(
            $this->sentences,
            array_unique(array_map("StrToLower",$this->sentences))
        );
        $this->sentenceMetadata = $this->calculateSentenceMetadata($this->sentences);
    }

    /**
     * Splits the given string into lowercase words without punctuation
     * @param $content
     * @return array
     */
    protected function splitContentInWords($content)
    {
        $words = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $content, -1, PREG_SPLIT_NO_EMPTY);
        foreach($words as &$word) {
            $word = mb_strtolower($word);
        }
        return $words;
    }

    /**
     * Splits the content into sentances
     * @param $content
     * @return array
     */
    protected function splitContentInSentences($content)
    {
        $sss = new SentenceSplitterService();
        return $sss->split($content);
    }

    // TF-IDF Algorithm - http://www.tfidf.com/

    /**
     * Calculates the and returnes the TF attribute for each word
     * @param $wordArray
     * @return array
     */
    protected function calculateTermFrequency($wordArray)
    {
        $originalToNormalizedMap = [];
        $occurenceCountByNormalizedWord = [];

        foreach($wordArray as $word) {
            $normalized = $this->datasetAdapter->getLemmaFromForm($word);
            $originalToNormalizedMap[$word] = $normalized;
            if(isset($occurenceCountByNormalizedWord[$normalized])) {
                $occurenceCountByNormalizedWord[$normalized]++;
            }
            else {
                $occurenceCountByNormalizedWord[$normalized] = 1;
            }
        }

        $wordCount = count($wordArray);
        $termFrequency = [];
        foreach($originalToNormalizedMap as $original => $normalized) {
            $termFrequency[$original] = $occurenceCountByNormalizedWord[$normalized] / $wordCount;
        }

        return $termFrequency;
    }

    /**
     * Calculates and returns the IDF value for each word
     * @param $wordArray
     * @return array
     */
    protected function calculateInverseDocumentFrequency($wordArray)
    {
        $idf = [];
        foreach($wordArray as $word) {
            $idf[$word] = $this->tfidfService->getInverseDocumentFrequency($word);
        }
        return $idf;
    }

    /**
     * Generates the TF-IDF weight for every word in the text
     * @param $content
     * @return array
     */
    protected function generateWordWeights($content)
    {
        $words = $this->splitContentInWords($content);
        $termFrequency = $this->calculateTermFrequency($words);
        $inverseDocumentFrequency = $this->calculateInverseDocumentFrequency($words);

        $weight = [];
        foreach($words as $word) {
            $weight[$word] = $termFrequency[$word] * $inverseDocumentFrequency[$word];
        }
        return $weight;
    }

    /**
     * Calculates the length and weight sum for each sentence
     * @param $sentences
     * @return array
     */
    protected function calculateSentenceMetadata($sentences)
    {
        $metadata = [];
        $sentenceCount = count($sentences);
        foreach($sentences as $index => $sentence) {
            $metadata[$index]['count'] = strlen($sentence);

            $words = $this->splitContentInWords($sentence);
            $metadata[$index]['weight'] = 0;
            foreach($words as $word) {
                $metadata[$index]['weight'] += $this->wordWeight[$word];
            }
            $count = count($words);
            if($count == 0) $count = 1;
            $metadata[$index]['weight'] = $metadata[$index]['weight'] / $count;
            $metadata[$index]['weight'] += (0.1 * $metadata[$index]['weight']) * ($sentenceCount/($index+1));
            //$metadata[$index]['weight'] = $metadata[$index]['weight'];

            if($metadata[$index]['weight'] > $this->maxSentenceWeight) {
                $this->maxSentenceWeight = $metadata[$index]['weight'];
            }
        }

        return $metadata;
    }

    /**
     * Tries to find the best weight treshold for the given target digest length in characters
     * @param $targetLength
     * @return int
     */
    protected function getOptimalWeightTreshold($targetLength)
    {
        $sortedByWeight = $this->sentenceMetadata;
        $sorter = function($sentenceA, $sentenceB) {
            if($sentenceA['weight'] < $sentenceB['weight'])
                return 1;
            else if($sentenceA['weight'] > $sentenceB['weight'])
                return -1;
            return 0;
        };
        uasort($sortedByWeight, $sorter);

        $currentLength = 0;
        foreach($sortedByWeight as $sentence) {
            $currentLength += $sentence['count'];
            if($currentLength > $targetLength) {
                return $sentence['weight'];
            }
        }
        return 0;
    }

    /**
     * Returns the digested content dr
     * @param int $digestPercentage
     * @return string
     */
    public function getContentDigest($digestPercentage = 70)
    {
        $targetLength = (1 - ($digestPercentage/100)) * strlen($this->content);
        $treshold = $this->getOptimalWeightTreshold($targetLength);

        $digestSentences = [];
        foreach($this->sentenceMetadata as $index => $metadata) {
            if($metadata['weight'] > $treshold) {
                $digestSentences[] = $this->sentences[$index];
            }
        }

        return implode(' ', $digestSentences);
    }

    /**
     * Returns the top ranked words
     * @param int $amount
     * @return array
     */
    public function getKeywords($amount = 10) {
        // Map words to their normalized version
        $originalToNormalizedMap = [];
        $isNormalizedWordIncluded = [];
        foreach($this->wordWeight as $word => $weight) {
            $normalized = $this->datasetAdapter->getLemmaFromForm($word);
            $originalToNormalizedMap[$word] = $normalized;
            $isNormalizedWordIncluded[$normalized] = false;
        }

        // Sort them by weight
        $keywords = $this->wordWeight;
        arsort($keywords);
        $keywords = array_keys($keywords);
        $keywords = array_filter($keywords, function($content) {
            $include = true;
            if(is_numeric($content)) $include = false;
            $contentFiltered = preg_replace("/[^a-zA-Z0-9]+/", "", $content);
            if(strlen($contentFiltered) < 3) $include = false;
            return $include;
        });

        // Remove keywords that are already included in a different format
        foreach($keywords as $index => $keyword) {
            if($isNormalizedWordIncluded[$originalToNormalizedMap[$keyword]] == false) {
                $isNormalizedWordIncluded[$originalToNormalizedMap[$keyword]] = true;
            }
            else {
                unset($keywords[$index]);
            }
        }
        $keywords = array_slice($keywords, 0, $amount);
        return $keywords;
    }

}