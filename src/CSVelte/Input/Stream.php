<?php namespace CSVelte\Input;

use CSVelte\Contract\Readable;

/**
 * CSVelte\Input\Stream
 * Represents a stream source for CSV data
 *
 * @package   CSVelte
 * @copyright (c) 2016, Luke Visinoni <luke.visinoni@gmail.com>
 * @author    Luke Visinoni <luke.visinoni@gmail.com>
 */
class Stream implements Readable
{
    /**
     * @const integer
     */
    const MAX_LINE_LENGTH = 4096;

    /**
     * @var resource The stream resource to input file
     */
    protected $source;

    /**
     * @var array An array of meta data about the source stream
     */
    protected $info;

    /**
     * @var integer The position of the pointer within the stream resource
     */
    protected $position;

    /**
     * Class constructor
     *
     * @param string The path and filename of the input file to read from
     * @return void
     * @access public
     */
    public function __construct($name)
    {
        if (false === ($this->source = @fopen($name, 'r'))) {
            // @todo custom exception
            throw new \Exception('Cannot open stream: ' . $name);
        }
        $this->updateInfo();
    }

    /**
     * Get the current position of the pointer
     *
     * @return integer Position of pointer within source
     * @access public
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * Get the current position of the pointer
     *
     * @return integer|false Position of pointer within source or false on failure
     * @access protected
     */
    protected function updateInfo()
    {
        $this->info = stream_get_meta_data($this->source);
        return $this->position = ftell($this->source);
    }

    /**
     * Retrieve the name of this stream. If stream is a file, it will return the
     * file's name. If it's some other type of stream, it's hard to say what,
     * exactly, the name will be.
     *
     * @return string The name of the stream resource
     * @access public
     */
    public function name()
    {
        return basename($this->info['uri']);
    }

    /**
     * Retrieve the dirname part of the stream name
     *
     * @return string The dirname of this stream's path
     * @access public
     * @todo I'm not sure this method is actually relevant when dealing with
     *     streams such as php://filter/read=string.toupper/resource=file:///var/www/foo.csv
     *     I'm not sure whether I should parse the stream name and return the
     *     dirname(realpath()) of /var/www/foo.csv or if the rest of it actually
     *     is techinally part of the dirname... I'm going to leave it as is for
     *     now because I'm leaning towards "It doesn't matter"
     */
    public function path()
    {
        return dirname($this->info['uri']);
    }

    /**
     * @inheritDoc
     */
    public function read($length)
    {
        if (false === ($data = fread($this->source, $length))) {
            // @todo custom exception
            throw new \Exception('Cannot read from ' . $this->name());
        }
        $this->updateInfo();
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function readLine($max = null, $eol = "\n")
    {
        if (false === ($line = stream_get_line($this->source, $max ?: self::MAX_LINE_LENGTH, $eol))) {
            // @todo custom exception
            throw new \Exception('Cannot read line from ' . $this->name());
        }
        $this->updateInfo();
        return $line;
    }
}
