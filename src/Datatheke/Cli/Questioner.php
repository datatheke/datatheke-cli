<?php

namespace Datatheke\Cli;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Question\Question;

class Questioner
{
    protected $input;
    protected $output;
    protected $helperSet;

    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }

    public function ask($label, $default = null, $hidden = null, $hiddenFallback = null)
    {
        $helper = $this->helperSet->get('question');

        if (null === $default) {
            $question = new Question(sprintf('<question>%s</question>: ', $label));
        } else {
            $question = new Question(sprintf('<question>%s</question> (<comment>%s</comment>): ', $label, $default), $default);
        }

        if (null !== $hidden) {
            $question->setHidden($hidden);
        }

        if (null !== $hiddenFallback) {
            $question->setHiddenFallback($hiddenFallback);
        }

        return $helper->ask($this->input, $this->output, $question);
    }
}
