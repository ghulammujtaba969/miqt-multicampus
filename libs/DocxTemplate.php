<?php
/**
 * Simple DOCX template replacer.
 * Replaces placeholders like {{NAME}} in word/document.xml.
 */
class DocxTemplate {
    private $templatePath;
    private $zip;
    private $documentXml;

    public function __construct($templatePath) {
        if (!file_exists($templatePath)) {
            throw new InvalidArgumentException('Template not found: '.$templatePath);
        }
        $this->templatePath = $templatePath;
        $this->zip = new ZipArchive();
        if ($this->zip->open($this->templatePath) !== true) {
            throw new RuntimeException('Unable to open DOCX');
        }
        $this->documentXml = $this->zip->getFromName('word/document.xml');
        if ($this->documentXml === false) {
            throw new RuntimeException('Invalid DOCX: missing word/document.xml');
        }
    }

    public function replace(array $map) {
        $xml = $this->documentXml;
        foreach ($map as $key => $val) {
            $needle = '{{'.strtoupper($key).'}}';
            $xml = str_replace($needle, htmlspecialchars($val), $xml);
        }
        $this->documentXml = $xml;
    }

    public function replaceRaw($placeholder, $rawXml) {
        $this->documentXml = str_replace($placeholder, $rawXml, $this->documentXml);
    }

    public function output($filename) {
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'docx_' . uniqid() . '.docx';
        $out = new ZipArchive();
        if ($out->open($tmp, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Unable to create DOCX');
        }
        // Copy all entries, but override document.xml
        for ($i=0; $i<$this->zip->numFiles; $i++) {
            $stat = $this->zip->statIndex($i);
            $name = $stat['name'];
            if ($name === 'word/document.xml') {
                $out->addFromString($name, $this->documentXml);
            } else {
                $out->addFromString($name, $this->zip->getFromIndex($i));
            }
        }
        $out->close();
        $this->zip->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Content-Length: '.filesize($tmp));
        readfile($tmp);
        unlink($tmp);
        exit;
    }
}

