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
 * @package    php2odp
 * @author     Arne Blankerts <arne@blankerts.de>
 * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
 * @license    BSD License
 *
 */
namespace TheSeer\php2odp {

    use \TheSeer\fDom\fDomDocument;

    class Application {

        protected $map = array(
            'color: #000000' => 'php1',
            'color: #0000BB' => 'php2',
            'color: #FF8000' => 'php3',
            'color: #007700' => 'php4',
            'color: #DD0000' => 'php5'
        );

        protected function getSourceAsDom($src) {
            $xml = new \TheSeer\fDom\fDOMDocument();
            $xml->loadHTML(highlight_file($src, true));

            $src = new \XMLReader();
            $src->xml($xml->saveXML());
            return $src;
        }

        protected function buildTextBox($srcDom) {
            $odp = new \XMLWriter();
            $odp->openMemory('1.0', 'UTF-8');
            $odp->startElement('draw:text-box');
            $odp->writeAttribute('xmlns:draw', "urn:oasis:names:tc:opendocument:xmlns:drawing:1.0");
            $odp->writeAttribute('xmlns:style', "urn:oasis:names:tc:opendocument:xmlns:style:1.0");
            $odp->writeAttribute('xmlns:text', "urn:oasis:names:tc:opendocument:xmlns:text:1.0");
            $odp->startElement('text:p');
            $start = false;
            $current = '';
            $text = '';

            while($srcDom->read()) {
                if (!$start) {
                    $start = ($srcDom->localName == 'code');
                    continue;
                }
                switch($srcDom->nodeType) {
                    case \XMLReader::TEXT: {
                        $odp->startElement('text:span');
                        $odp->writeAttribute('text:style-name', $current);
                        $odp->text(trim($srcDom->value));
                        $odp->endElement();
                        continue;
                    }
                    case \XMLReader::ELEMENT: {
                        switch($srcDom->localName) {
                            case 'br': {
                                $odp->endElement();
                                $odp->startElement('text:p');
                                break;
                            }
                            case 'span': {
                                $current = $this->map[$srcDom->getAttribute('style')];
                                break;
                            }
                        }
                    }
                }
            }

            $odp->endDocument();
            return $odp->outputMemory();
        }

        protected function mergeTextBox($xml) {
            $content = new \TheSeer\fDom\fDOMDocument();
            $content->load(__DIR__ . '/odp/content.xml');
            $content->registerNamespace('draw', "urn:oasis:names:tc:opendocument:xmlns:drawing:1.0");
            $page = $content->query('//draw:frame')->item(0);
            foreach($page->query('draw:text-box') as $box) {
                $page->removeChild($box);
            }
            $page->appendXML($xml);
            return $content->saveXML();
        }

        protected function buildDocument($target, $content) {
            copy(__DIR__ . '/odp/template.odp', $target);
            $zip = new \ZipArchive;
            $zip->open($target);
            $zip->addFromString('content.xml', $content);
            $zip->close();
        }

        public function execute($srcFile, $destFile) {
            if (!file_exists($srcFile)) {
                throw new ApplicationException("'$srcFile' does not exist", ApplicationException::SourceNotFound);
            }
            if (file_exists($destFile)) {
                throw new ApplicationException("'$destFile' does already exist", ApplicationException::DestinationExists);
            }
            $srcDom = $this->getSourceAsDom($srcFile);
            $textBox = $this->buildTextBox($srcDom);
            $content = $this->mergeTextBox($textBox);
            $this->buildDocument($destFile, $content);
        }
    }

    class ApplicationException extends \Exception {

        const SourceNotFound = 1;
        const DestinationExists = 2;
    }
}