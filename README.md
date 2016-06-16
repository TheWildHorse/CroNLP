#CroNLP

[![Build Status](https://travis-ci.org/TheWildHorse/CroNLP.svg?branch=master)](https://travis-ci.org/TheWildHorse/CroNLP)

-------------------

CroNLP is a package used for extracting metadata from Croatian text. Currently it supports basic keyword extraction and summarization implemented using the TF-IDF algorithm.

&#x1F535; *This package is stable, but the algorithm may produce subpar results since it is still in development stages. Feel free to submit a merge request if you can improve upon any part ot the package. :)* &#x1F535;

[Test application to check the algorithm results.](http://188.226.244.156/)

## Installation
Installation is a two step process, and it does involve a bit of implementation on your side, but nothing complex.

### Composer
As with any other composer package installation can be done by either running  
`composer require thewildhorse\cronlp`  
or by including the package in your composer.json file.

```
"require": { 
	... 
    "thewildhorse/cronlp": "dev-master"   
}
    
```

### Dataset Adapter
The key part of this package is the dataset, without it the script can not function at all. This dataset can be found in `vendor/thewildhorse/cronlp/data` in a form of two database export files. Those two exports need to be imported into your database of choice. (or a caching engine if you need blazing fast performance)

The dataset contains two tables:  

- **word_variations** - Contains a map of Croatian word terms linked to their lemmas. (thanks to FFZG)
- **word_frequency** - Document frequency map containing a amount of documents a lemma has been mentioned in. For the supplied datased we used 706134 Croatian online news articles. This table is a document frequency table in the terms of TFIDF algorithm.

CroNLP utilizes a Dataset Adapter to get information from the dataset, this is done to ensure the versatility of the package. The sole purpose of the Dataset Adapter is to provide an interface to the two dataset tables. The Dataset Adapter is a class that implements `IgorRinkovec\CroNLP\DatasetAdapters\AbstractDatasetAdapter` abstract class. If you use the Laravel framework you can use the already implemented `IgorRinkovec\CroNLP\DatasetAdapters\EloquentDatasetAdapter`, if you use Doctrine or any other custom ORM, feel free to use it as a reference when implementing your own adapter. It is a pretty straight-forward process.

**If you implement a DatasetAdapter for a popular ORM, feel free to contribute it to the project by sending a merge request.**



## Usage
After you implemented the adapter, the usage is straight-forward:

```PHP
        $datasetAdapter = new EloquentDatasetAdapter(WordFrequency::class, WordVariation::class);
        $summarizer = new CroNLP($datasetAdapter);
        $summarizedText = $summarizer->summarize($content, $digestPercentage);
        $keywords = $summarizer->extractKeywords($content, $numOfKeywords);

```

First you have to construct a `IgorRinkovec\CroNLP\CroNLP` instance with a reference to your DatasetAdapter implementation object. `CroNLP` class exposes the following methods:

- **summarize($text, $percentageToCondense = 70)** - Summarizes the supplied $text to a $percentageToCondense of its original size.
- **extractKeywords($text, $amount = 10)** - Extracts the $amount of top ranking keywords in the supplied $text.


## Future
- Refactor the TextProcessorService to remove global variables and split it into several smaller services.
- Cover the codebase with more unit tests.
- Regenerate the dataset with a larger document database and try to clean up the results a bit more.
- Implement a DoctrineDatasetAdapter.


