<?php
/**
 * Copyright (c) 2011 Arne Blankerts <arne@blankerts.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Arne Blankerts nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Exit codes:
 *   0 - No error
 *   1 - Execution Error
 *
 * @package    php2odp
 * @author     Arne Blankerts <arne@blankerts.de>
 * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
 * @license    BSD License
 *
 */
namespace TheSeer\php2odp {

    use \TheSeer\Tools\PHPFilterIterator;
    use \TheSeer\fDom\fDomDocument;

    class CLI {

        /**
         * Version identifier
         *
         * @var string
         */
        const VERSION = "%version%";

        /**
         * Main executor for CLI process.
         */
        public function run() {
            try {
                $input = new \ezcConsoleInput();
                $this->registerOptions($input);
                $input->process();

                if ($input->getOption('help')->value === true) {
                    $this->showVersion();
                    $this->showUsage();
                    exit(0);
                }

                if ($input->getOption('version')->value === true) {
                    $this->showVersion();
                    exit(0);
                }

                $args = $input->getArguments();

                $app = new Application();
                $app->execute($args[0], $args[1]);

                echo $args[1] . " created.\n\n";

            } catch (\ezcConsoleException $e) {
                $this->showVersion();
                fwrite(STDERR, $e->getMessage()."\n\n");
                $this->showUsage();
                exit(3);
            } catch (ApplicationException $e) {
                $this->showVersion();
                fwrite(STDERR, "Error while processing request:\n");
                fwrite(STDERR, $e->getMessage()."\n");
                exit(3);
            } catch (\Exception $e) {
                $this->showVersion();
                fwrite(STDERR, "Error while processing request:\n");
                fwrite(STDERR, ' - ' . $e."\n");
                exit(1);
            }
        }

        /**
         * Helper to output version information.
         */
        protected function showVersion() {
            printf("php2odp %s - Copyright (C) 2011 by Arne Blankerts\n\n", self::VERSION);
        }

        /**
         * Helper to register supported CLI options into ezcConsoleInput
         *
         * @param \ezcConsoleInput $input ezcConsoleInput instance to register options in to
         */
        protected function registerOptions(\ezcConsoleInput $input) {
            $versionOption = $input->registerOption( new \ezcConsoleOption( 'v', 'version' ) );
            $versionOption->shorthelp    = 'Prints the version and exits';
            $versionOption->isHelpOption = true;

            $helpOption = $input->registerOption( new \ezcConsoleOption( 'h', 'help' ) );
            $helpOption->isHelpOption = true;
            $helpOption->shorthelp    = 'Prints this usage information';

            $input->argumentDefinition = new \ezcConsoleArguments();
            $input->argumentDefinition[0] = new \ezcConsoleArgument( "source" );
            $input->argumentDefinition[0]->shorthelp = "The PHP source to process.";

            $input->argumentDefinition[1] = new \ezcConsoleArgument( "target" );
            $input->argumentDefinition[1]->shorthelp = "The filename for the OpenDocument Presentation file";

        }

        /**
         * Helper to output usage information.
         */
        protected function showUsage() {
            print <<<EOF
Usage: php2odp [switches] <source.php> <target.odp>

  -h, --help       Prints this usage information
  -v, --version    Prints the version and exits


EOF;
        }

    }

}