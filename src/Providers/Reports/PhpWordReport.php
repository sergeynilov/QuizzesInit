<?php


namespace sergeynilov\QuizzesInit\Providers\Reports;


use sergeynilov\QuizzesInit\Enums\WordTextLineEnum;
use sergeynilov\QuizzesInit\Library\Facades\QuizzesInitFacade;
use sergeynilov\QuizzesInit\Models\UserQuizzesHistoryDetail;
use Str;

class PhpWordReport
{

    protected string $reportFontName = 'Tahoma';
    protected int $reportFontSize = 12;
    protected string $outputFileFormat = 'Word2007';
    protected ?\PhpOffice\PhpWord\PhpWord $phpWord = null;

    public function setReportFontName(string $value): void
    {
        $this->reportFontName = $value;
    }

    public function setReportFontSize(int $value): void
    {
        $this->reportFontSize = $value;
    }

    public function setOutputFileFormat(string $value): void
    {
        $this->outputFileFormat = $value;
    }

    protected function initPhpWord()
    {
        $this->phpWord = new \PhpOffice\PhpWord\PhpWord();
        $this->phpWord->setDefaultFontName($this->reportFontName);
        $this->phpWord->setDefaultFontSize($this->reportFontSize);
    }

    protected function addListing(array $itemsListing)
    {
        $section = $this->phpWord->addSection(['breakType' => 'continuous']);
        foreach ($itemsListing as $item) {
            $listItemRun = $section->addListItemRun();
            $listItemRun->addText(htmlspecialchars($item, ENT_COMPAT, 'UTF-8'));
        }
    }
    protected function addHorizontalLine(bool $addTextBreak = false)
    {
        $section = $this->phpWord->addSection(['breakType' => 'continuous']);
        $section->addText('', [], ['borderBottomSize' => 4]);
        if($addTextBreak) {
            $section->addTextBreak();
        }
    }

    protected function addImageIcon(string $url, string $title, string $wordTextLineEnum, bool $addTextBreak = false)
    {
        $section = $this->phpWord->addSection(['breakType' => 'continuous']);
        $section->addText($title . ": ");
        $section->addImageIcon($url, ['width' => 16, 'height' => 16, 'align'=>'center', 'marginTop' => 5, 'wrappingStyle'=>'behind']);
    }

    protected function addDoubledFormattedTextLine(string $leftText, string $rightText, string $wordTextLineEnum, bool $addTextBreak = false)
    {
        $section = $this->phpWord->addSection(['breakType' => 'continuous']);
        $fontStyle = $this->setFontStyleByWordTextLineEnum($wordTextLineEnum);

        $sectionStyle = $section->getStyle();
        $position =
            $sectionStyle->getPageSizeW()
            - $sectionStyle->getMarginRight()
            - $sectionStyle->getMarginLeft();

        $this->phpWord->addParagraphStyle("leftRight", array("tabs" => array(
            new \PhpOffice\PhpWord\Style\Tab("right", $position)
        )));
        $section->addText($leftText . "\t" . $rightText, array(), "leftRight");
//        $textElement->setFontStyle($fontStyle);
    }

    protected function addTextLine(string $text, string $wordTextLineEnum, bool $addTextBreak = false)
    {
        $sectionMargins = ['marginLeft' => 50, 'marginRight' => 50, 'borderSize' => 0, 'border' => 0, 'borderStyle' => 'none',
                           'marginTop' => 50, 'marginBottom' => 50, 'breakType'=> 'continuous'];
        $fontStyle = $this->setFontStyleByWordTextLineEnum($wordTextLineEnum);
        $section = $this->phpWord->addSection($sectionMargins);
        $textElement = $section->addText($text);
        $textElement->setFontStyle($fontStyle);
        if($addTextBreak) {
            $section->addTextBreak();
        }
    }

    protected function setFontStyleByWordTextLineEnum(string $wordTextLineEnum): \PhpOffice\PhpWord\Style\Font
    {
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        if( WordTextLineEnum::WTL_HEADER_TEXT === $wordTextLineEnum) {
            $fontStyle->setBold(true);
            $fontStyle->setSize(18);
        }
        if( WordTextLineEnum::WTL_SUBHEADER_TEXT === $wordTextLineEnum) {
            $fontStyle->setBold(true);
            $fontStyle->setSize(14);
        }
        if( WordTextLineEnum::WTL_CONTENT_TEXT === $wordTextLineEnum) {
            $fontStyle->setBold(false);
            $fontStyle->setSize(13);
        }
        return $fontStyle;
    }

    protected function addTableWithData(array $dataArray, array $columnsArray, string $titleWordTextLineEnum): void
    {
//        \Log::info(QuizzesInitFacade::varDump($dataArray, ' -1 addTableWithData $dataArray::'));
        $section = $this->phpWord->addSection(['marginTop' => 50, 'marginLeft' => 50, 'marginRight' => 50,
                                               'marginBottom' => 50, 'breakType'=> 'continuous']);
        $fontStyle = $this->setFontStyleByWordTextLineEnum($titleWordTextLineEnum);
        $textElement = $section->addText('Answers');
        $textElement->setFontStyle($fontStyle);

        $rows = count($dataArray);
        $cols = count($columnsArray);

        $table = $section->addTable($this->getTableStyle());
        $table->addRow();
        for ($col = 0; $col < $cols; $col++) {
            $table->addCell($columnsArray[$col]['width'])->addText( Str::headline($columnsArray[$col]['title']), $this->getTableHeaderLineStyle(), array('align' => 'center') );
        }

        for ($row = 0; $row < $rows; $row++) {
            $table->addRow();
            for ($col = 0; $col < $cols; $col++) {
                $cellValue = '';
                if(isset($dataArray[$row][$columnsArray[$col]['column_id']])) {
                    $columnId = $columnsArray[$col]['column_id'];
                    $val = $dataArray[$row][$columnId] ?? '';
                    if( $columnId === 'is_correct') {
                        $val = UserQuizzesHistoryDetail::getIsCorrectLabel($val);
                    }
                    $cellValue = $this->getTableCellValue($val, $columnId, $row, $col);
                }
                $cellAlign = 'left';
                if(!empty($columnsArray[$col]['align'])) {
                    $cellAlign = $columnsArray[$col]['align'];
                }
                $table->addCell($columnsArray[$col]['width'])->addText( $cellValue, $this->getTableContentRowLineStyle(), array('align' => $cellAlign)) ;
            }
        }
    }

    protected function getTableCellValue( $value, string $columnId, int $row, int $col): string {
        return (string)$value;
    }

    protected function getTableHeaderLineStyle(): array {
        return [
            'borderSize' => 2,
            'bold' => true
        ];
    }

    protected function getTableContentRowLineStyle(): array {
        return [
            'bold' => false
        ];
    }

    protected function getTableStyle(): array {
        return [
            'borderSize'  => 2,
            'borderColor' => '404040',
            'cellMargin'  => 50,
            'cellSpacing' => 7,
            'alignment'   => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'layout'      => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
        ];
    }

}
