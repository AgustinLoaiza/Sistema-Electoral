<?php

namespace App\Support;

class PdfReport
{
    public static function make(string $title, ?string $subtitle, array $headers, iterable $rows): string
    {
        $lines = [];
        $lines[] = $title;

        if ($subtitle) {
            $lines[] = $subtitle;
        }

        $lines[] = 'Generado: ' . now()->format('Y-m-d H:i');
        $lines[] = str_repeat('-', 120);
        $lines[] = implode(' | ', $headers);
        $lines[] = str_repeat('-', 120);

        $index = 1;

        foreach ($rows as $row) {
            $values = [];

            foreach ($headers as $position => $header) {
                $values[] = $header . ': ' . ($row[$position] ?? '');
            }

            $lines[] = $index . '. ' . implode(' | ', $values);
            $index++;
        }

        if ($index === 1) {
            $lines[] = 'Sin registros para exportar.';
        }

        return self::render($lines);
    }

    private static function render(array $sourceLines): string
    {
        $pageWidth = 842;
        $pageHeight = 595;
        $marginX = 36;
        $startY = 548;
        $lineHeight = 14;
        $maxChars = 128;
        $linesPerPage = 36;

        $wrappedLines = [];

        foreach ($sourceLines as $line) {
            $wrapped = self::wrap((string) $line, $maxChars);

            foreach ($wrapped as $wrappedLine) {
                $wrappedLines[] = $wrappedLine;
            }
        }

        $pages = array_chunk($wrappedLines, $linesPerPage);
        $objects = [];

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        $kids = [];

        foreach ($pages as $pageIndex => $pageLines) {
            $pageObject = 4 + ($pageIndex * 2);
            $contentObject = $pageObject + 1;
            $kids[] = $pageObject . ' 0 R';

            $content = '';
            $y = $startY;

            foreach ($pageLines as $lineIndex => $line) {
                $fontSize = $pageIndex === 0 && $lineIndex === 0 ? 16 : 9;
                $content .= 'BT /F1 ' . $fontSize . ' Tf ' . $marginX . ' ' . $y . ' Td (' . self::escape($line) . ") Tj ET\n";
                $y -= $lineHeight;
            }

            $objects[$pageObject] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $pageWidth . ' ' . $pageHeight . '] /Resources << /Font << /F1 3 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
            $objects[$contentObject] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream";
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        foreach (array_keys($objects) as $number) {
            $pdf .= str_pad((string) $offsets[$number], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private static function wrap(string $text, int $limit): array
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');

        if ($text === '') {
            return [''];
        }

        $lines = [];

        while (strlen($text) > $limit) {
            $chunk = substr($text, 0, $limit);
            $breakAt = strrpos($chunk, ' ');

            if ($breakAt === false || $breakAt < 40) {
                $breakAt = $limit;
            }

            $lines[] = trim(substr($text, 0, $breakAt));
            $text = trim(substr($text, $breakAt));
        }

        $lines[] = $text;

        return $lines;
    }

    private static function escape(string $text): string
    {
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $text);
            $text = $converted !== false ? $converted : $text;
        }

        $text = preg_replace('/[^\x09\x0A\x0D\x20-\xFF]/', '', $text) ?? '';

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
