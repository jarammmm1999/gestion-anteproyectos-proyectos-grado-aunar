<?php
// Librería liviana para generar Excel con varias hojas correctamente (versión corregida)

class SimpleXLSXGen {
    private $sheets = [];

    public static function make() {
        return new self();
    }

    public function addSheet(array $rows, string $name = 'Sheet1') {
        $this->sheets[] = ['name' => $name, 'rows' => $rows];
        return $this;
    }

    public function downloadAs($filename = 'informe_completo.xlsx') {
        $this->saveAs($filename);
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Cache-Control: max-age=0");
        readfile($filename);
        unlink($filename);
    }
    

    public function saveAs($filename) {
        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Plantillas de estructura
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

        $core = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                   xmlns:dc="http://purl.org/dc/elements/1.1/"
                   xmlns:dcterms="http://purl.org/dc/terms/"
                   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>SimpleXLSXGen</dc:creator>
</cp:coreProperties>';

        $app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
            xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>SimpleXLSXGen</Application>
</Properties>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"/>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>';
        $sheet_rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        // Agregar hojas
        $sheet_files = '';
        foreach ($this->sheets as $i => $sheet) {
            $sheet_id = $i + 1;
            $sheet_name = $sheet['name'];
            $rows = $sheet['rows'];
            $sheet_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetData>';
            foreach ($rows as $rowNum => $row) {
                $sheet_xml .= "<row r=\"" . ($rowNum + 1) . "\">";
                foreach ($row as $col) {
                    $val = htmlspecialchars($col);
                    $sheet_xml .= "<c t=\"inlineStr\"><is><t>{$val}</t></is></c>";
                }
                $sheet_xml .= "</row>";
            }
            $sheet_xml .= '</sheetData></worksheet>';
            $zip->addFromString("xl/worksheets/sheet{$sheet_id}.xml", $sheet_xml);
            $workbook .= "<sheet name=\"{$sheet_name}\" sheetId=\"{$sheet_id}\" r:id=\"rId{$sheet_id}\"/>";
            $sheet_rels .= "<Relationship Id=\"rId{$sheet_id}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet\" Target=\"worksheets/sheet{$sheet_id}.xml\"/>";
            $contentTypes .= "<Override PartName=\"/xl/worksheets/sheet{$sheet_id}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
        }

        $workbook .= '</sheets></workbook>';
        $sheet_rels .= '</Relationships>';
        $contentTypes .= '</Types>';

        // Archivos en el ZIP
        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('docProps/core.xml', $core);
        $zip->addFromString('docProps/app.xml', $app);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $sheet_rels);

        $zip->close();
    }
}
?>