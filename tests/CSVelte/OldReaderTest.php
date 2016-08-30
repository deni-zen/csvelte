<?php

use PHPUnit\Framework\TestCase;
use CSVelte\CSVelte;
use CSVelte\Reader;
use CSVelte\Table\Row;
use CSVelte\Flavor;
use CSVelte\Contract\Readable;
use CSVelte\Input\Stream;
use CSVelte\Input\File;
use CSVelte\Input\String;
use Carbon\Carbon;

/**
 * CSVelte\Reader Tests
 *
 * @package   CSVelte Unit Tests
 * @copyright (c) 2016, Luke Visinoni <luke.visinoni@gmail.com>
 * @author    Luke Visinoni <luke.visinoni@gmail.com>
 * @todo      Move these into the new reader test class when possible
 */
class OldReaderTest extends TestCase
{
    protected $CSVstrings = array(
        'NoQuote' => "1,Eldon Base for stackable storage shelf platinum,Muhammed MacIntyre,3,-213.25,38.94,35,Nunavut,Storage & Organization,0.8\n2,1.7 Cubic Foot Compact Office Refrigerators,Barry French,293,457.81,208.16,68.02,Nunavut,Appliances,0.58\n3,Cardinal Slant-DÆ Ring Binder Heavy Gauge Vinyl,Barry French,293,46.71,8.69,2.99,Nunavut,Binders and Binder Accessories,0.39\n4,R380,Clay Rozendal,483,1198.97,195.99,3.99,Nunavut,Telephones and Communication,0.58\n5,Holmes HEPA Air Purifier,Carlos Soltero,515,30.94,21.78,5.94,Nunavut,Appliances,0.5\n6,G.E. Longer-Life Indoor Recessed Floodlight Bulbs,Carlos Soltero,515,4.43,6.64,4.95,Nunavut,Office Furnishings,0.37\n7,Angle-D Binders with Locking Rings Label Holders,Carl Jackson,613,-54.04,7.3,7.72,Nunavut,Binders and Binder Accessories,0.38\n8,SAFCO Mobile Desk Side File Wire Frame,Carl Jackson,613,127.70,42.76,6.22,Nunavut,Storage & Organization,\n9,SAFCO Commercial Wire Shelving Black,Monica Federle,643,-695.26,138.14,35,Nunavut,Storage & Organization,\n10,Xerox 198,Dorothy Badders,678,-226.36,4.98,8.33,Nunavut,Paper,0.38",
        'QuoteMinimal' => "Bank Name,City,ST,CERT,Acquiring Institution,Closing Date,Updated Date\nFirst CornerStone Bank,\"King of\nPrussia\",PA,35312,First-Citizens Bank & Trust Company,6-May-16,25-May-16\nTrust Company Bank,Memphis,TN,9956,The Bank of Fayette County,29-Apr-16,25-May-16\nNorth Milwaukee State Bank,Milwaukee,WI,20364,First-Citizens Bank & Trust Company,11-Mar-16,16-Jun-16\nHometown National Bank,Longview,WA,35156,Twin City Bank,2-Oct-15,13-Apr-16\nThe Bank of Georgia,Peachtree City,GA,35259,Fidelity Bank,2-Oct-15,13-Apr-16\nPremier Bank,Denver,CO,34112,\"United Fidelity \r\n \r \r \n \r\n Bank, fsb\",10-Jul-15,17-Dec-15\nEdgebrook Bank,Chicago,IL,57772,Republic Bank of Chicago,8-May-15,2-Jun-16\nDoral Bank,San Juan,PR,32102,Banco Popular de Puerto Rico,27-Feb-15,13-May-15\nCapitol\t City Bank & Trust: Company,Atlanta,GA,33938,First-Citizens Bank & Trust: Company,13-Feb-15,21-Apr-15\nHighland: Community Bank,Chicago,IL,20290,\"United Fidelity Bank, fsb\",23-Jan-15,21-Apr-15\nFirst National Bank of Crestview ,Crestview,FL,17557,First NBC Bank,16-Jan-15,15-Jan-16\nNorthern Star Bank,Mankato,MN,34983,BankVista,19-Dec-14,6-Jan-16\n\"Frontier Bank, FSB D/B/A El Paseo Bank\",Palm Desert,CA,34738,\"Bank of Southern California, N.A.\",7-Nov-14,6-Jan-16\nThe National Republic Bank of Chicago,Chicago,IL,916,State Bank of Texas,24-Oct-14,6-Jan-16\nNBRS Financial,Rising Sun,MD,4862,Howard Bank,17-Oct-14,26-Mar-15\n\"GreenChoice Bank, fsb\",Chicago,IL,28462,\"Providence Bank, LLC\",25-Jul-14,28-Jul-15\nEastside Commercial Bank,Conyers,GA,58125,Community: Southern Bank,18-Jul-14,28-Jul-15\nThe Freedom State Bank ,Freedom,OK,12483,Alva State Bank & Trust Company,27-Jun-14,25-Mar-16\nValley Bank,Fort Lauderdale,FL,21793,\"Landmark Bank, National Association\",20-Jun-14,29-Jun-15\nValley Bank,Moline,IL,10450,Great Southern Bank,20-Jun-14,26-Jun-15\nSlavie Federal Savings Bank,Bel Air,MD,32368,\"Bay Bank, FSB\",30-May-14,15-Jun-15\nColumbia Savings Bank,Cincinnati,OH,32284,\"United Fidelity Bank, fsb\",23-May-14,28-May-15\nAztecAmerica Bank ,Berwyn,IL,57866,Republic Bank of Chicago,16-May-14,18-Jul-14\nAllendale County Bank,Fairfax,SC,15062,Palmetto State Bank,25-Apr-14,18-Jul-14\nVantage Point Bank,Horsham,PA,58531,First Choice Bank,28-Feb-14,3-Mar-15\n\"Millennium Bank, National\n Association\",Sterling,VA,35096,WashingtonFirst Bank,28-Feb-14,3-Mar-15\nSyringa Bank,Boise,ID,34296,Sunwest Bank,31-Jan-14,12-Apr-16\nThe Bank of Union,El Reno,OK,17967,BancFirst,24-Jan-14,25-Mar-16\nDuPage National Bank,West Chicago,IL,5732,Republic Bank of Chicago,17-Jan-14,19-F\n"
    );

    public function testReaderTreatsQuotedNewlinesAsOneLine()
    {
        $flavor = new Flavor(array('quoteStyle' => Flavor::QUOTE_MINIMAL, 'lineTerminator' => "\n"), array('hasHeader' => false));
        $source = new String($this->CSVstrings['QuoteMinimal']);
        //$source = new Stream('file:///Users/luke/test.csv');
        $reader = new Reader($source, $flavor);
        //$reader->next();
        $line = $reader->current();
        $this->assertEquals($expected = "First CornerStone Bank,King of\nPrussia,PA,35312,First-Citizens Bank & Trust Company,6-May-16,25-May-16", $line->join(","));
    }

    public function testReaderWillAutomaticallyDetectFlavorIfNoneProvided()
    {
        // $stub = $this->createMock(Readable::class);
        // $stub->method('read')
        //      ->willReturn(file_get_contents(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')));
        $in = new Stream("file://" . realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv'));
        $reader = new Reader($in);
        $expected = new Flavor(array(
            'delimiter' => ',',
            'quoteChar' => '"',
            'quoteStyle' => Flavor::QUOTE_MINIMAL,
            'escapeChar' => '\\',
            'lineTerminator' => "\r\n"
        ));
        $this->assertInstanceOf(Flavor::class, $flavor = $reader->getFlavor());
        $this->assertEquals($expected, $flavor);
    }

    // it is useful for a CSV reader class to have a method for determining
    // whether or not its source input contains a header column, so this provides
    // one for convenience, although it is just a proxy to Taster with a sort of
    // cache so that the expensive Taster::lickHeader method is only ran when it
    // has to be (when input source changes or something)
    public function testReaderHasHeader()
    {
        $no_header_stub = $this->createMock(Readable::class);
        $no_header_stub->method('read')
             ->willReturn(file_get_contents(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')));
        $no_header_reader = new Reader($no_header_stub);
        $this->assertEquals(false, $no_header_reader->hasHeader());

        $header_stub = $this->createMock(Readable::class);
        $header_stub->method('read')
             ->willReturn(substr(file_get_contents(realpath(__DIR__ . '/../files/banklist.csv')), 0, 2500));
        $header_reader = new Reader($header_stub);
        $this->assertEquals(true, $header_reader->hasHeader());
    }

    public function testReaderStillRunsLickHeaderIfFlavorWasPassedInWithNullHasHeaderProperty()
    {
        $flavor = new Flavor();
        $reader = new Reader(new Stream('file://' . realpath(__DIR__ . '/../files/banklist.csv')), $flavor);
        $this->assertTrue($reader->hasHeader());
    }

    public function testReaderCurrent()
    {
        $flavor = new Flavor(array('header' => false));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
        $this->assertInstanceOf($expected = Row::class, $reader->current());
        $this->assertEquals($expected = array("1","Eldon Base for stackable storage shelf, platinum","Muhammed MacIntyre","3","-213.25","38.94","35","Nunavut","Storage & Organization","0.8"), $reader->current()->toArray());
    }

    public function testReaderNext()
    {
        $flavor = new Flavor(array('header' => false));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
        $this->assertEquals($expected = array("1","Eldon Base for stackable storage shelf, platinum","Muhammed MacIntyre","3","-213.25","38.94","35","Nunavut","Storage & Organization","0.8"), $reader->current()->toArray());
        $this->assertEquals($expected = array("2","1.7 Cubic Foot Compact \"Cube\" Office Refrigerators","Barry French","293","457.81","208.16","68.02","Nunavut","Appliances","0.58"), $reader->next()->toArray());
        $this->assertEquals($expected = array("2","1.7 Cubic Foot Compact \"Cube\" Office Refrigerators","Barry French","293","457.81","208.16","68.02","Nunavut","Appliances","0.58"), $reader->current()->toArray());
    }

    public function testReaderValid()
    {
        $flavor = new Flavor(array('header' => false));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
        $this->assertEquals($expected = array("1","Eldon Base for stackable storage shelf, platinum","Muhammed MacIntyre","3","-213.25","38.94","35","Nunavut","Storage & Organization","0.8"), $reader->current()->toArray());
        $this->assertEquals($expected = array("2","1.7 Cubic Foot Compact \"Cube\" Office Refrigerators","Barry French","293","457.81","208.16","68.02","Nunavut","Appliances","0.58"), $reader->next()->toArray());
        // there are 10 lines in the source file...
        $reader->next(); // 7...
        $reader->next(); // 6...
        $reader->next(); // 5...
        $reader->next(); // 4...
        $reader->next(); // 3...
        $reader->next(); // 2...
        $reader->next(); // 1...
        $reader->next(); // 0...
        $reader->next(); // now we should have reached EOF...
        $this->assertFalse($reader->valid());
    }

    public function testReaderKey()
    {
        $flavor = new Flavor(array('header' => false));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
        $this->assertEquals($expected = array("1","Eldon Base for stackable storage shelf, platinum","Muhammed MacIntyre","3","-213.25","38.94","35","Nunavut","Storage & Organization","0.8"), $reader->current()->toArray());
        $this->assertEquals($expected = 1, $reader->key());
        $this->assertEquals($expected = array("2","1.7 Cubic Foot Compact \"Cube\" Office Refrigerators","Barry French","293","457.81","208.16","68.02","Nunavut","Appliances","0.58"), $reader->next()->toArray());
        $this->assertEquals($expected = 2, $reader->key());
        // there are 10 lines in the source file...
        $reader->next(); // 7...
        $this->assertEquals($expected = 3, $reader->key());
        $reader->next(); // 6...
        $this->assertEquals($expected = 4, $reader->key());
        $reader->next(); // 5...
        $this->assertEquals($expected = 5, $reader->key());
        $reader->next(); // 4...
        $this->assertEquals($expected = 6, $reader->key());
        $reader->next(); // 3...
        $this->assertEquals($expected = 7, $reader->key());
        $reader->next(); // 2...
        $this->assertEquals($expected = 8, $reader->key());
        $reader->next(); // 1...
        $this->assertEquals($expected = 9, $reader->key());
        $reader->next(); // 0...
        $this->assertEquals($expected = 10, $reader->key());
        $reader->next(); // now we should have reached EOF...
        $this->assertEquals($expected = 10, $reader->key());
        $this->assertFalse($reader->valid());
    }

    public function testReaderCanBeRewound()
    {
        $flavor = new Flavor(array('header' => false));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
        $reader->next(); // move to line 2
        $this->assertEquals($expected = array("2","1.7 Cubic Foot Compact \"Cube\" Office Refrigerators","Barry French","293","457.81","208.16","68.02","Nunavut","Appliances","0.58"), $reader->current()->toArray());
        $reader->next(); // move to ilne 3
        $reader->next(); // move to line 4
        $this->assertEquals($expected = 4, $reader->key());
        $reader->rewind();
        $this->assertEquals($expected = array("1","Eldon Base for stackable storage shelf, platinum","Muhammed MacIntyre","3","-213.25","38.94","35","Nunavut","Storage & Organization","0.8"), $reader->current()->toArray());
        $this->assertEquals($expected = 1, $reader->key());
    }

    public function testReaderCanBeIterated()
    {
        $flavor = new Flavor(array('header' => false));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
        $expected_line = 0;
        $first = $reader->current();
        foreach ($reader as $line => $row) {
            $this->assertEquals(++$expected_line, $line);
            $this->assertInstanceOf(Row::class, $row);
        }
        // does it rewind itself to be looped through again?
        $expected_line = 0;
        foreach ($reader as $line => $row) {
            $this->assertEquals(++$expected_line, $line);
            $this->assertInstanceOf(Row::class, $row);
        }
        // now, since the loop iterated to the end of the file, current should contain nothing...
        $this->assertFalse($reader->current());
        // not to worry, we can rewind that sucker!
        $reader->rewind();
        $this->assertEquals($first, $reader->current());
    }

    // I was using outeriterator incorrectly so I killed it
    // public function testReaderImplementsOuterIterator()
    // {
    //     $flavor = new Flavor(array('header' => false));
    //     $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/SampleCSVFile_2kb.csv')), $flavor);
    //     $this->assertEquals($expected = array("1","Eldon Base for stackable storage shelf, platinum","Muhammed MacIntyre","3","-213.25","38.94","35","Nunavut","Storage & Organization","0.8"), $reader->getInnerIterator()->toArray());
    // }

    public function testReaderCanSkipFirstLineAsHeader()
    {
        $flavor = new Flavor(array('header' => true));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/banklist.csv')), $flavor);
        $this->assertEquals(
            $expectedHeader = array('Bank Name','City','ST','CERT','Acquiring Institution','Closing Date','Updated Date'),
            $reader->header()->toArray()
        );
    }

    public function testHeaderRowIsAlwaysSkippedWhenWorkingWithReader()
    {
        $flavor = new Flavor(array('header' => true));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/banklist.csv')), $flavor);
        // make sure that directly after instantiation, current() returns row #2
        $this->assertEquals($expectedFirstRow = array('Bank Name' => 'First CornerStone Bank','City' => 'King of Prussia','ST' => 'PA','CERT' => '35312','Acquiring Institution' => 'First-Citizens Bank & Trust Company','Closing Date' => '6-May-16','Updated Date' => '25-May-16'), $reader->current()->toArray());
        $this->assertEquals($expectedHeader = array('Bank Name','City','ST','CERT','Acquiring Institution','Closing Date','Updated Date'), $reader->header()->toArray());
        // make sure that running through foreach starts with row #2
        foreach ($reader as $line_no => $row) {
            $this->assertEquals(2, $line_no);
            $this->assertEquals($expectedFirstRow = array('Bank Name' => 'First CornerStone Bank','City' => 'King of Prussia','ST' => 'PA','CERT' => '35312','Acquiring Institution' => 'First-Citizens Bank & Trust Company','Closing Date' => '6-May-16','Updated Date' => '25-May-16'), $row->toArray());
            break;
        }
    }

    public function testBodyRowsAreIndexedByHeaderValues()
    {
        $flavor = new Flavor(array('header' => true));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/banklist.csv')), $flavor);
        $line = $reader->current();
        $this->assertEquals($line[0], $line['Bank Name']);
        $this->assertEquals($line[1], $line['City']);
        $this->assertEquals($line[2], $line['ST']);
        $this->assertEquals($line[3], $line['CERT']);
        $this->assertEquals($line[4], $line['Acquiring Institution']);
        $this->assertEquals($line[5], $line['Closing Date']);
        $this->assertEquals($line[6], $line['Updated Date']);
    }

    // @todo This little nugget causes a very funkadelic little bug to pop up... come back to it.
    // public function testIterateOverRowsThenIterateOverColumns()
    // {
    //     $input = new Stream(realpath(__DIR__ . '/../files/banklist.csv'));
    //     $reader = new Reader($input);
    //
    //     $expectedLine = 0;
    //     foreach ($reader as $line_no => $row) {
    //         $this->assertEquals(++$expectedLine, $line_no);
    //         foreach ($row as $col_no => $val) {
    //             dd($col_no);
    //         }
    //     }
    // }

    /**
     * Default behavior when looping over columns is to provide column number
     * (indexed from 0) as key and column value as value. But I would like to
     * provide some sort of mechanism to change this so that you would get
     * header => value and/or possibly even header => valueObj where valueObj is
     * a special object representation of whatever data-type is contained in the
     * datum. For instance, if it's a date, you may get a CSVelte\DataType\DateTime
     * object or if it's a unit of currency you might get a DataType\Currency\Dollar
     * object. But this is definitely a nice-to-have, possibly not even useful
     * feature so I'm not worried about it just yet..
     */
    public function testIterateOverRowsThenIterateOverColumns()
    {
        $flavor = new Flavor(array('header' => true));
        $reader = new Reader(new Stream(realpath(__DIR__ . '/../files/banklist.csv')), $flavor);
        $headers = explode(',', 'Bank Name,City,ST,CERT,Acquiring Institution,Closing Date,Updated Date');

        $expectedLine = 1;
        foreach ($reader as $line_no => $row) {
            $expectedCol = 0;
            $this->assertEquals(++$expectedLine, $line_no);
            $i = 0;
            foreach ($row as $hdr => $val) {
                $this->assertEquals($headers[$i], $hdr);
                $i++;
            }
            if ($expectedLine > 2) break;
        }
    }

    public function testReaderStripsQuotesFromQuotedCells()
    {
        $in = new String("First CornerStone Bank,\"King of\nPrussia\",PA,35312,First-Citizens Bank & Trust Company,6-May-16,25-May-16\n\"Trust \"\"Company\"\" Bank\",Memphis,TN,9956,\"The \"\"Bank of Fayette\"\" County\",29-Apr-16,25-May-16\nNorth Milwaukee State Bank,Milwaukee,WI,20364,First-Citizens Bank & Trust Company,11-Mar-16,16-Jun-16\nHometown National Bank,Longview,WA,35156,Twin City Bank,2-Oct-15,13-Apr-16\nThe Bank of Georgia,Peachtree City,GA,35259,Fidelity Bank,2-Oct-15,13-Apr-16\nPremier Bank,Denver,CO,34112,\"United Fidelity \r\n \r \r \n \r\n Bank, fsb\",10-Jul-15,17-Dec-15");
        $reader = new Reader($in, new Flavor(array('lineTerminator' => "\n")));
        $line1 = $reader->current();
        $this->assertEquals("First CornerStone Bank", $line1[0], "Quote stripping tests--control test.");
        $this->assertEquals("King of\nPrussia", $line1[1], "Ensure that quoted strings get quotes stripped when read with reader");
        $line2 = $reader->next();
        $this->assertEquals("Trust \"Company\" Bank", $line2[0], "Ensure doublequote escaped quotes are reduced to one quote.");
        $this->assertEquals("The \"Bank of Fayette\" County", $line2[4], "Ensure doublequote escaped quotes are reduced to one quote.");
        // @todo need to test that escapeChar is removed when reading as well...
    }

    public function testReaderCanBeLoopedThroughMultipleTimes()
    {
        $reader = CSVelte::reader(realpath(__DIR__ . '/../files/banklist.csv'));
        $i = 0;
        foreach ($reader as $row) {
            $i++;
        }
        $this->assertEquals(545, $i, "First iteration of several");
        $i = 0;
        foreach ($reader as $row) {
            $i++;
        }
        $this->assertEquals(545, $i, "Second iteration of several");
        $i = 0;
        foreach ($reader as $row) {
            $i++;
        }
        $this->assertEquals(545, $i, "Last iteration of several");
    }

    /**
     * Just out of curiosity, test a flavor that uses "\n" for the delimiter and
     * like.. a tab or diff kind of line terminator string ("\r\n" or "\n"?) as
     * the line terminator. I just want to see how flexible the flavor concept
     * really is. Play around with stuff like * as line terminator and/or | as
     * the quote character. Try # or * or ^ or % as the escape character. Try it
     * with doubleQuote turned on and turned off (because, according to what
     * exists of a spec, doubleQuote and escapeChar are mutually exclusive)
     */
    // public function testFlavorsThatReallyStretchTheDialectSlashFlavorConceptToItsLimits()
    // {
    //     $flavor = new Flavor(array(
    //         'delimiter' => "\n",
    //         'lineTerminator' => "\r",
    //         'escapeChar' => "\t",
    //         'doubleQuote' => false,
    //         'quoteStyle' => Flavor::QUOTE_ALL,
    //         'header' => true
    //     ));
    // }

    public function testIteratorFilter()
    {
        $reader = CSVelte::reader(realpath(__DIR__ . '/../files/banklist.csv'));
        $i = 0;
        foreach ($reader as $row) {
            $i++;
        }
        $this->assertEquals(545, $i, "Control test to ensure that filter works properly");
        $reader->addFilter(function($row) {
            // only iterate rows with CERT larger than 30000
            if (isset($row['CERT'])) {
                $cert = (int) $row['CERT'];
                return ($cert > 30000);
            }
            return true;
        });
        $i2 = 0;
        foreach ($reader->filter() as $row) {
            if ($row['CERT'] <= 30000) {
                $this->assertTrue(false, "Ensure that reader filters properly");
            };
            $i2++;
        }
        $this->assertEquals(296, $i2);
    }

    public function testMultipleFiltersOnReader()
    {
        $data = "id,firstname,lastname,email,phone,created
1,luke,visinoni,luke.visinoni@gmail.com,5305551234,2016-04-23 14:25:04
2,margaret,kelly,mkelly@mekelly.info,5305554321,2014-01-12 05:04:23
3,patrick,kelly,pat.kelly@mekelly.info,5305551112,2010-03-20 06:34:39
4,jeff,carson,jcarson23@gmail.com,5307812234,2011-11-01 5:01:05
5,larry,thecableguy,larry@cableguy.net,,2013-01-02 22:45:52
6,jim,jefferies,,5302239399,2015-12-24 11:51:57
";
        $reader = CSVelte::stringReader($data, new Flavor(array('lineTerminator' => "
", 'header' => true)));
        $i = 0;
        foreach ($reader as $row) {
            $i++;
        }
        $this->assertEquals(6, $i, "Control test to ensure that multiple filter works properly");
        $i2 = 0;
        $release = Carbon::parse("2012-11-01 8:00");
        foreach ($reader->addFilter(function($row) use ($release) {
            if (isset($row['created'])) {
                $date = Carbon::parse($row['created']);
                return $date->gt($release);
            }
            return true;
        })->addFilter(function($row) {
            return !empty($row['email']);
        })->filter() as $row) {
            $date = Carbon::parse($row['created']);
            if (empty($row['email']) || $date->lte($release)) {
                $this->assertTrue(false, "Ensure that reader filters properly");
            };
            $i2++;
        }
        $this->assertEquals(3, $i2);
    }

}
