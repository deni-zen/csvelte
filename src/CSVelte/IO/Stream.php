<?php
/**
 * CSVelte: Slender, elegant CSV for PHP.
 *
 * Inspired by Python's CSV module and Frictionless Data and the W3C's CSV
 * standardization efforts, CSVelte was written in an effort to take all the
 * suck out of working with CSV.
 *
 * @version   v0.2.1
 * @copyright Copyright (c) 2016 Luke Visinoni <luke.visinoni@gmail.com>
 * @author    Luke Visinoni <luke.visinoni@gmail.com>
 * @license   https://github.com/deni-zen/csvelte/blob/master/LICENSE The MIT License (MIT)
 */
namespace CSVelte\IO;

use CSVelte\Traits\IsReadable;
use CSVelte\Traits\IsWritable;
use CSVelte\Traits\IsSeekable;

use CSVelte\Contract\Streamable;

use \SplFileObject;

use \Exception;
use \InvalidArgumentException;
use CSVelte\Exception\NotYetImplementedException;
use CSVelte\Exception\EndOfFileException;
use CSVelte\Exception\IOException;

/**
 * CSVelte Stream.
 *
 * Represents a stream for input/output. Implements both readable and writable
 * interfaces so that it can be passed to either ``CSVelte\Reader`` or
 * ``CSVelte\Writer``.
 *
 * @package    CSVelte
 * @subpackage CSVelte\IO
 * @copyright  (c) 2016, Luke Visinoni <luke.visinoni@gmail.com>
 * @author     Luke Visinoni <luke.visinoni@gmail.com>
 * @since      v0.2
 */
class Stream implements Streamable
{
    use IsReadable, IsWritable, IsSeekable;

    /**
     * @var \CSVelte\IO\Resource A stream resource object
     */
    protected $resource;

    /**
     * @var int The total size (in bytes) of the stream
     */
    protected $size;

    /**
     * Meta data about stream resource.
     * Just contains the return value of stream_get_meta_data.
     * @var array The return value of stream_get_meta_data
     * @todo Not sure if this belongs in this class or in Resource. I'm leaving
     *     it here for now, simply because I'm worried Stream will become superfluous
     */
    protected $meta;

    /**
     * Instantiate a stream.
     *
     * Instantiate a new stream object using a stream resource object.
     *
     * @param \CSVelte\IO\Resource A stream resource object
     */
    public function __construct(Resource $resource)
    {
        $this->setResource($resource);
    }

    /**
     * Set stream resource object
     *
     * @param \CSVelte\IO\Resource $resource A stream resource object
     */
    protected function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Stream Object Destructor.
     *
     * Closes stream connection.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Converts object/string to a usable stream
     *
     * Mercilessly stolen from:
     * https://github.com/guzzle/streams/blob/master/src/Stream.php
     *
     * My kudos and sincere thanks go out to Michael Dowling and Graham Campbell
     * of the guzzle/streams PHP package. Thanks for the inspiration (in some cases)
     * and the not suing me for outright theft (in this case).
     *
     * @param object|string|SplFileObject The string/object to convert to a stream
     * @param array Options to pass to the newly created stream
     * @return \CSVelte\IO\Stream
     * @throws \InvalidArgumentException
     * @todo Write an IO\AccessMode class like what I talked about in issue #114
     */
    public static function streamize($obj = '')
    {
        if ($obj instanceof SplFileObject) {
            return self::open($obj->getPathName());
        }

        $type = gettype($obj);

        if ($type == 'string') {
            $stream = self::open('php://temp', 'r+');
            if ($obj !== '') {
                $res = $stream->getResource();
                fwrite($res(), $obj);
                fseek($res(), 0);
            }
            return $stream;
        }

        if ($type == 'object' && method_exists($obj, '__toString')) {
            return self::streamize((string) $obj);
        }

        throw new InvalidArgumentException('Invalid resource type: ' . $type);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * Returns the internal pointer to the position it was in once it's finished
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     * @todo I'm leaning towards getting rid of the code that places the cursor
     *     back at the position it was in... I'm not sure it's expected behavior
     */
    public function __toString()
    {
        $string = '';
        try {
            $pos = (int) $this->tell();
            $this->rewind();
            $string .= $this->getContents();
            $this->seek($pos);
        } catch (Exception $e) {
            // eat any exception that may be thrown...
        }
        return $string;
    }

    /**
     *
     * @see http://php.net/manual/en/function.fopen.php
     * @see http://php.net/manual/en/function.stream-context-create.php
     */
    public static function open($uri, $mode = null, $context = null, $lazy = false)
    {
        $resource = (new Resource($uri, $mode))
            ->setContextResource($context);
        if (!$lazy) $resource->connect();
        return new self($resource);
    }

    /**
     * Close stream resource.
     *
     * @return boolean True on success or false on failure
     */
    public function close()
    {
        if ($this->resource) {
            return $this->resource->disconnect();
        }
    }

    /**
     * Get stream metadata (all or certain value).
     *
     * Get either the entire stream metadata array or a single value from it by key.
     *
     * @param string $key If set, must be one of ``stream_get_meta_data`` array keys
     * @return string|array Either a single value or whole array returned by ``stream_get_meta_data``
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     */
    public function getMetaData($key = null)
    {
        if ($this->resource) {
            if (is_null($this->meta)) {
                $this->meta = stream_get_meta_data($this->resource->getHandle());
            }
            // if a certain value was requested, return it
            // otherwise, return entire array
            if (is_null($key)) return $this->meta;
            return (array_key_exists($key, $this->meta)) ? $this->meta[$key] : null;
        }
    }

    /**
     * Accessor for seekability.
     *
     * Returns true if possible to seek to a certain position within this stream
     *
     * @return boolean True if stream is seekable
     */
    public function isSeekable()
    {
        if ($this->resource) {
            return $this->getMetaData('seekable');
        }
        return false;
    }

    /**
     * Accessor for readability.
     *
     * Returns true if possible to read from this stream
     *
     * @return boolean True if stream is readable
     */
    public function isReadable()
    {
        if ($this->resource) {
            return $this->resource->isReadable();
        }
        return false;
    }

    /**
     * Accessor for writability.
     *
     * Returns true if possible to write to this stream
     *
     * @return boolean True if stream is writable
     */
    public function isWritable()
    {
        if ($this->resource) {
            return $this->resource->isWritable();
        }
        return false;
    }

    /**
     * Accessor for internal stream resource.
     *
     * Returns the internal stream resource pointer
     *
     * @return resource The open stream resource pointer
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Accessor for stream URI.
     *
     * Returns the stream URI
     *
     * @return string The URI for the stream
     */
    public function getUri()
    {
        if ($this->resource) {
            return $this->resource->getUri();
        }
    }

    /**
     * Accessor for stream name.
     *
     * Alias for ``getUri()``
     *
     * @return string The name for this stream
     */
    public function getName()
    {
        return $this->getUri();
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        // @todo I need to get a better understanding of when and why a stream
        // would need to be detached to properly implement this
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if ($this->resource) {
            if (is_null($this->size)) {
                $stats = fstat($this->resource->getHandle());
                if (array_key_exists('size', $stats)) {
                    $this->size = $stats['size'];
                }
            }
            return $this->size;
        }
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        return $this->resource ? ftell($this->resource->getHandle()) : false;
    }

    /**
     * Read $length bytes from stream.
     *
     * Reads $length bytes (number of characters) from the stream
     *
     * @param int $length Number of bytes to read from stream
     * @return string|false The data read from stream or false if at end of
     *     file or some other problem.
     * @throws CSVelte\Exception\IOException if stream not readable
     */
    public function read($length)
    {
        $this->assertIsReadable();
        if ($this->eof()) return false;
        return fread($this->resource->getHandle(), $length);
    }

    /**
     * Returns the remaining contents in a string.
     *
     * Read and return the remaining contents of the stream, beginning from
     * wherever the stream's internal pointer is when this method is called. If
     * you want the ENTIRE stream's contents, use __toString() instead.
     *
     * @param void
     * @return string The remaining contents of the file, beginning at internal
     *     pointer's current location
     * @throws CSVelte\Exception\IOException
     */
    public function getContents()
    {
        $buffer = '';
        if ($this->isReadable()) {
            while ($chunk = $this->read(1024)) {
                $buffer .= $chunk;
            }
        }
        return $buffer;
    }

    /**
     * Is file pointer at the end of the stream?
     *
     * Returns true if internal pointer has reached the end of the stream.
     *
     * @return boolean True if end of stream has been reached
     */
    public function eof()
    {
        return !$this->resource || feof($this->resource->getHandle());
    }

    /**
     * Rewind pointer to beginning of stream.
     *
     * Rewinds the stream, meaning it returns the pointer to the beginning of the
     * stream as if it had just been initialized.
     */
    public function rewind()
    {
        if ($this->resource) {
            rewind($this->resource->getHandle());
        }
    }

    /**
     * Write to stream
     *
     * Writes a string to the stream (if it is writable)
     *
     * @param string $str The data to be written to the stream
     * @return int The number of bytes written to the stream
     * @throws CSVelte\Exception\IOException
     */
    public function write($str)
    {
        $this->assertIsWritable();
        return fwrite($this->resource->getHandle(), $str);
    }

    /**
     * Seek to position.
     *
     * Seek to a specific position within the stream (if seekable).
     *
     * @param int $offset The position to seek to
     * @param int $whence One of three native php ``SEEK_`` constants
     * @return boolean True on success false on failure
     * @throws CSVelte\Exception\IOException
     * @see http://php.net/manual/en/function.seek.php
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->assertIsSeekable();
        return fseek($this->resource->getHandle(), $offset, $whence) === 0;
    }

}
