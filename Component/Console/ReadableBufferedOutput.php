<?php

namespace Borovets\ChainCommandBundle\Component\Console;

use Symfony\Component\Console\Output\Output;

/**
 *
 * Implementation of the buffer with the ability to read
 */
class ReadableBufferedOutput extends Output
{
    /**
     * @var string
     */
    private $buffer = '';

    /**
     * Return buffer content
     *
     * @return string
     */
    public function get()
    {
        return $this->buffer;
    }

    /**
     * Method backward compatibility with Symfony\Component\Console\Output\BufferOutput
     * @deprecated see get() and flush()
     *
     * @return string
     */
    public function fetch()
    {
        $this->flush();
    }


    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function flush()
    {
        $content = $this->buffer;
        $this->clean();

        return $content;
    }

    /**
     * Clean buffer
     */
    public function clean()
    {
        $this->buffer = '';
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message;

        if ($newline) {
            $this->buffer .= "\n";
        }
    }
}