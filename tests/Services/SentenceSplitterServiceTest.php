<?php
namespace IgorRinkovec\CroNLP\Tests\Services;

use IgorRinkovec\CroNLP\Services\SentenceSplitterService;

class SentenceSplitterServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SentenceSplitterService
     */
    protected $service;

    /**
     * Initializes the SentenceSplitterService
     */
    protected function setUp()
    {
        $this->service = new SentenceSplitterService();
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountEmpty()
    {
        $this->assertSame(0, $this->service->count(''));
        $this->assertSame(0, $this->service->count(' '));
        $this->assertSame(0, $this->service->count("\n"));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountWord()
    {
        $this->assertSame(1, $this->service->count('Riječ'));
        $this->assertSame(1, $this->service->count('Riječ.'));
        $this->assertSame(1, $this->service->count('Riječ...'));
        $this->assertSame(1, $this->service->count('Riječ!'));
        $this->assertSame(1, $this->service->count('Riječ?'));
        $this->assertSame(1, $this->service->count('Riječ?!'));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountTwoWords()
    {
        $this->assertSame(1, $this->service->count('Dvije riječi'));
        $this->assertSame(1, $this->service->count('Dvije riječi.'));
        $this->assertSame(1, $this->service->count('Dvije riječi...'));
        $this->assertSame(1, $this->service->count('Dvije riječi!'));
        $this->assertSame(1, $this->service->count('Dvije riječi?'));
        $this->assertSame(1, $this->service->count('Dvije riječi?!'));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountMultipleWords()
    {
        $this->assertSame(2, $this->service->count('Prva rečenica. Slijedi druga rečenica'));
        $this->assertSame(2, $this->service->count('Prva rečenica. Slijedi druga rečenica?'));
        $this->assertSame(1, $this->service->count('Prva rečenica, Slijedi druga rečenica?'));
        $this->assertSame(1, $this->service->count('Prva rečenica: Slijedi druga rečenica?'));
        $this->assertSame(1, $this->service->count('Prva rečenica... Slijedi druga rečenica?'));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountLinebreaks()
    {
        $this->assertSame(2, $this->service->count("Prva rečenica...\rSlijedi druga rečenica?"));
        $this->assertSame(2, $this->service->count("Prva rečenica...\nSlijedi druga rečenica?"));
        $this->assertSame(2, $this->service->count("Prva rečenica...\r\nSlijedi druga rečenica?"));
        $this->assertSame(2, $this->service->count("Prva rečenica...\r\n\rSlijedi druga rečenica?"));
        $this->assertSame(2, $this->service->count("Prva rečenica...\n\r\nSlijedi druga rečenica?"));
        $this->assertSame(2, $this->service->count("Prva rečenica...\n\nSlijedi druga rečenica?"));
        $this->assertSame(2, $this->service->count("Prva rečenica...\r\rSlijedi druga rečenica?"));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountAbreviations()
    {
        $this->assertSame(1, $this->service->count("Pozdrav g. Horvat."));
        $this->assertSame(1, $this->service->count("Pozdrav, OPG Horvat!"));
        $this->assertSame(1, $this->service->count("Pozdrav, gđice. Horvat!"));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountMultiplePunctuation()
    {
        $this->assertSame(2, $this->service->count("Prva rečenica. Slijedi druga rečenica."));
        $this->assertSame(1, $this->service->count("Prva rečenica... Slijedi druga rečenica."));
        $this->assertSame(2, $this->service->count("Prva rečenica?... Slijedi druga rečenica."));
        $this->assertSame(2, $this->service->count("Prva rečenica!... Slijedi druga rečenica."));
        $this->assertSame(2, $this->service->count("Prva rečenica!!! Slijedi druga rečenica."));
        $this->assertSame(2, $this->service->count("Prva rečenica??? Slijedi druga rečenica."));
    }

    /**
     * @covers SentenceSplitterService::count
     */
    public function testCountOneWordSentences()
    {
        $this->assertSame(2, $this->service->count("Gospodine? Gospodine Horvat?"));
        $this->assertSame(1, $this->service->count("Vi ste g. Horvat?"));
        $this->assertSame(2, $this->service->count("Jeste li ovdje. Gospodine Horvat?"));
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitEmpty()
    {
        $this->assertSame(array(), $this->service->split(''));
        $this->assertSame(array(), $this->service->split(' '));
        $this->assertSame(array(), $this->service->split("\n"));
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitWord()
    {
        $this->assertSame(array('Pozdrav'), $this->service->split('Pozdrav'));
        $this->assertSame(array('Pozdrav.'), $this->service->split('Pozdrav.'));
        $this->assertSame(array('Pozdrav...'), $this->service->split('Pozdrav...'));
        $this->assertSame(array('Pozdrav!'), $this->service->split('Pozdrav!'));
        $this->assertSame(array('Pozdrav?'), $this->service->split('Pozdrav?'));
        $this->assertSame(array('Pozdrav?!'), $this->service->split('Pozdrav?!'));
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitMultipleWords()
    {
        $this->assertSame(array('Prva Rečenica.', ' Slijedi druga rečenica'), $this->service->split('Prva Rečenica. Slijedi druga rečenica'));
        $this->assertSame(array('Prva Rečenica.', ' Slijedi druga rečenica?'), $this->service->split('Prva Rečenica. Slijedi druga rečenica?'));
        $this->assertSame(array('Prva Rečenica.', 'Slijedi druga rečenica'), $this->service->split('Prva Rečenica. Slijedi druga rečenica', SentenceSplitterService::SPLIT_TRIM));
        $this->assertSame(array('Prva Rečenica.', 'Slijedi druga rečenica?'), $this->service->split('Prva Rečenica. Slijedi druga rečenica?', SentenceSplitterService::SPLIT_TRIM));
        $this->assertSame(array('Prva Rečenica, Slijedi druga rečenica?'), $this->service->split('Prva Rečenica, Slijedi druga rečenica?'));
        $this->assertSame(array('Prva Rečenica: Slijedi druga rečenica?'), $this->service->split('Prva Rečenica: Slijedi druga rečenica?'));
        $this->assertSame(array('Prva Rečenica... Slijedi druga rečenica?'), $this->service->split('Prva Rečenica... Slijedi druga rečenica?'));
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitLinebreaks()
    {
        $this->assertSame(array("Prva Rečenica...\r", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\rSlijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\n", " Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\n Slijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\n", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\nSlijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\r\n", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\r\nSlijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\r\n\r", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\r\n\rSlijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\n\r\n", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\n\r\nSlijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\n\n", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\n\nSlijedi druga rečenica?"));
        $this->assertSame(array("Prva Rečenica...\r\r", "Slijedi druga rečenica?"), $this->service->split("Prva Rečenica...\r\rSlijedi druga rečenica?"));
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitOneWordSentences()
    {
        $this->assertSame(array("Gospodine?", " Horvat?"), $this->service->split("Gospodine? Horvat?"));
        $this->assertSame(array("Jeste li ovdje?", " Horvat?"), $this->service->split("Jeste li ovdje? Horvat?"));
        $this->assertSame(array("Jeste li vi g. Horvat?"), $this->service->split("Jeste li vi g. Horvat?"));
        $this->assertSame(array("Jeste li ovdje?", " Gospodine Horvat?"), $this->service->split("Jeste li ovdje? Gospodine Horvat?"));
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitParenthesis()
    {
        $this->assertSame(
            array("Ovo je test (sa zagradama!).", " Gospodin Horvat"),
            $this->service->split("Ovo je test (sa zagradama!). Gospodin Horvat")
        );
        $this->assertSame(
            array("Opet (test!) sa zagradama.", " Gospodin Horvat"),
            $this->service->split("Opet (test!) sa zagradama. Gospodin Horvat")
        );
        $this->assertSame(
            array("(Opet!) ovo testiramo.", " Gospodin Horvat"),
            $this->service->split("(Opet!) ovo testiramo. Gospodin Horvat")
        );
        $this->assertSame(
            array("Ovo je test (sa zagradama).", " Gospodin Horvat"),
            $this->service->split("Ovo je test (sa zagradama). Gospodin Horvat")
        );
        $this->assertSame(
            array("Ovo (pravi test) je.", " Gospodin Horvat"),
            $this->service->split("Ovo (pravi test) je. Gospodin Horvat")
        );
        $this->assertSame(
            array("(Što) vi radite?", " Gospodin Horvat"),
            $this->service->split("(Što) vi radite? Gospodin Horvat")
        );
    }

    /**
     * @covers SentenceSplitterService::split
     */
    public function testSplitOrdinalNumbers()
    {
        $this->assertSame(
            array('To se dogodilo 2014. godine!'),
            $this->service->split('To se dogodilo 2014. godine!')
        );
        $this->assertSame(
            array('1990. započele su devedesete.'),
            $this->service->split('1990. započele su devedesete.')
        );
        $this->assertSame(
            array('90-ih godina prošlog stoljeća.'),
            $this->service->split('90-ih godina prošlog stoljeća.')
        );
    }


}
