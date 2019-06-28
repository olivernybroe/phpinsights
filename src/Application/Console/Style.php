<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class Style extends SymfonyStyle
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    public $output;

    /**
     * Style constructor.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($this->input = $input, $this->output = $output);
    }

    /**
     * Waits for Enter key.
     *
     * @param string $category
     *
     * @return \NunoMaduro\PhpInsights\Application\Console\Style
     */
    public function waitForKey(string $category): Style
    {
        $stdin = fopen('php://stdin', 'r');

        if ($stdin !== false && $this->output instanceof ConsoleOutput && $this->input->isInteractive() === true) {
            $this->newLine();
            $section = $this->output->section();
            $section->writeln(sprintf('<title>Press enter to see %s issues...</title>', strtolower($category)));
            fgetc($stdin);
            $section->clear(3);
        }

        return $this;
    }

    public static function loadingBar1(ProgressBar $bar, OutputInterface $output)
    {
        $green = "82";
        $yellow = "226";
        $red = "1";

        $width = $bar->getBarWidth();
        $charWidth = floor($width / 3);

        // Create an array with chars for the color
        $charArray = [];
        for ($i = 0; $i < $charWidth*3; $i++) {
            if ($i < $charWidth) {
                // green
                $charArray[$i] = "\033[48;5;{$green}m \033[0m";
            } elseif ($i < $charWidth * 2) {
                // yellow
                $charArray[$i] = "\033[48;5;{$yellow}m \033[0m";
            } else {
                // red
                $charArray[$i] = "\033[48;5;{$red}m \033[0m";
            }
        }

        // Split the array
        $pointer = $bar->getProgress() % $width;

        for ($i = 0; $i < $pointer; $i++) {
            array_unshift($charArray, array_pop($charArray));
        }

        return implode("", $charArray);
    }

    public static $pointer = 0;

    public static function loadingBar(ProgressBar $bar, OutputInterface $output)
    {
        $green = "82";
        $yellow = "226";
        $red = "1";
        $square = '    ';
        $loading = [
            [
                "\033[48;5;{$green}m{$square}\033[0m", // green
                "\033[48;5;{$yellow}m{$square}\033[0m", // yellow
                "\033[48;5;{$red}m{$square}\033[0m", // red
            ],
            [
                "\033[48;5;{$red}m{$square}\033[0m", // red
                "\033[48;5;{$green}m{$square}\033[0m", // green
                "\033[48;5;{$yellow}m{$square}\033[0m", // yellow
            ],
            [
                "\033[48;5;{$yellow}m{$square}\033[0m", // yellow
                "\033[48;5;{$red}m{$square}\033[0m", // red
                "\033[48;5;{$green}m{$square}\033[0m", // green
            ],
        ];


        if ($bar->getProgress() % 6 === 0) {
            ++self::$pointer;
        }

        if (self::$pointer > 2) {
            self::$pointer = 0;
        }

        return implode(' ', $loading[self::$pointer]);
    }
}
