<?php
/**
 * CSVelte: Slender, elegant CSV for PHP
 *
 * Inspired by Python's CSV module and Frictionless Data and the W3C's CSV
 * standardization efforts, CSVelte was written in an effort to take all the
 * suck out of working with CSV.
 *
 * @copyright Copyright (c) 2018 Luke Visinoni
 * @author    Luke Visinoni <luke.visinoni@gmail.com>
 * @license   See LICENSE file (MIT license)
 */
namespace CSVelteTest;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Base Unit Test for CSVelte
 *
 * @package   CSVelte Unit Tests
 * @copyright (c) 2016, Luke Visinoni <luke.visinoni@gmail.com>
 * @author    Luke Visinoni <luke.visinoni@gmail.com>
 */
abstract class UnitTestCase extends TestCase
{
    protected $root;

    protected $filedir;

//    protected $tmpdir;

    public function setUp()
    {
        //$this->tmpdir = realpath(__DIR__ . '/../files/temp');
        $this->root = vfsStream::setup();
        $this->filedir = $this->root->url() . "/testfiles";
        mkdir($this->filedir);
        $strings = array(
            'veryShort' => "foo,bar,baz\nbin,boz,bork\nlib,bil,ilb\n",
            'shortQuotedNewlines' => "foo,bar,baz\nbin,\"boz,bork\nlib,bil,ilb\",bon\nbib,bob,\"boob\nboober\"\ncool,pool,wool\n",
            'shortQuotedNewlines2' => "bin,\"boz,\"\"bork\"\"\nlib,bil,ilb\",bon\nfoo,bar,baz\nbib,bob,\"boob\nboober\"\ncool,pool,wool\n",
            'commaNewlineHeader' => "Bank Name,City,ST,CERT,Acquiring Institution,Closing Date,Updated Date\nFirst CornerStone Bank,\"King of\nPrussia\",PA,35312,First-Citizens Bank & Trust Company,6-May-16,25-May-16\nTrust Company Bank,Memphis,TN,9956,The Bank of Fayette County,29-Apr-16,25-May-16\nNorth Milwaukee State Bank,Milwaukee,WI,20364,First-Citizens Bank & Trust Company,11-Mar-16,16-Jun-16\nHometown National Bank,Longview,WA,35156,Twin City Bank,2-Oct-15,13-Apr-16\nThe Bank of Georgia,Peachtree City,GA,35259,Fidelity Bank,2-Oct-15,13-Apr-16\nPremier Bank,Denver,CO,34112,\"United Fidelity \r\n \r \r \n \r\n Bank, fsb\",10-Jul-15,17-Dec-15\nEdgebrook Bank,Chicago,IL,57772,Republic Bank of Chicago,8-May-15,2-Jun-16\nDoral Bank,San Juan,PR,32102,Banco Popular de Puerto Rico,27-Feb-15,13-May-15\nCapitol\t City Bank & Trust: Company,Atlanta,GA,33938,First-Citizens Bank & Trust: Company,13-Feb-15,21-Apr-15\nHighland: Community Bank,Chicago,IL,20290,\"United Fidelity Bank, fsb\",23-Jan-15,21-Apr-15\nFirst National Bank of Crestview ,Crestview,FL,17557,First NBC Bank,16-Jan-15,15-Jan-16\nNorthern Star Bank,Mankato,MN,34983,BankVista,19-Dec-14,6-Jan-16\n\"Frontier Bank, FSB D/B/A El Paseo Bank\",Palm Desert,CA,34738,\"Bank of Southern California, N.A.\",7-Nov-14,6-Jan-16\nThe National Republic Bank of Chicago,Chicago,IL,916,State Bank of Texas,24-Oct-14,6-Jan-16\nNBRS Financial,Rising Sun,MD,4862,Howard Bank,17-Oct-14,26-Mar-15\n\"GreenChoice Bank, fsb\",Chicago,IL,28462,\"Providence Bank, LLC\",25-Jul-14,28-Jul-15\nEastside Commercial Bank,Conyers,GA,58125,Community: Southern Bank,18-Jul-14,28-Jul-15\nThe Freedom State Bank ,Freedom,OK,12483,Alva State Bank & Trust Company,27-Jun-14,25-Mar-16\nValley Bank,Fort Lauderdale,FL,21793,\"Landmark Bank, National Association\",20-Jun-14,29-Jun-15\nValley Bank,Moline,IL,10450,Great Southern Bank,20-Jun-14,26-Jun-15\nSlavie Federal Savings Bank,Bel Air,MD,32368,\"Bay Bank, FSB\",30-May-14,15-Jun-15\nColumbia Savings Bank,Cincinnati,OH,32284,\"United Fidelity Bank, fsb\",23-May-14,28-May-15\nAztecAmerica Bank ,Berwyn,IL,57866,Republic Bank of Chicago,16-May-14,18-Jul-14\nAllendale County Bank,Fairfax,SC,15062,Palmetto State Bank,25-Apr-14,18-Jul-14\nVantage Point Bank,Horsham,PA,58531,First Choice Bank,28-Feb-14,3-Mar-15\n\"Millennium Bank, National\n Association\",Sterling,VA,35096,WashingtonFirst Bank,28-Feb-14,3-Mar-15\nSyringa Bank,Boise,ID,34296,Sunwest Bank,31-Jan-14,12-Apr-16\nThe Bank of Union,El Reno,OK,17967,BancFirst,24-Jan-14,25-Mar-16\nDuPage National Bank,West Chicago,IL,5732,Republic Bank of Chicago,17-Jan-14,19-F\n",
            'headerDoubleQuote' => "Bank Name,City,ST,CERT,Acquiring Institution,Closing Date,Updated Date\nFirst CornerStone Bank,\"King of\n\"\"Prussia\"\"\",PA,35312,First-Citizens Bank & Trust Company,6-May-16,25-May-16\nTrust Company Bank,Memphis,TN,9956,The Bank of Fayette County,29-Apr-16,25-May-16\nNorth Milwaukee State Bank,Milwaukee,WI,20364,First-Citizens Bank & Trust Company,11-Mar-16,16-Jun-16\nHometown National Bank,Longview,WA,35156,Twin City Bank,2-Oct-15,13-Apr-16\nThe Bank of Georgia,Peachtree City,GA,35259,Fidelity Bank,2-Oct-15,13-Apr-16\nPremier Bank,Denver,CO,34112,\"United Fidelity \r\n \r \r \n \r\n Bank, fsb\",10-Jul-15,17-Dec-15\nEdgebrook Bank,Chicago,IL,57772,Republic Bank of Chicago,8-May-15,2-Jun-16\nDoral Bank,San Juan,PR,32102,Banco Popular de Puerto Rico,27-Feb-15,13-May-15\nCapitol\t City Bank & Trust: Company,Atlanta,GA,33938,First-Citizens Bank & Trust: Company,13-Feb-15,21-Apr-15\n\"Highland: \"\"Community\"\" Bank\",Chicago,IL,20290,\"United Fidelity Bank, fsb\",23-Jan-15,21-Apr-15\nFirst National Bank of Crestview ,Crestview,FL,17557,First NBC Bank,16-Jan-15,15-Jan-16\nNorthern Star Bank,Mankato,MN,34983,BankVista,19-Dec-14,6-Jan-16\n\"Frontier Bank, FSB D/B/A El Paseo Bank\",Palm Desert,CA,34738,\"Bank of Southern California, N.A.\",7-Nov-14,6-Jan-16\nThe National Republic Bank of Chicago,Chicago,IL,916,State Bank of Texas,24-Oct-14,6-Jan-16\nNBRS Financial,Rising Sun,MD,4862,Howard Bank,17-Oct-14,26-Mar-15\n\"GreenChoice Bank, fsb\",Chicago,IL,28462,\"Providence Bank, LLC\",25-Jul-14,28-Jul-15\nEastside Commercial Bank,Conyers,GA,58125,Community: Southern Bank,18-Jul-14,28-Jul-15\nThe Freedom State Bank ,Freedom,OK,12483,Alva State Bank & Trust Company,27-Jun-14,25-Mar-16\nValley Bank,Fort Lauderdale,FL,21793,\"Landmark Bank, National Association\",20-Jun-14,29-Jun-15\nValley Bank,Moline,IL,10450,Great Southern Bank,20-Jun-14,26-Jun-15\nSlavie Federal Savings Bank,Bel Air,MD,32368,\"Bay Bank, FSB\",30-May-14,15-Jun-15\nColumbia Savings Bank,Cincinnati,OH,32284,\"United Fidelity Bank, fsb\",23-May-14,28-May-15\nAztecAmerica Bank ,Berwyn,IL,57866,Republic Bank of Chicago,16-May-14,18-Jul-14\nAllendale County Bank,Fairfax,SC,15062,Palmetto State Bank,25-Apr-14,18-Jul-14\nVantage Point Bank,Horsham,PA,58531,First Choice Bank,28-Feb-14,3-Mar-15\n\"Millennium Bank, National\n Association\",Sterling,VA,35096,WashingtonFirst Bank,28-Feb-14,3-Mar-15\nSyringa Bank,Boise,ID,34296,Sunwest Bank,31-Jan-14,12-Apr-16\nThe Bank of Union,El Reno,OK,17967,BancFirst,24-Jan-14,25-Mar-16\nDuPage National Bank,West Chicago,IL,5732,\"Republic \"\"Bank\"\" of Chicago\",17-Jan-14,19-F\n",
            'headerTabSingleQuotes' => "Bank Name\tCity\tST\tCERT\tAcquiring Institution\tClosing Date\tUpdated Date\nFirst CornerStone Bank\tKing of Prussia\tPA\t35312\tFirst-Citizens Bank & Trust Company\t6-May-16\t25-May-16\nTrust Company Bank\tMemphis\tTN\t9956\tThe Bank of Fayette County\t29-Apr-16\t25-May-16\nNorth Milwaukee State Bank\tMilwaukee\tWI\t20364\tFirst-Citizens Bank & Trust Company\t11-Mar-16\t16-Jun-16\nHometown National Bank\tLongview\tWA\t35156\tTwin City Bank\t2-Oct-15\t13-Apr-16\nThe Bank of Georgia\tPeachtree City\tGA\t35259\tFidelity Bank\t2-Oct-15\t13-Apr-16\nPremier Bank\tDenver\tCO\t34112\t'United Fidelity \r\n \r \r \n \r\n Bank\t fsb'\t10-Jul-15\t17-Dec-15\nEdgebrook Bank\tChicago\tIL\t57772\tRepublic Bank of Chicago\t8-May-15\t2-Jun-16\nDoral Bank\tSan Juan\tPR\t32102\tBanco Popular de Puerto Rico\t27-Feb-15\t13-May-15\nCapitol City Bank & Trust Company\tAtlanta\tGA\t33938\tFirst-Citizens Bank & Trust Company\t13-Feb-15\t21-Apr-15\nHighland Community Bank\tChicago\tIL\t20290\t'United Fidelity Bank, fsb'\t23-Jan-15\t21-Apr-15\nFirst National Bank of Crestview \tCrestview\tFL\t17557\tFirst NBC Bank\t16-Jan-15\t15-Jan-16\nNorthern Star Bank\tMankato\tMN\t34983\tBankVista\t19-Dec-14\t6-Jan-16\n'Frontier\'s Bank, FSB D/B/A El Paseo Bank'\tPalm Desert\tCA\t34738\t'Bank of Southern California, N.A.'\t7-Nov-14\t6-Jan-16\nThe National Republic Bank of Chicago\tChicago\tIL\t916\tState Bank of Texas\t24-Oct-14\t6-Jan-16\nNBRS Financial\tRising Sun\tMD\t4862\tHoward Bank\t17-Oct-14\t26-Mar-15\n'GreenChoice\'s Bank, fsb'\tChicago\tIL\t28462\t'Providence Bank, LLC'\t25-Jul-14\t28-Jul-15\nEastside Commercial Bank\tConyers\tGA\t58125\tCommunity & Southern Bank\t18-Jul-14\t28-Jul-15\nThe Freedom State Bank \tFreedom\tOK\t12483\tAlva State Bank & Trust Company\t27-Jun-14\t25-Mar-16\nValley Bank\tFort Lauderdale\tFL\t21793\t'Landmark Bank, National Association'\t20-Jun-14\t29-Jun-15\nValley Bank\tMoline\tIL\t10450\tGreat Southern Bank\t20-Jun-14\t26-Jun-15\nSlavie Federal Savings Bank\tBel Air\tMD\t32368\t'Bay Bank, FSB'\t30-May-14\t15-Jun-15\nColumbia Savings Bank\tCincinnati\tOH\t32284\t'United Fidelity Bank, fsb'\t23-May-14\t28-May-15\nAztecAmerica Bank \tBerwyn\tIL\t57866\tRepublic Bank of Chicago\t16-May-14\t18-Jul-14\nAllendale County Bank\tFairfax\tSC\t15062\tPalmetto State Bank\t25-Apr-14\t18-Jul-14\nVantage Point Bank\tHorsham\tPA\t58531\tFirst Choice Bank\t28-Feb-14\t3-Mar-15\n'Millennium Bank, National\n Association'\tSterling\tVA\t35096\tWashingtonFirst Bank\t28-Feb-14\t3-Mar-15\nSyringa Bank\tBoise\tID\t34296\tSunwest Bank\t31-Jan-14\t12-Apr-16\nThe Bank of Union\tEl Reno\tOK\t17967\tBancFirst\t24-Jan-14\t25-Mar-16\nDuPage National Bank\tWest Chicago\tIL\t5732\tRepublic Bank of Chicago\t17-Jan-14\t19-F\n",
            'noHeaderCommaNoQuotes' => "1,Eldon Base for stackable storage shelf platinum,Muhammed MacIntyre,3,-213.25,38.94,35,Nunavut,Storage & Organization,0.8\n2,1.7 Cubic Foot Compact Office Refrigerators,Barry French,293,457.81,208.16,68.02,Nunavut,Appliances,0.58\n3,Cardinal Slant-DÆ Ring Binder Heavy Gauge Vinyl,Barry French,293,46.71,8.69,2.99,Nunavut,Binders and Binder Accessories,0.39\n4,R380,Clay Rozendal,483,1198.97,195.99,3.99,Nunavut,Telephones and Communication,0.58\n5,Holmes HEPA Air Purifier,Carlos Soltero,515,30.94,21.78,5.94,Nunavut,Appliances,0.5\n6,G.E. Longer-Life Indoor Recessed Floodlight Bulbs,Carlos Soltero,515,4.43,6.64,4.95,Nunavut,Office Furnishings,0.37\n7,Angle-D Binders with Locking Rings Label Holders,Carl Jackson,613,-54.04,7.3,7.72,Nunavut,Binders and Binder Accessories,0.38\n8,SAFCO Mobile Desk Side File Wire Frame,Carl Jackson,613,127.70,42.76,6.22,Nunavut,Storage & Organization,\n9,SAFCO Commercial Wire Shelving Black,Monica Federle,643,-695.26,138.14,35,Nunavut,Storage & Organization,\n10,Xerox 198,Dorothy Badders,678,-226.36,4.98,8.33,Nunavut,Paper,0.38",
            'noHeaderCommaQuoteAll' => "\"1\",\"Eldon Base for stackable storage shelf platinum\",\"Muhammed MacIntyre\",\"3\",\"-213.25\",\"38.94\",\"35\",\"Nunavut\",\"Storage & Organization\",\"0.8\"\n\"2\",\"1.7 Cubic Foot Compact Office Refrigerators\",\"Barry French\",\"293\",\"457.81\",\"208.16\",\"68.02\",\"Nunavut\",\"Appliances\",\"0.58\"\n\"3\",\"Cardinal Slant-DÆ Ring Binder Heavy Gauge Vinyl\",\"Barry French\",\"293\",\"46.71\",\"8.69\",\"2.99\",\"Nunavut\",\"Binders and Binder Accessories\",\"0.39\"\n\"4\",\"R380\",\"Clay Rozendal\",\"483\",\"1198.97\",\"195.99\",\"3.99\",\"Nunavut\",\"Telephones and Communication\",\"0.58\"\n\"5\",\"Holmes HEPA Air Purifier\",\"Carlos Soltero\",\"515\",\"30.94\",\"21.78\",\"5.94\",\"Nunavut\",\"Appliances\",\"0.5\"\n\"6\",\"G.E. Longer-Life Indoor Recessed Floodlight Bulbs\",\"Carlos Soltero\",\"515\",\"4.43\",\"6.64\",\"4.95\",\"Nunavut\",\"Office Furnishings\",\"0.37\"\n\"7\",\"Angle-D Binders with Locking Rings Label Holders\",\"Carl Jackson\",\"613\",\"-54.04\",\"7.3\",\"7.72\",\"Nunavut\",\"Binders and Binder Accessories\",\"0.38\"\n\"8\",\"SAFCO Mobile Desk Side File Wire Frame\",\"Carl Jackson\",\"613\",\"127.70\",\"42.76\",\"6.22\",\"Nunavut\",\"Storage & Organization\",\"\"\n\"9\",\"SAFCO Commercial Wire Shelving Black\",\"Monica Federle\",\"643\",\"-695.26\",\"138.14\",\"35\",\"Nunavut\",\"Storage & Organization\",\"\"\n\"10\",\"Xerox 198\",\"Dorothy Badders\",\"678\",\"-226.36\",\"4.98\",\"8.33\",\"Nunavut\",\"Paper\",\"0.38\"",
            'headerCommaQuoteNonnumeric' => "\"policyID\",\"statecode\",\"county\",\"eq_site_limit\",\"hu_site_limit\",\"fl_site_limit\",\"fr_site_limit\",\"tiv_2011, tiv_2012\",\"eq_site_deductible\",\"hu_site_deductible\",\"fl_site_deductible\",\"fr_site_deductible\",\"point_latitude\",\"point_longitude\",\"line\",\"construction\",\"point_granularity\"\n119736,\"FL\",\"CLAY COUNTY\",498960,498960,498960,498960,498960,792148.9,0,9979.2,0,0,30.102261,-81.711777,\"Residential\",\"Masonry\",1\n448094,\"FL\",\"CLAY COUNTY\",1322376.3,1322376.3,1322376.3,1322376.3,1322376.3,1438163.57,0,0,0,0,30.063936,-81.707664,\"Residential\",\"Masonry\",3\n206893,\"FL\",\"CLAY COUNTY\",190724.4,190724.4,190724.4,190724.4,190724.4,192476.78,0,0,0,0,30.089579,-81.700455,\"Residential\",\"Wood\",1\n333743,\"FL\",\"CLAY COUNTY\",0,79520.76,0,0,79520.76,86854.48,0,0,0,0,30.063236,-81.707703,\"Residential\",\"Wood\",3\n172534,\"FL\",\"CLAY COUNTY\",0,254281.5,0,254281.5,254281.5,246144.49,0,0,0,0,30.060614,-81.702675,\"Residential\",\"Wood\",1\n785275,\"FL\",\"CLAY COUNTY\",0,515035.62,0,0,515035.62,884419.17,0,0,0,0,30.063236,-81.707703,\"Residential\",\"Masonry\",3\n995932,\"FL\",\"CLAY COUNTY\",0,19260000,0,0,19260000,20610000,0,0,0,0,30.102226,-81.713882,\"Commercial\",\"Reinforced Concrete\",1\n223488,\"FL\",\"CLAY COUNTY\",328500,328500,328500,328500,328500,348374.25,0,16425,0,0,30.102217,-81.707146,\"Residential\",\"Wood\",1\n433512,\"FL\",\"CLAY COUNTY\",315000,315000,315000,315000,315000,265821.57,0,15750,0,0,30.118774,-81.704613,\"Residential\",\"Wood\",1\n142071,\"FL\",\"CLAY COUNTY\",705600,705600,705600,705600,705600,1010842.56,14112,35280,0,0,30.100628,-81.703751,\"Residential\",\"Masonry\",1\n253816,\"FL\",\"CLAY COUNTY\",831498.3,831498.3,831498.3,831498.3,831498.3,1117791.48,0,0,0,0,30.10216,-81.719444,\"Residential\",\"Masonry\",1\n894922,\"FL\",\"CLAY COUNTY\",0,24059.09,0,0,24059.09,33952.19,0,0,0,0,30.095957,-81.695099,\"Residential\",\"Wood\",1\n422834,\"FL\",\"CLAY COUNTY\",0,48115.94,0,0,48115.94,66755.39,0,0,0,0,30.100073,-81.739822,\"Residential\",\"Wood\",1\n582721,\"FL\",\"CLAY COUNTY\",0,28869.12,0,0,28869.12,42826.99,0,0,0,0,30.09248,-81.725167,\"Residential\",\"Wood\",1\n842700,\"FL\",\"CLAY COUNTY\",0,56135.64,0,0,56135.64,50656.8,0,0,0,0,30.101356,-81.726248,\"Residential\",\"Wood\",1\n874333,\"FL\",\"CLAY COUNTY\",0,48115.94,0,0,48115.94,67905.07,0,0,0,0,30.113743,-81.727463,\"Residential\",\"Wood\",1\n580146,\"FL\",\"CLAY COUNTY\",0,48115.94,0,0,48115.94,66938.9,0,0,0,0,30.121655,-81.732391,\"Residential\",\"Wood\",3\n456149,\"FL\",\"CLAY COUNTY\",0,80192.49,0,0,80192.49,86421.04,0,0,0,0,30.109537,-81.741661,\"Residential\",\"Wood\",1\n767862,\"FL\",\"CLAY COUNTY\",0,48115.94,0,0,48115.94,73798.5,0,0,0,0,30.11824,-81.745335,\"Residential\",\"Wood\",3\n353022,\"FL\",\"CLAY COUNTY\",0,60946.79,0,0,60946.79,62467.29,0,0,0,0,30.065799,-81.717416,\"Residential\",\"Wood\",1\n367814,\"FL\",\"CLAY COUNTY\",0,28869.12,0,0,28869.12,42727.74,0,0,0,0,30.082993,-81.710581,\"Residential\",\"Wood\",1\n671392,\"FL\",\"CLAY COUNTY\",0,13410000,0,0,13410000,11700000,0,0,0,0,30.091921,-81.711929,\"Commercial\",\"Reinforced Concrete\",3\n772887,\"FL\",\"CLAY COUNTY\",0,1669113.93,0,0,1669113.93,2099127.76,0,0,0,0,30.117352,-81.711884,\"Residential\",\"Masonry\",1\n983122,\"FL\",\"CLAY COUNTY\",0,179562.23,0,0,179562.23,211372.57,0,0,0,0,30.095783,-81.713181,\"Residential\",\"Wood\",3\n934215,\"FL\",\"CLAY COUNTY\",0,177744.16,0,0,177744.16,157171.16,0,0,0,0,30.110518,-81.727478,\"Residential\",\"Wood\",1\n385951,\"FL\",\"CLAY COUNTY\",0,17757.58,0,0,17757.58,16948.72,0,0,0,0,30.10288,-81.705719,\"Residential\",\"Wood\",1\n716332,\"FL\",\"CLAY COUNTY\",0,130129.87,0,0,130129.87,101758.43,0,0,0,0,30.068468,-81.71624,\"Residential\",\"Wood\",1\n751262,\"FL\",\"CLAY COUNTY\",0,42854.77,0,0,42854.77,63592.88,0,0,0,0,30.068468,-81.71624,\"Residential\",\"Wood\",1\n633663,\"FL\",\"CLAY COUNTY\",0,785.58,0,0,785.58,662.18,0,0,0,0,30.068468,-81.71624,\"Residential\",\"Wood\",1\n105851,\"FL\",\"CLAY COUNTY\",0,170361.91,0,0,170361.91,177176.38,0,0,0,0,30.068468,-81.71624,\"Residential\",\"Wood\",1\n710400,\"FL\",\"CLAY COUNTY\",0,1430.89,0,0,1430.89,1861.41,0,0,0,0,30.068468,-81.71624,\"Residential\",\"Wood\",1",
            'noHeaderCommaQuoteMinimal' => "1,\"Eldon Base for stackable, storage, shelf, and platinum\",Muhammed MacIntyre,3,-213.25,38.94,35,Nunavut,\"Storage, Cleaning & Organization\",0.8\n2,1.7 Cubic Foot Compact Office Refrigerators,Barry French,293,457.81,208.16,68.02,Nunavut,Appliances,0.58\n3,\"Cardinal Slant-D, Ring Binder, Heavy Gauge, and Vinyl\",Barry French,293,46.71,8.69,2.99,Nunavut,Binders and Binder Accessories,0.39\n4,R380,Clay Rozendal,483,1198.97,195.99,3.99,Nunavut,Telephones and Communication,0.58\n5,Holmes HEPA Air Purifier,Carlos Soltero,515,30.94,21.78,5.94,Nunavut,\"Appliances, Construction, and Other stuff\",0.5\n6,G.E. Longer-Life Indoor Recessed Floodlight Bulbs,Carlos Soltero,515,4.43,6.64,4.95,Nunavut,Office Furnishings,0.37\n7,Angle-D Binders with Locking Rings Label Holders,Carl Jackson,613,-54.04,7.3,7.72,Nunavut,\"Binders,\n Binder Accessories\",0.38\n8,SAFCO Mobile Desk Side File Wire Frame,Carl Jackson,613,127.70,42.76,6.22,Nunavut,Storage & Organization,\n9,\"SAFCO\nCommercial Wire Shelving Black\",Monica Federle,643,-695.26,138.14,35,Nunavut,Storage & Organization,\n10,Xerox 198,Dorothy Badders,678,-226.36,4.98,8.33,Nunavut,Paper,0.38",
            'commaDelimTie' => "1,luke.visinoni@gmail.com,Luker,Visicvoni,10/10/2018,http://www.example.com/\n2,m.visinoni@gmail.com,Lauke,Visidfanoni,10/10/2018,http://www.google.com/\n3,luke@gmail.com,Lukeaa,Visindddoni,10/10/2018,http://www.bleh.com/\n4,linoni@gmail.com,Lukasdfe,Visinoni1,10/10/2018,http://www.asdf.com/\n5,luke.visinoni@gmail.net,Lffuke,ddddddddd,10/10/2018,http://www.ffdaoo.com/\n6,lunoni@yahoo.com,Lukde,Visasdfinoni,10/10/2018,http://www.fffffoo.com/\n7,lunoni@gmail.com,Lddaduke,Visisssnoni,10/10/2018,http://www.fofffo.com/\n8,lu23456@gmail.com,Luasdke,Visin-oni,10/10/2018,http://www.foasdassao.com/\n9,lili@example.com,Luke,Visiasdfsadfnoni,10/10/2018,http://www.foo.com/\n10,lvisinoni@lili.codddm,ddLuke,Visinasdfni,10/10/2018,http://www.foo.com/\n11,visinoni@nono.com,Lukdfe,Visinocni,10/10/2018,http://www.peepee.net/\n12,goofy@disney.com,Lukde,Visinoccni,10/10/2018,http://www.foo.com/\n13,bla@nothing.com,Lukasdfe,Visiccnoni,10/10/2018,http://www.foasdfasdfao.com/\n14,lasdf@lasdkfj.com,asdfLuke,Visicccnoni,10/10/2018,http://www.foo.com/\n15,gmail@luke.visinoni.com,Lasdfuke,Visinccconi,10/10/2018,http://www.foasdfasdfasdfo.com/\n16,luke@gmail.com,Lfuke,Visinoni,10/10/2018,http://www.foo.com/\n17,luke.visinonis@gmail.com,Like,Visindddoni,10/10/2018,http://www.ffffoo.com/\n18,luke.visinoni@gmail.com,Karl,Visinoni,10/10/2018,http://www.foo.com/\n19,luasdfasdfasdfnoni@gmail.com,Dale,Visddddddinoni,10/10/2018,http://www.foo.com/\n20,luke.visinoni@gmail.com,Luke,Visinoni,10/10/2018,http://www.somewebsite.com/\n21,luke.visinoni@gmaasdfil.com,Luddddke,ddasdf,10/10/2018,http://www.foo.com/\n22,lukasdfasdfni@gmail.com,Luke,Visiasdnoni,10/10/2018,http://www.foo.com/\n23,luke_visinoni@gmail.com,Ludddke,Visinoni,10/10/2018,http://www.foo.com/\n24,luke_visinoni_@gmail.com,Luasdfke,VisiSSSnoni,10/10/2018,http://www.someawesomesite.com/\n25,luassni@gmail.com,asdf,Vi-sinoni,10/10/2018,http://www.foo.com/\n26,luke.visinoni@gmail.com,asdfasdfasdf,VisiFDnoni,10/10/2018,http://www.visionofedesign.com/\n27,luke.vi_sin_oni@gmail.com,Ldaduke,VisinaSDoni,10/10/2018,http://www.foo.com/\n28,luke.v24567isinoni@gmail.com,Liuke,Visinoytrni,10/10/2018,http://www.foo.com/\n29,luke.vnoni@gmail.com,Lukie,Visipoinoni,10/10/2018,http://www.foo.com/\n30,lukasdfasdfasdfnoni@gmail.com,Lukiiiie,Visiddddnoni,10/10/2018,http://www.somethingyouknowandlove.com/\n31,lukisinoni@gmail.com,Lukeii,Visidddnoni,10/10/2018,http://www.foo.com/\n32,lukevisinoni@gmail.com,Lukesadf,Visidddnoni,10/10/2018,http://www.thevirginmaryandhergrandma.com/\n33,luke.visi@gmail.com,Luked,Visinonid,10/10/2018,http://www.poopootape.com/\n34,luke.vinoni@gmail.com,Luke,Visinoniocious,10/10/2018,http://www.peepeetable.com/\n"
        );
        foreach ($strings as $filename => $content) {
            $file = "{$this->filedir}/{$filename}.csv";
            file_put_contents($file, $content);
        }
    }

    public function tearDown()
    {
        // Do I need to destroy anything for vfsStream?
//        $dir = new \DirectoryIterator($this->tmpdir);
//        foreach ($dir as $finfo) {
//            if ($finfo->isFile()) {
//                $fname = $finfo->getPathname();
//                $finfo = null;
//                unlink ($fname);
//            }
//        }
    }

    protected function getFilePathFor($filekey)
    {
        $filename = "{$this->filedir}/{$filekey}.csv";
        if (!file_exists($filename)) throw new \Exception("Try again, sailor: " . $filekey);
        return $filename;
    }

    protected function getFileContentFor($filekey)
    {
        return file_get_contents($this->getFilePathFor($filekey));
    }
}